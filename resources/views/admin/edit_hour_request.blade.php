<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-semibold mb-6">{{ __('Edit Hour Request') }}</h2>

        <form action="{{ route('admin.updateHourRequest', $hourRequest->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">{{ __('Student:') }}</label>
                <p>{{ $hourRequest->student->first_name }} {{ $hourRequest->student->second_name }}</p>
            </div>

            <div class="mb-4">
                <label for="requested_hours" class="block text-gray-700 font-bold mb-2">{{ __('Requested Hours:') }}</label>
                <input type="number" name="requested_hours" id="requested_hours" value="{{ $hourRequest->requested_hours }}" class="w-full p-2 border rounded">
            </div>

            <div class="mb-4">
                <label for="status" class="block text-gray-700 font-bold mb-2">{{ __('Status:') }}</label>
                <select name="status" id="status" class="w-full p-2 border rounded">
                    <option value="pending" {{ $hourRequest->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $hourRequest->status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $hourRequest->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="reason" class="block text-gray-700 font-bold mb-2">{{ __('Reason:') }}</label>
                <textarea name="reason" id="reason" class="w-full p-2 border rounded" placeholder="Enter reason if the request is rejected...">{{ $hourRequest->reason }}</textarea>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                {{ __('Update Hour Request') }}
            </button>
        </form>

        <div class="mt-4">
            <a href="{{ route('admin.dashboard') }}" class="text-blue-500 underline">
                {{ __('Back to Dashboard') }}
            </a>
        </div>
    </div>
</x-app-layout>
