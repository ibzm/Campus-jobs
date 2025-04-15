
<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('Timesheet Details') }}</h2>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-semibold mb-2">
                    {{ __('Job Title: ') }} {{ $timesheet->job->title }}
                </h3>
                <p class="mb-4">
                    {{ __('Job Description: ') }} {{ $timesheet->job->description }}
                </p>

                <h4 class="text-lg font-semibold mb-2">{{ __('Shift Details') }}</h4>
                <p>{{ __('Shift Start: ') }} {{ $timesheet->shift_start }}</p>
                <p>{{ __('Shift End: ') }} {{ $timesheet->shift_end }}</p>
                <p>{{ __('Hours Worked: ') }} {{ $timesheet->hours_requested }}</p>

                <h4 class="text-lg font-semibold mb-2">{{ __('Approval Status') }}</h4>
                <p>{{ ucfirst($timesheet->status) }}</p>

                <h4 class="text-lg font-semibold mb-2">{{ __('Actions') }}</h4>
                @if($timesheet->status === 'pending')
                    <form action="{{ route('student.processTimesheet', $timesheet->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="decision" value="approve">
                        <button type="submit" class="btn btn-primary">{{ __('Approve') }}</button>
                    </form>
                    <form action="{{ route('student.processTimesheet', $timesheet->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="decision" value="reject">
                        <button type="submit" class="btn btn-danger">{{ __('Reject') }}</button>
                    </form>
                @else
                    <p>{{ __('This timesheet has already been processed.') }}</p>
                @endif

                <a href="{{ route('student.dashboard') }}" class="text-blue-500">
                    {{ __('Back to Dashboard') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
