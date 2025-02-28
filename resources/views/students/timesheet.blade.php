<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Submit Timesheet
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('timesheet.store') }}" method="POST">
                    @csrf
                
                    <div class="mb-4">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">User ID</label>
                        <input type="number" name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300">
                    </div>

                 
                    <div class="mb-4">
                        <label for="recruiter_name" class="block text-sm font-medium text-gray-700">Recruiter Name</label>
                        <input type="text" name="recruiter_name" id="recruiter_name" class="mt-1 block w-full rounded-md border-gray-300">
                    </div>

               
                    <div class="mb-4">
                        <label for="requested_hours" class="block text-sm font-medium text-gray-700">Requested Hours</label>
                        <input type="number" step="0.01" name="requested_hours" id="requested_hours" class="mt-1 block w-full rounded-md border-gray-300">
                    </div>

                 
                    <div class="mb-4">
                        <label for="date_time" class="block text-sm font-medium text-gray-700">Date &amp; Time</label>
                        <input type="datetime-local" name="date_time" id="date_time" class="mt-1 block w-full rounded-md border-gray-300">
                    </div>

                    <div class="mb-4">
                        <label for="remaining_hours" class="block text-sm font-medium text-gray-700">Remaining Hours</label>
                        <input type="number" step="0.01" name="remaining_hours" id="remaining_hours" class="mt-1 block w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <x-primary-button type="submit">
                            Submit Timesheet
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
