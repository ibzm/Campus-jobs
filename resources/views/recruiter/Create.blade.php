<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Submit Timesheet') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <form method="POST" action="{{ route('recruiter.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="student_id" class="block font-medium text-gray-700 dark:text-gray-300">
                        Select Student:
                    </label>
                    <select name="student_id" id="student_id" required class="w-full mt-1 p-2 border rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->second_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="job_id" class="block font-medium text-gray-700 dark:text-gray-300">
                        Select Job:
                    </label>
                    <select name="job_id" id="job_id" required class="w-full mt-1 p-2 border rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                        <option value="">Select a job</option>
                    </select>
                </div>

                <div>
                    <label for="hour_request_id" class="block font-medium text-gray-700 dark:text-gray-300">
                        Select Hour Request:
                    </label>
                    <select name="hour_request_id" id="hour_request_id" class="w-full mt-1 p-2 border rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                        <option value="">No Hour Request (use dummy)</option>
                    </select>
                </div>
                <div>
                    <label for="shift_start" class="block font-medium text-gray-700 dark:text-gray-300">
                        Shift Start:
                    </label>
                    <input type="datetime-local" name="shift_start" id="shift_start" required class="w-full mt-1 p-2 border rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                </div>

                <div>
                    <label for="shift_end" class="block font-medium text-gray-700 dark:text-gray-300">
                        Shift End:
                    </label>
                    <input type="datetime-local" name="shift_end" id="shift_end" required class="w-full mt-1 p-2 border rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                </div>

                <div class="mt-4">
                    <x-primary-button type="submit">Submit Timesheet</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const allHourRequests = @json($hourRequests);
        const hourRequestsByStudent = allHourRequests.reduce((acc, hr) => {
            if (!acc[hr.student_id]) {
                acc[hr.student_id] = [];
            }
            acc[hr.student_id].push(hr);
            return acc;
        }, {});

        const studentJobs = @json($studentJobs);

        function updateHourRequestDropdown(studentId) {
            const hrSelect = document.getElementById('hour_request_id');
            hrSelect.innerHTML = '<option value="">No Hour Request (use dummy)</option>';
            if (hourRequestsByStudent[studentId]) {
                hourRequestsByStudent[studentId].forEach(hr => {
                    let option = document.createElement('option');
                    option.value = hr.id;
                    let displayText = `${hr.requested_date} - ${hr.requested_hours} hours (${hr.status.charAt(0).toUpperCase() + hr.status.slice(1)})`;
                    option.textContent = displayText;
                    hrSelect.appendChild(option);
                });
            }
        }

        function updateJobDropdown(studentId) {
            const jobSelect = document.getElementById('job_id');
            jobSelect.innerHTML = '<option value="">Select a job</option>';
            if (studentJobs[studentId]) {
                studentJobs[studentId].forEach(job => {
                    let option = document.createElement('option');
                    option.value = job.id;
                    option.textContent = job.title;
                    jobSelect.appendChild(option);
                });
            }
        }
        document.getElementById('student_id').addEventListener('change', function() {
            const studentId = this.value;
            updateHourRequestDropdown(studentId);
            updateJobDropdown(studentId);
        });

        const initialStudentId = document.getElementById('student_id').value;
        updateHourRequestDropdown(initialStudentId);
        updateJobDropdown(initialStudentId);
    </script>
</x-app-layout>
