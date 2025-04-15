<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-semibold mb-6">{{ __('Admin Notifications') }}</h2>

        @if($notifications->isEmpty())
            <p class="text-gray-700">{{ __('No notifications found.') }}</p>
        @else
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <div class="bg-white shadow rounded p-4 border border-gray-300">
                        <div class="font-bold text-lg">
                            {{ ucwords($notification->data['status'] ?? 'Notification') }}
                        </div>
                        <div class="mt-2 text-gray-800">
                            {{ $notification->data['message'] ?? 'No additional details provided.' }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('admin.dashboard') }}" class="text-blue-500 underline">
                {{ __('Back to Dashboard') }}
            </a>
        </div>
    </div>
</x-app-layout>
