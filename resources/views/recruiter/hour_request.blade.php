<x-app-layout>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div class="container mx-auto px-4 py-6 max-w-4xl">
        <h2 class="text-2xl font-semibold mb-6">Request Hours via Calendar</h2>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                <ul class="list-disc ml-6">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div id='calendar' class="bg-white shadow rounded p-4 mb-8"></div>


        <div class="modal fade" id="hourRequestModal" tabindex="-1" aria-labelledby="hourRequestModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('recruiter.hour-request.submit') }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Request Hours for a Student</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body space-y-4">
                            <input type="hidden" id="selected_date" name="start_date">

                            <div>
                                <label for="student_id" class="block font-semibold text-sm">Student</label>
                                <select name="student_id" class="form-select mt-1 block w-full p-2 border rounded"
                                    required>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}">
                                            {{ $student->first_name }} {{ $student->second_name }}
                                            ({{ $student->remaining_hours }} hrs left)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-sm">Start Time</label>
                                <input type="time" name="start_time" class="mt-1 block w-full p-2 border rounded"
                                    required>
                            </div>

                            <div>
                                <label class="block font-semibold text-sm">End Time</label>
                                <input type="time" name="end_time" class="mt-1 block w-full p-2 border rounded"
                                    required>
                            </div>

                            <div>
                                <label class="block font-semibold text-sm">Repeat Weekly?</label>
                                <input type="number" name="recurrence_weeks"
                                    class="mt-1 block w-full p-2 border rounded" placeholder="e.g. 3 (for 3 weeks)"
                                    min="1">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="comment" class="block text-sm font-medium text-gray-700">Recruiter Comment
                                (optional)</label>
                            <textarea name="comment" id="comment" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('comment', $hourRequest->comment ?? '') }}</textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                                Submit Request
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                    initialView: 'dayGridMonth',
                    selectable: true,
                    select: function (info) {
                        document.getElementById('selected_date').value = info.startStr;
                        new bootstrap.Modal(document.getElementById('hourRequestModal')).show();
                    }
                });
                calendar.render();
            });
        </script>
    </div>
</x-app-layout>