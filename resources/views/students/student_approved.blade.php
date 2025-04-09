<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('Timesheet Approved') }}</h2>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <p class="text-green-500">{{ __('Your timesheet has been successfully approved!') }}</p>
                <a href="{{ route('student.dashboard') }}" class="text-blue-500">{{ __('Back to Dashboard') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>
