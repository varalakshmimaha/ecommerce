@extends('layouts.admin')

@section('title', 'Customer Queries')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Customer Queries</h1>
        <div class="text-sm text-gray-600">
            Total Queries: <span class="font-semibold">{{ $queries->total() }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if($queries->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500">No customer queries found.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($queries as $query)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold mb-1">{{ $query->subject }}</h3>
                                <div class="flex items-center gap-4 text-sm text-gray-600">
                                    <span>From: <strong>{{ $query->user->name }}</strong> ({{ $query->user->email }})</span>
                                    @if($query->order_number)
                                        <span>Order: <strong>{{ $query->order_number }}</strong></span>
                                    @endif
                                    <span>{{ $query->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <form action="{{ route('admin.queries.update-status', $query) }}" method="POST" class="inline-block">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="px-3 py-1 rounded-full text-sm font-medium border-0 {{
                                        $query->status === 'open' ? 'bg-yellow-100 text-yellow-800' :
                                        ($query->status === 'in_progress' ? 'bg-blue-100 text-blue-800' :
                                        ($query->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))
                                    }}">
                                        <option value="open" {{ $query->status === 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="in_progress" {{ $query->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="resolved" {{ $query->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        <option value="closed" {{ $query->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-700">{{ $query->message }}</p>
                        </div>

                        @if($query->admin_response)
                            <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                                <p class="text-sm font-semibold text-blue-900 mb-1">Your Response:</p>
                                <p class="text-blue-800">{{ $query->admin_response }}</p>
                                @if($query->resolved_at)
                                    <p class="text-xs text-brand-gold mt-2">Responded on {{ $query->resolved_at->format('M d, Y h:i A') }}</p>
                                @endif
                            </div>
                        @endif

                        <div class="border-t pt-4">
                            <button onclick="toggleResponseForm({{ $query->id }})" class="px-4 py-2 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white rounded-lg hover:bg-blue-700 transition">
                                {{ $query->admin_response ? 'Update Response' : 'Respond to Query' }}
                            </button>
                        </div>

                        <div id="response-form-{{ $query->id }}" class="hidden mt-4 border-t pt-4">
                            <form action="{{ route('admin.queries.respond', $query) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Response</label>
                                    <textarea name="response" rows="4" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300" placeholder="Type your response here...">{{ $query->admin_response }}</textarea>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white rounded-lg font-semibold hover:shadow-lg transition-all duration-300">
                                        Send Response
                                    </button>
                                    <button type="button" onclick="toggleResponseForm({{ $query->id }})" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-300">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $queries->links() }}
        </div>
    @endif
</div>

<script>
function toggleResponseForm(queryId) {
    const form = document.getElementById('response-form-' + queryId);
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
    } else {
        form.classList.add('hidden');
    }
}
</script>
@endsection
