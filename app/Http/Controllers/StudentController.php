<?php

namespace App\Http\Controllers;
use App\Models\Student;
use App\Models\Timesheet;#
use App\Models\Item;
use App\Models\store_item;
use App\Models\store_item_storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::where('id', 1)
            //   ->where('store_id', '=', Auth::user()->store_id)
            //
            // ->where('id', '=', 1)
            // ->with('user')
            ->orderBy('first_name', 'asc')
            ->get();

        return view('students.index', compact('students'));

    }
    public function show_timesheet()
    {
        $timesheet = timesheet::where('id', 2)
        ->orderBy('recruiter_name', 'asc')
            ->get();

            return view('students.show_timesheet', compact('timesheet'));
    }

    public function create()
    {
        return view('students.timesheet');
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            // Use 'required' or 'nullable' as appropriate for your logic
            'user_id' => 'required|integer',
            'recruiter_name' => 'required|string|max:255',
            'requested_hours' => 'required|numeric',
            'date_time' => 'required|date',
            'remaining_hours' => 'required|numeric',
        ]);


        timesheet::create($validated);


        return redirect()->back()->with('success', 'Timesheet entry saved successfully!');
    }
}