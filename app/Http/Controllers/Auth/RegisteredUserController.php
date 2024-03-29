<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Unit;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $unit = Unit::orderBy('created_at', 'DESC')->get();
        return view('auth.register', compact('unit'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    { 
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'registration' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'is_admin' => ['required', 'int', 'max:255'],
            'is_active' => ['int', 'max:255'],
            'unit_id' => ['string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'registration' => $request->registration,
            'phone' => $request->phone,
            'is_admin' => $request->is_admin,
            'is_active' => $request->is_active,
            'unit_id' => $request->unit_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect("administrator");
    }
}
