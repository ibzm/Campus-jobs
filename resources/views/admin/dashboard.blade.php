<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-semibold mb-6">{{ __('Admin Dashboard') }}</h2>
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 shadow rounded">
                <div class="text-xl font-bold">{{ $pendingTimesheets }}</div>
                <div class="text-gray-600">{{ __('Pending Timesheets') }}</div>
            </div>
            <div class="bg-white p-4 shadow rounded">
                <div class="text-xl font-bold">{{ $pendingHourRequests }}</div>
                <div class="text-gray-600">{{ __('Pending Hour Requests') }}</div>
            </div>
            <div class="bg-white p-4 shadow rounded">
                <div class="text-xl font-bold">{{ $approvedTimesheets }}</div>
                <div class="text-gray-600">{{ __('Approved Timesheets') }}</div>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-2">{{ __('All Timesheets') }}</h3>
            @if($timesheets->isEmpty())
                <p>{{ __('No timesheets found.') }}</p>
            @else
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2">ID</th>
                            <th class="border border-gray-300 px-4 py-2">Student</th>
                            <th class="border border-gray-300 px-4 py-2">Job</th>
                            <th class="border border-gray-300 px-4 py-2">Hours</th>
                            <th class="border border-gray-300 px-4 py-2">Shift Start</th>
                            <th class="border border-gray-300 px-4 py-2">Shift End</th>
                            <th class="border border-gray-300 px-4 py-2">Status</th>
                            <th class="border border-gray-300 px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timesheets as $ts)
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">{{ $ts->id }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    {{ $ts->user->first_name }} {{ $ts->user->second_name }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2">{{ $ts->job->title }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $ts->hours_requested }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $ts->shift_start }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $ts->shift_end }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ ucfirst($ts->status) }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <a href="{{ route('admin.edit_timesheet', $ts->id) }}" class="text-blue-500">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>


        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-2">{{ __('All Hour Requests') }}</h3>
            @if($hourRequests->isEmpty())
                <p>{{ __('No hour requests found.') }}</p>
            @else
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2">ID</th>
                            <th class="border border-gray-300 px-4 py-2">Student</th>
                            <th class="border border-gray-300 px-4 py-2">Requested Hours</th>
                            <th class="border border-gray-300 px-4 py-2">Requested Date</th>
                            <th class="border border-gray-300 px-4 py-2">Status</th>
                            <th class="border border-gray-300 px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hourRequests as $hr)
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">{{ $hr->id }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    {{ $hr->student->first_name }} {{ $hr->student->second_name }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2">{{ $hr->requested_hours }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $hr->requested_date }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ ucfirst($hr->status) }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <a href="{{ route('admin.editHourRequest', $hr->id) }}" class="text-blue-500">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="mt-4 flex space-x-4">
    <a href="{{ route('admin.exportReport') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
        {{ __('Export Timesheets') }}
    </a>
    <a href="{{ route('admin.exportHourRequests') }}" class="bg-green-500 text-white px-4 py-2 rounded">
        {{ __('Export Hour Requests') }}
    </a>
</div>

    </div>
</x-app-layout>
