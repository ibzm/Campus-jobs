<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('Student Dashboard') }}</h2>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-semibold mb-2">{{ __('Welcome, ') }} {{ $student->first_name }}!</h3>
                <p class="mb-4">{{ __('Remaining Hours: ') }} {{ $student->remaining_hours }}</p>

                <h4 class="text-lg font-semibold mb-2">{{ __('Assigned Jobs') }}</h4>
                <ul class="list-disc pl-6 mb-4">
                    @foreach($jobs as $job)
                        <li>{{ $job->title }} - {{ $job->description }}</li>
                    @endforeach
                </ul>

                <h4 class="text-lg font-semibold mb-2">{{ __('Timesheets') }}</h4>
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
                        @foreach($timesheets as $timesheet)
                            <tr class="border border-gray-300">
                                <td class="border border-gray-300 px-4 py-2">{{ $timesheet->job->title }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $timesheet->hours_requested }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $timesheet->shift_start }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $timesheet->shift_end }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $timesheet->approved ? 'Approved' : 'Pending' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
