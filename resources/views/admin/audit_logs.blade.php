<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-semibold mb-6">{{ __('Audit Logs') }}</h2>
        
        @if($auditLogs->isEmpty())
            <p class="text-gray-700">{{ __('No audit log entries found.') }}</p>
        @else
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Record Type</th>
                        <th class="border px-4 py-2">Record ID</th>
                        <th class="border px-4 py-2">Admin (User ID)</th>
                        <th class="border px-4 py-2">Changes</th>
                        <th class="border px-4 py-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($auditLogs as $log)
                        <tr>
                            <td class="border px-4 py-2">{{ $log->id }}</td>
                            <td class="border px-4 py-2">{{ $log->record_type }}</td>
                            <td class="border px-4 py-2">{{ $log->record_id }}</td>
                            <td class="border px-4 py-2">{{ $log->user_id }}</td>
                            <td class="border px-4 py-2"><pre>{{ $log->changes }}</pre></td>
                            <td class="border px-4 py-2">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="mt-6">
            <a href="{{ route('admin.dashboard') }}" class="text-blue-500 underline">{{ __('Back to Dashboard') }}</a>
        </div>
    </div>
</x-app-layout>
