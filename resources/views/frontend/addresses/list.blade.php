@foreach($addresses as $address)
<div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mb-4 flex flex-col md:flex-row md:items-center justify-between gap-4 animate-fade-in">
    <div>
        <div class="flex items-center gap-2 mb-2">
            <span class="inline-block w-2 h-6 bg-gradient-to-b from-[#D4AF37] to-[#B8962E] rounded"></span>
            <span class="font-bold text-lg text-[#1A1A1A]">{{ $address->name }}</span>
            @if($address->is_default)
                <span class="ml-2 px-2 py-1 text-xs rounded-full bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white font-semibold">Default</span>
            @endif
        </div>
        <div class="text-[#6B6B6B] text-sm">
            <div>{{ $address->address }}, {{ $address->city }}, {{ $address->state }} - {{ $address->pincode }}</div>
            <div>{{ $address->country }}</div>
            <div>Phone: {{ $address->phone }}</div>
        </div>
    </div>
    <div class="flex flex-col md:flex-row gap-2 md:items-center">
        @if(!$address->is_default)
        <form method="POST" action="{{ route('user.dashboard.addresses.default', $address) }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-lg bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white font-semibold hover:shadow-lg transition-all duration-300">Set Default</button>
        </form>
        @endif
        <button class="px-4 py-2 rounded-lg border border-gray-200 text-[#1A1A1A] hover:bg-gray-50 font-semibold transition-all duration-300" onclick='editAddress({{ $address->id }}, @json($address))'>Edit</button>
        <form method="POST" action="{{ route('user.dashboard.addresses.destroy', $address) }}" class="inline delete-address-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 font-semibold transition-all duration-300">Delete</button>
        </form>
    </div>
</div>
@endforeach
@if($addresses->isEmpty())
    <div class="text-center text-[#6B6B6B] py-8">No addresses found. Add your first address!</div>
@endif
