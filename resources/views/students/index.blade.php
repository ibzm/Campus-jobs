<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('students') }}
            <p>{{ $students->count() }} students found.</p>
            
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("this is the new page and maybe it works testing !") }}
                </div>
            </div>
        </div>
    </div>

    @if($students->isNotEmpty())

        <div class="overflow-x-auto w-full">
            <table
                class="border-separate border-2 m-auto my-4 lg:w-[90%] w-full text-center border-grey border-spacing-2 md:border-spacing-8 bg-stockhive-grey rounded-lg">
                <thead>
                    <tr>
                        <th class="py-2 px-4">ID</th>
                        <th class="py-2 px-4">Name</th>
                        <th class="py-2 px-4">Email</th>
                        <th class="py-2 px-4">Visa status</th>
                        <th class="py-2 px-4">Remaining hours</th>
                        <th class="py-2 px-4">Student ID</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr>
                            <td class="py-2 px-4">{{ $student->id }}</td>
                            <td class="py-2 px-4">{{ $student->first_name }}</td>
                            <td class="py-2 px-4">{{ $student->email }}</td>
                            <td class="py-2 px-4">{{ $student->visa_status }}</td>
                            <td class="py-2 px-4">{{ $student->remaining_hours }}</td>
                            <td class="py-2 px-4">{{ $student->student_id }}</td>
                            <td class="py-2 px-4 flex justify-center items-center gap-4">
                                <form action="{{ route('dashboard') }}" method="GET">
                                    <x-primary-button>Button1</x-primary-button>
                                </form>
                                <form action="{{ route('dashboard') }}" method="GET">
                                    <x-primary-button>Button2</x-primary-button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p>No students found.</p>
    @endif
</x-app-layout>