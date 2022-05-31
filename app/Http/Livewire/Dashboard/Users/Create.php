<?php

namespace App\Http\Livewire\Dashboard\Users;

use App\Enum\UserStatusEnum;
use App\Models\User;
use App\Notifications\UserWelcome;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Create extends Component
{
    public $backRoute;
    public $name;
    public $email;
    public $status;
    public $password;
    public $password_confirmation;

    public $statuses;

    protected $rules = [
        'name' => 'required',
        'email' => 'required|email',
        'status' => 'required|in:Active,Suspended',
        'password' => 'required|min:8|confirmed',
    ];

    public function mount()
    {
        $this->backRoute = url()->previous() === url()->current() ? route('dashboard.users.index') : url()->previous();

        $this->statuses = array_map(function ($status) {
            return $status->value;
        }, UserStatusEnum::cases());
    }

    public function render()
    {
        return view('livewire.dashboard.users.create');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function create()
    {
        $this->validate();

        $existsEmail = User::whereEmail($this->email)->exists();

        if ($existsEmail) {
            $this->emit('alert', 'Email is already registered');
            return;
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'password' => Hash::make($this->password),
        ]);

        $user->notify(new UserWelcome());

        $this->emit('alert', 'User created successfully');

        redirect($this->backRoute);
    }
}
