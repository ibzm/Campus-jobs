<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('Student Dashboard') }}</h2>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-semibold mb-2">
                    {{ __('Welcome, ') }}{{ $student->first_name }}!
                </h3>
                <p class="mb-4">{{ __('Remaining Hours: ') }}{{ $student->remaining_hours }}</p>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h4 class="text-lg font-semibold mb-2">{{ __('Assigned Jobs') }}</h4>
                @if($jobs->isEmpty())
                    <p>{{ __('No jobs assigned.') }}</p>
                @else
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 px-4 py-2">{{ __('Job Title') }}</th>
                                <th class="border border-gray-300 px-4 py-2">{{ __('Recruiter') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $job->title }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        {{ $job->recruiter_name ?? 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>


        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h4 class="text-lg font-semibold mb-2">{{ __('Pending Hour Requests') }}</h4>
                @if($pendingHourRequests->isEmpty())
                    <p>{{ __('There are no pending hour requests.') }}</p>
                @else
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 px-4 py-2">{{ __('Requested Hours') }}</th>
                                <th class="border border-gray-300 px-4 py-2">{{ __('Requested Date') }}</th>
                                <th class="border border-gray-300 px-4 py-2">{{ __('Start Time') }}</th>
                                <th class="border border-gray-300 px-4 py-2">{{ __('End Time') }}</th>
                                <th class="border border-gray-300 px-4 py-2">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingHourRequests as $hr)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $hr->requested_hours }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $hr->requested_date }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $hr->start_time }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $hr->end_time }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <form action="{{ route('student.processHourRequest', $hr->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="decision" value="approve">
                                            <button type="submit" class="btn btn-success">{{ __('Approve') }}</button>
                                        </form>
                                        <form action="{{ route('student.processHourRequest', $hr->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="decision" value="reject">
                                            <button type="submit" class="btn btn-danger">{{ __('Reject') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h4 class="text-lg font-semibold mb-2">{{ __('Timesheets') }}</h4>
                @if($timesheets->isEmpty())
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
                                <th class="border border-gray-300 px-4 py-2">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timesheets as $ts)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $ts->job->title }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $ts->hours_requested }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $ts->shift_start }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $ts->shift_end }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ ucfirst($ts->status) }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        @if($ts->status === 'pending')
                                            <form action="{{ route('student.processTimesheet', $ts->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="decision" value="approve">
                                                <button type="submit" class="btn btn-primary">{{ __('Approve') }}</button>
                                            </form>
                                            <form action="{{ route('student.processTimesheet', $ts->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="decision" value="reject">
                                                <button type="submit" class="btn btn-danger">{{ __('Reject') }}</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('student.showTimesheet', $ts->id) }}" class="text-blue-500">{{ __('View Details') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
