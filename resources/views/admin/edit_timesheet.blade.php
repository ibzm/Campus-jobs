<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-semibold mb-6">{{ __('Edit Timesheet') }}</h2>

        <form action="{{ route('admin.updateTimesheet', $timesheet->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">{{ __('Job Title:') }}</label>
                <p>{{ $timesheet->job->title }}</p>
            </div>

            <div class="mb-4">
                <label for="hours_requested" class="block text-gray-700 font-bold mb-2">{{ __('Hours Worked:') }}</label>
                <input type="number" name="hours_requested" id="hours_requested" value="{{ $timesheet->hours_requested }}" class="w-full p-2 border rounded">
            </div>

            <div class="mb-4">
                <label for="status" class="block text-gray-700 font-bold mb-2">{{ __('Status:') }}</label>
                <select name="status" id="status" class="w-full p-2 border rounded">
                    <option value="pending" {{ $timesheet->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $timesheet->status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $timesheet->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="override_message" class="block text-gray-700 font-bold mb-2">{{ __('Override Message:') }}</label>
                <textarea name="override_message" id="override_message" class="w-full p-2 border rounded" placeholder="Enter override message if any...">{{ $timesheet->override_message }}</textarea>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                {{ __('Update Timesheet') }}
            </button>
        </form>

        <div class="mt-4">
            <a href="{{ route('admin.dashboard') }}" class="text-blue-500 underline">
                {{ __('Back to Dashboard') }}
            </a>
        </div>
    </div>
</x-app-layout>
