<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class RenewPassword extends Component
{
    public $token;
    public $user;
    public $password;
    public $password_confirmation;

    public $rules = [
        'password' => 'required|min:8|confirmed',
    ];

    /**
     * RenewPassword constructor.
     * 
     * @return void
     */
    public function mount($token)
    {
        $this->token = $token;

        try {
            $decodedToken = base64_decode($this->token);
            $decryptedToken = decrypt($decodedToken);

            $this->user = User::whereId($decryptedToken['id'])->whereRenewToken($decodedToken)->first();

            if (is_null($this->user)) {
                return redirect()->route('login');
            }

            if ($decryptedToken['exp']->isPast()) {
                if ($this->user) {
                    $this->user->invalidateRenewToken();
                }

                return redirect()->route('login');
            }
        } catch (DecryptException $e) {
            return redirect()->route('login');
        } catch (Exception $e) {
            return redirect()->route('login');
        }
    }

    public function render()
    {
        return view('livewire.renew-password');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function renewPassword()
    {
        $this->validate();

        $this->user->password = Hash::make($this->password);
        $this->user->save();
        $this->user->invalidateRenewToken();

        $this->emit('alert', 'Your password has been changed successfully.');

        return redirect()->route('login');
    }
}
