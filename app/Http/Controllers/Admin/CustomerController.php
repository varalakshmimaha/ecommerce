<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $query = Customer::with(['orders' => function($query) {
            $query->latest()->take(5);
        }]);

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
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
            'email' => 'required|email|unique:customers,email',
            'mobile' => 'required|string|max:20|unique:customers,mobile',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $customer = Customer::create($validated);

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer created successfully!');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        $customer->load([
            'orders' => function($query) {
                $query->with(['items.product'])->latest();
            },
            'queries' => function($query) {
                $query->latest();
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
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')->ignore($customer->id),
            ],
            'mobile' => [
                'required',
                'string',
                'max:20',
                Rule::unique('customers', 'mobile')->ignore($customer->id),
            ],
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $customer->update($validated);

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
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
    public function toggleStatus(Customer $customer)
    {
        $customer->update([
            'is_active' => !$customer->is_active
        ]);

        $status = $customer->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', "Customer {$status} successfully!");
    }

    /**
     * Export customers to CSV.
     */
    public function export(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
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
                fputcsv($file, [
                    $customer->id,
                    $customer->name,
                    $customer->email,
                    $customer->mobile,
                    $customer->address,
                    $customer->city,
                    $customer->state,
                    $customer->postal_code,
                    $customer->country,
                    $customer->total_orders,
                    $customer->formatted_total_spent,
                    $customer->formatted_last_order,
                    $customer->is_active ? 'Active' : 'Inactive',
                    $customer->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
