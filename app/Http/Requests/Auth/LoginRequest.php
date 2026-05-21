<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $loginType = $this->input('login_type', 'email');

        return [
            'email'    => $loginType === 'email'    ? ['required', 'string', 'email'] : ['nullable', 'string'],
            'nickname' => $loginType === 'nickname' ? ['required', 'string', 'max:30'] : ['nullable', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = [
            'password' => $this->input('password'),
        ];

        if ($this->input('login_type', 'email') === 'nickname') {
            $credentials['nickname'] = $this->input('nickname');
        } else {
            $credentials['email'] = $this->input('email');
        }

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        $identifier = $this->login_type === 'email'
            ? Str::lower($this->email)
            : Str::lower($this->nickname);

        return Str::transliterate($identifier . '|' . $this->ip());
    }
}
