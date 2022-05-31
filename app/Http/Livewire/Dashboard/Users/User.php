<?php

namespace App\Http\Livewire\Dashboard\Users;

use App\Enum\UserStatusEnum;
use App\Models\User as ModelsUser;
use Illuminate\Validation\Rule;
use Livewire\Component;

class User extends Component
{
    public $backRoute;
    public $currentUser;
    public $user;
    public $email;
    public $status;

    public $statuses;

    protected $rules = [
        'email' => 'required|email',
        'status' => 'required|in:Active,Suspended',
    ];

    public function mount(ModelsUser $user)
    {
        $this->backRoute = url()->previous() === url()->current() ? route('dashboard.users.index') : url()->previous();
        $this->currentUser = request()->user;
        $this->user = $user;
        $this->email = $user->email;
        $this->status = $user->status->value;

        $this->statuses = array_map(function ($status) {
            return $status->value;
        }, UserStatusEnum::cases());
    }

    public function render()
    {
        return view('livewire.dashboard.users.user');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->validate();

        $existsEmail = ModelsUser::whereEmail($this->email)->where('id', '!=', $this->user->id)->exists();

        if ($existsEmail) {
            $this->emit('alert', 'Email is already registered, and is not yours');
            return;
        }

        if ($this->status === $this->user->status->value && $this->email === $this->user->email) {
            $this->emit('alert', 'Nothing changed.');
            return;
        }

        if ($this->user->id === $this->currentUser->id) {
            $this->emit('alert', 'You cannot edit your own account.');
            return;
        }

        $this->user->update([
            'email' => $this->email,
            'status' => $this->status,
        ]);

        $this->user->refresh();

        $this->emit('alert', 'User updated successfully');
    }
}
