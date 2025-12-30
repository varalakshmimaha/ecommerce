<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers (non-admin users).
     */
    public function index(Request $request)
    {
        $query = User::where('is_admin', false)
            ->with(['orders' => function($query) {
                $query->latest()->take(5);
            }, 'addresses']);

        // Search functionality
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $search = $request->search;
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_verified', $request->status === 'active');
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|string|max:20|unique:users,mobile',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'is_verified' => 'boolean',
        ]);

        // Create user as customer (non-admin)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'password' => bcrypt($validated['password']),
            'is_admin' => false,
            'is_verified' => $validated['is_verified'] ?? true,
        ]);

        // Create address if provided
        if (!empty($validated['address']) || !empty($validated['city']) || !empty($validated['state'])) {
            $user->addresses()->create([
                'address' => $validated['address'] ?? '',
                'city' => $validated['city'] ?? '',
                'state' => $validated['state'] ?? '',
                'postal_code' => $validated['postal_code'] ?? '',
                'country' => $validated['country'] ?? 'India',
                'is_default' => true,
            ]);
        }

        return redirect()
            ->route('admin.customers.show', $user)
            ->with('success', 'Customer created successfully!');
    }

    /**
     * Display the specified customer.
     */
    public function show(User $customer)
    {
        // Ensure we're only showing non-admin users
        if ($customer->is_admin) {
            abort(403, 'Cannot view admin users as customers');
        }

        $customer->load([
            'orders' => function($query) {
                $query->with(['items.product'])->latest();
            },
            'queries' => function($query) {
                $query->latest();
            },
            'addresses' => function($query) {
                $query->orderBy('is_default', 'desc');
            }
        ]);

        // Calculate additional statistics
        $statistics = [
            'total_orders' => $customer->orders()->count(),
            'total_spent' => $customer->orders()->sum('total_amount'),
            'average_order_value' => $customer->orders()->avg('total_amount'),
            'total_queries' => $customer->queries()->count(),
            'pending_queries' => $customer->queries()->where('status', 'pending')->count(),
            'completed_orders' => $customer->orders()->where('order_status', 'delivered')->count(),
            'pending_orders' => $customer->orders()->whereIn('order_status', ['pending', 'confirmed', 'shipped'])->count(),
        ];

        return view('admin.customers.show', compact('customer', 'statistics'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(User $customer)
    {
        // Ensure we're only editing non-admin users
        if ($customer->is_admin) {
            abort(403, 'Cannot edit admin users as customers');
        }

        $customer->load('addresses');
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, User $customer)
    {
        // Ensure we're only updating non-admin users
        if ($customer->is_admin) {
            abort(403, 'Cannot update admin users as customers');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($customer->id),
            ],
            'mobile' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'mobile')->ignore($customer->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'is_verified' => 'boolean',
        ]);

        // Update user information
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'is_verified' => $validated['is_verified'] ?? $customer->is_verified,
        ];

        // Update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = bcrypt($validated['password']);
        }

        $customer->update($updateData);

        // Update or create address
        $defaultAddress = $customer->addresses()->where('is_default', true)->first();
        $addressData = [
            'address' => $validated['address'] ?? '',
            'city' => $validated['city'] ?? '',
            'state' => $validated['state'] ?? '',
            'postal_code' => $validated['postal_code'] ?? '',
            'country' => $validated['country'] ?? 'India',
        ];

        if ($defaultAddress) {
            $defaultAddress->update($addressData);
        } elseif (!empty($validated['address']) || !empty($validated['city']) || !empty($validated['state'])) {
            $customer->addresses()->create(array_merge($addressData, ['is_default' => true]));
        }

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(User $customer)
    {
        // Ensure we're only deleting non-admin users
        if ($customer->is_admin) {
            abort(403, 'Cannot delete admin users');
        }

        // Check if customer has orders
        if ($customer->orders()->exists()) {
            return redirect()
                ->route('admin.customers.index')
                ->with('error', 'Cannot delete customer with existing orders. Please deactivate the customer instead.');
        }

        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully!');
    }

    /**
     * Toggle customer status (activate/deactivate).
     */
    public function toggleStatus(User $customer)
    {
        // Ensure we're only toggling non-admin users
        if ($customer->is_admin) {
            abort(403, 'Cannot toggle admin user status');
        }

        $customer->update([
            'is_verified' => !$customer->is_verified
        ]);

        $status = $customer->is_verified ? 'verified' : 'unverified';

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', "Customer {$status} successfully!");
    }

    /**
     * Export customers to CSV.
     */
    public function export(Request $request)
    {
        $query = User::where('is_admin', false);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_verified', $request->status === 'active');
        }

        $customers = $query->get();

        $filename = 'customers_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'ID',
                'Name',
                'Email',
                'Mobile',
                'Address',
                'City',
                'State',
                'Postal Code',
                'Country',
                'Total Orders',
                'Total Spent',
                'Last Order',
                'Status',
                'Created At'
            ]);

            // CSV Data
            foreach ($customers as $customer) {
                $address = $customer->addresses()->where('is_default', true)->first();
                $totalOrders = $customer->orders()->count();
                $totalSpent = $customer->orders()->sum('total_amount');
                $lastOrder = $customer->orders()->max('created_at');

                fputcsv($file, [
                    $customer->id,
                    $customer->name,
                    $customer->email,
                    $customer->mobile,
                    $address ? $address->address : '',
                    $address ? $address->city : '',
                    $address ? $address->state : '',
                    $address ? $address->postal_code : '',
                    $address ? $address->country : '',
                    $totalOrders,
                    '₹' . number_format($totalSpent, 2),
                    $lastOrder ? $lastOrder->format('M d, Y') : 'No orders',
                    $customer->is_verified ? 'Verified' : 'Unverified',
                    $customer->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
