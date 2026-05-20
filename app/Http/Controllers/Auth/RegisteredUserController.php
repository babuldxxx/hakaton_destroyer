<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Label;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'nullable|string|max:255',
            'nickname' => 'required|string|max:255|unique:' . User::class,
            'email'    => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'in:label,artist'],
        ]);

        $user = User::create([
            'name'     => $request->name ?? $request->nickname,
            'nickname' => $request->nickname,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        if ($request->role === 'label') {
            $label = Label::create(['name' => $request->name]);
            $user->label_id = $label->id;
            $user->save();
        }

        if ($request->role === 'artist') {
            Artist::create([
                'user_id'    => $user->id,
                'stage_name' => $request->name,
                'real_name'  => $request->name,
                'status'     => 'pending',
            ]);
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard'));
    }
}
