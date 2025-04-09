<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('Approved Timesheets') }}</h2>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                @if($timesheets->isEmpty())
                    <p>{{ __('You have no approved timesheets.') }}</p>
                @else
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th>{{ __('Job Title') }}</th>
                                <th>{{ __('Hours Worked') }}</th>
                                <th>{{ __('Shift Start') }}</th>
                                <th>{{ __('Shift End') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timesheets as $timesheet)
                                <tr>
                                    <td>{{ $timesheet->job->title }}</td>
                                    <td>{{ $timesheet->hours_requested }}</td>
                                    <td>{{ $timesheet->shift_start }}</td>
                                    <td>{{ $timesheet->shift_end }}</td>
                                    <td>{{ __('Approved') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
