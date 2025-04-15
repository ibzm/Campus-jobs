<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('History') }}</h2>
        
        <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold">{{ __('Student: ') }} {{ $student->first_name }} {{ $student->second_name }}</h3>
            <p class="mt-2">{{ __('Remaining Hours: ') }} {{ $student->remaining_hours }}</p>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">{{ __('Hour Requests History') }}</h3>
            @if($allHourRequests->isEmpty())
                <p>{{ __('No hour requests found.') }}</p>
            @else
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2">{{ __('Requested Hours') }}</th>
                            <th class="border border-gray-300 px-4 py-2">{{ __('Requested Date') }}</th>
                            <th class="border border-gray-300 px-4 py-2">{{ __('Start Time') }}</th>
                            <th class="border border-gray-300 px-4 py-2">{{ __('End Time') }}</th>
                            <th class="border border-gray-300 px-4 py-2">{{ __('Status') }}</th>
                            <th class="border border-gray-300 px-4 py-2">{{ __('Reason') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allHourRequests as $hr)
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">{{ $hr->requested_hours }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $hr->requested_date }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $hr->start_time }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $hr->end_time }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ ucfirst($hr->status) }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $hr->reason ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">{{ __('Timesheets History') }}</h3>
            @if($allTimesheets->isEmpty())
                <p>{{ __('No timesheets found.') }}</p>
            @else
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2">{{ __('Job') }}</th>
                            <th class="border border-gray-300 px-4 py-2">{{ __('Hours Worked') }}</th>
                            <th class="border border-gray-300 px-4 py-2">{{ __('Shift Start') }}</th>
                            <th class="border border-gray-300 px-4 py-2">{{ __('Shift End') }}</th>
                            <th class="border border-gray-300 px-4 py-2">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allTimesheets as $ts)
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">{{ $ts->job->title }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $ts->hours_requested }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $ts->shift_start }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $ts->shift_end }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ ucfirst($ts->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="mt-4">
            <a href="{{ route('student.dashboard') }}" class="text-blue-500">{{ __('Back to Dashboard') }}</a>
        </div>
    </div>
</x-app-layout>
