<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-xl font-semibold mb-4">Timesheets for My Students</h2>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2">Student Name</th>
                    <th class="border border-gray-300 px-4 py-2">Email</th>
                    <th class="border border-gray-300 px-4 py-2">Shift Start</th>
                    <th class="border border-gray-300 px-4 py-2">Shift End</th>
                    <th class="border border-gray-300 px-4 py-2">hours requested</th>
                    <th class="border border-gray-300 px-4 py-2">Approved</th>
                </tr>
            </thead>
            <tbody>
                @foreach($timesheets as $timesheet)
                <tr class="border border-gray-300">
                    <td class="border border-gray-300 px-4 py-2">{{ $timesheet->first_name }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $timesheet->email }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $timesheet->shift_start }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $timesheet->shift_end }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $timesheet->hours_requested }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $timesheet->approved }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
