<?php

namespace App\Http\Controllers;
use App\Models\Student;

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
}