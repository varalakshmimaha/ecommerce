<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Query;
use Illuminate\Http\Request;

class QueryController extends Controller
{
    public function index()
    {
        $queries = Query::with('user')
            ->orderByRaw("FIELD(status, 'open', 'in_progress', 'resolved', 'closed')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.queries.index', compact('queries'));
    }

    public function respond(Request $request, Query $query)
    {
        $request->validate([
            'response' => 'required|string',
        ]);

        $query->update([
            'admin_response' => $request->response,
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Response sent successfully');
    }

    public function updateStatus(Request $request, Query $query)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $query->update(['status' => $request->status]);

        if ($request->status === 'resolved' && !$query->resolved_at) {
            $query->update(['resolved_at' => now()]);
        }

        return redirect()->back()->with('success', 'Status updated successfully');
    }
}
