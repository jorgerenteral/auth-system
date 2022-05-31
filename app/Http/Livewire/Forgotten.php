<?php

namespace App\Http\Livewire;

use App\Enum\UserStatusEnum;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Livewire\Component;

class Forgotten extends Component
{
    public $email;

    protected $rules = [
        'email' => 'required|email',
    ];

    public function render()
    {
        return view('livewire.forgotten');
    }

    public function forgot()
    {
        $this->validate();

        $user = User::whereEmail($this->email)->first();

        if (!$user) {
            $this->emit('alert', 'If this email address is registered, we\'ll send you a notification with instructions to reset your password');
            return;
        }

        if ($user->status === UserStatusEnum::SUSPENDED) {
            $this->emit('alert', 'Your account is suspended, please contact the administrator');
            return;
        }

        if ($user->renew_token) {
            try {
                $token = decrypt($user->renew_token);

                if ($token['exp']->isFuture()) {
                    $this->emit('alert', 'You have already requested a password reset, please check your email');
                    return;
                }

                $user->sendForgotPasswordNotification();
            } catch (Exception $e) {
                $user->renew_token = null;

                $user->save();

                $this->emit('alert', 'There was a problem while checking your current status, please try again');
                return;
            }
        }

        $user->sendForgotPasswordNotification();

        $this->emit('alert', 'If this email address is registered, we\'ll send you a notification with instructions to reset your password');

        redirect()->route('login');
    }
}
