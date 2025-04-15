<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('Upcoming Shifts') }}</h2>

        @if($upcomingHourRequests->isEmpty())
            <p>{{ __('No upcoming shifts scheduled.') }}</p>
        @else
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2">{{ __('Requested Date') }}</th>
                        <th class="border border-gray-300 px-4 py-2">{{ __('Start Time') }}</th>
                        <th class="border border-gray-300 px-4 py-2">{{ __('End Time') }}</th>
                        <th class="border border-gray-300 px-4 py-2">{{ __('Requested Hours') }}</th>
                        <th class="border border-gray-300 px-4 py-2">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcomingHourRequests as $hr)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{ $hr->requested_date }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $hr->start_time }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $hr->end_time }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $hr->requested_hours }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ ucfirst($hr->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="mt-4">
            <a href="{{ route('student.dashboard') }}" class="text-blue-500">{{ __('Back to Dashboard') }}</a>
        </div>
    </div>
</x-app-layout>

