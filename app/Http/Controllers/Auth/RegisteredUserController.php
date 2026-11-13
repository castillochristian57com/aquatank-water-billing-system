<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     * Only permitted for the initial system administrator setup.
     */
    public function create(): View|RedirectResponse
    {
        // If an administrator already exists, close public registration
        if (User::count() > 0) {
            return redirect()->route('login')->with(
                'status',
                'System initialization is complete. An administrator account is already active. Please log in.'
            );
        }

        return view('auth.register', ['isFirstAdmin' => true]);
    }

    /**
     * Handle an incoming registration request.
     * Only creates the initial administrator account.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Enforce protection: do not allow unauthorized visitors to register if an admin already exists
        if (User::count() > 0) {
            return redirect()->route('login')->with(
                'error',
                'Registration is closed. Additional administrative accounts cannot be created publicly.'
            );
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false))->with(
            'success',
            'Initial system administrator account created successfully. Welcome to AquaTank!'
        );
    }
}
