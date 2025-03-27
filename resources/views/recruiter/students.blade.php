<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-xl font-semibold mb-4">Students Assigned to My Jobs</h2>

        @if ($students->isEmpty())
            <p>No students assigned to your jobs.</p>
        @else
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2">First Name</th>
                        <th class="border border-gray-300 px-4 py-2">Last Name</th>
                        <th class="border border-gray-300 px-4 py-2">Email</th>
                        <th class="border border-gray-300 px-4 py-2">Remaining hours</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr class="border border-gray-300">
                            <td class="border border-gray-300 px-4 py-2">{{ $student->first_name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $student->second_name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $student->email }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $student->remaining_hours }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
