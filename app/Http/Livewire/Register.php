<?php

namespace App\Http\Livewire;

use App\Enum\UserStatusEnum;
use App\Models\User;
use App\Notifications\UserWelcome;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{

    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
    ];

    public function render()
    {
        return view('livewire.register');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function register()
    {
        $this->validate();

        $query = User::whereEmail($this->email);

        if ($query->exists()) {
            $this->emit('alert', 'This email is already registered');
            return;
        }

        $user = new User();

        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = Hash::make($this->password);
        $user->status = UserStatusEnum::ACTIVE;

        $user->save();

        $user->notify(new UserWelcome);

        $this->emit('alert', 'Your account was created succesfully');

        return redirect()->route('login');
    }
}
