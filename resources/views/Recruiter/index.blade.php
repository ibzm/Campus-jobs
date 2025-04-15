<x-app-layout>
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-red-100 p-4 rounded-lg shadow">
        <h3 class="text-xl font-semibold text-red-600">Flagged Timesheets</h3>
        <p class="text-3xl font-bold text-red-800">{{ $flaggedTimesheets }}</p>
    </div>


    <div class="bg-yellow-100 p-4 rounded-lg shadow">
        <h3 class="text-xl font-semibold text-yellow-600">Pending Hour Requests</h3>
        <p class="text-3xl font-bold text-yellow-800">{{ $pendingRequests }}</p>
    </div>

 
    <div class="bg-orange-100 p-4 rounded-lg shadow">
        <h3 class="text-xl font-semibold text-orange-600">Low Remaining Hours</h3>
        <p class="text-3xl font-bold text-orange-800">{{ $lowHourStudents }}</p>
    </div>

    <div class="bg-blue-100 p-4 rounded-lg shadow">
        <h3 class="text-xl font-semibold text-blue-600">Near Weekly Limit</h3>
        <p class="text-3xl font-bold text-blue-800">{{ $nearLimitStudents }}</p>
    </div>
</div>

<h2 class="text-xl font-semibold mb-2">Recent Timesheets</h2>
<table class="table-auto w-full text-left">
    <thead>
        <tr class="bg-gray-200">
            <th>Name</th>
            <th>Shift Start</th>
            <th>Shift End</th>
            <th>Hours Requested</th>
            <th>Flagged?</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($recentTimesheets as $ts)
        <tr>
            <td>{{ $ts->user->first_name }}</td>
            <td>{{ $ts->shift_start }}</td>
            <td>{{ $ts->shift_end }}</td>
            <td>{{ $ts->hours_requested }}</td>
            <td>{{ $ts->flagged ? 'Yes' : 'No' }}</td>
        </tr>
        @endforeach
        <a href="{{ route('recruiter.exportHourRequests') }}" class="btn btn-primary">Export Hour Requests</a>

    </tbody>
</table>

</x-app-layout>
