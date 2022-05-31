<?php

namespace App\Http\Livewire;

use App\Enum\UserStatusEnum;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Login extends Component
{

    public $email;
    public $password;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8',
    ];

    public function render()
    {
        return view('livewire.login');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function login()
    {
        $this->validate();

        $query = User::whereEmail($this->email);

        if (!$query->exists()) {
            $this->emit('alert', 'Wrong credentials');
            return;
        }

        $user = $query->first();

        if (!Hash::check($this->password, $user->password)) {
            $this->emit('alert', 'Wrong credentials');
            return;
        }

        if ($user->status === UserStatusEnum::SUSPENDED) {
            $this->emit('alert', 'Your account is suspended, please contact the administrator');
            return;
        }

        if ($user->hasValidSession()) {
            $this->emit('alert', 'You are already logged in! Close the session before you can login again');
            return;
        }

        $user->session_token = \Firebase\JWT\JWT::encode(['id' => $user->id, 'exp' => strtotime('+10 minutes')], config('app.key'), 'HS256');
        $user->save();

        session(['session_token' => $user->session_token]);

        return redirect()->route('dashboard.users.index');
    }
}
