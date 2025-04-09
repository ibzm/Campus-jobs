<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('Timesheet Details') }}</h2>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-semibold mb-2">{{ __('Job Title: ') }} {{ $timesheet->job->title }}</h3>
                <p class="mb-4">{{ __('Job Description: ') }} {{ $timesheet->job->description }}</p>

                <h4 class="text-lg font-semibold mb-2">{{ __('Shift Details') }}</h4>
                <p>{{ __('Shift Start: ') }} {{ $timesheet->shift_start }}</p>
                <p>{{ __('Shift End: ') }} {{ $timesheet->shift_end }}</p>
                <p>{{ __('Hours Worked: ') }} {{ $timesheet->hours_requested }}</p>

                <h4 class="text-lg font-semibold mb-2">{{ __('Approval Status') }}</h4>
                <p>{{ $timesheet->approved ? 'Approved' : 'Pending' }}</p>

                <h4 class="text-lg font-semibold mb-2">{{ __('Actions') }}</h4>
                @if (!$timesheet->approved)
                    <form action="{{ route('student.approveTimesheet', $timesheet->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">{{ __('Approve') }}</button>
                    </form>
                @else
                    <p>{{ __('This timesheet has already been approved.') }}</p>
                @endif

                <a href="{{ route('student.dashboard') }}" class="text-blue-500">{{ __('Back to Dashboard') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>

