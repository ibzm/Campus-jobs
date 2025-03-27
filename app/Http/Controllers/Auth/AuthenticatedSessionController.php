<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
    
        $user = Auth::user();
    
        // Fetch the user's role from the user_role table
        $role = DB::table('user_role')
            ->where('user_id', $user->id)
            ->join('role', 'user_role.role_id', '=', 'role.id')
            ->value('role.name'); // Get role name
    
        // Redirect based on role
        if ($role === 'Admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'Recruiter') {
            return redirect()->route('recruiter.index');  // Correct route name
        } elseif ($role === 'Student') {
            return redirect()->route('student.dashboard');
        } else {
            return redirect()->route('login')->withErrors(['error' => 'Invalid role assigned']);
        }
    }
    


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
