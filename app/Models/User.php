<?php

namespace App\Models;

use App\Enum\UserStatusEnum;
use App\Notifications\UserForgotPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\UnauthorizedException;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'session_token',
        'renew_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'session_token',
        'renew_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => UserStatusEnum::class,
    ];

    public function hasValidSession()
    {
        if (!$this->session_token) {
            return false;
        }

        try {
            \Firebase\JWT\JWT::decode($this->session_token, new \Firebase\JWT\Key(config('app.key'), 'HS256'));

            return true;
        } catch (\Firebase\JWT\BeforeValidException $e) {
            return false;
        } catch (\Firebase\JWT\ExpiredException $e) {
            $this->invalidateSession();

            return false;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $this->invalidateSession();

            return false;
        }
    }

    public function updateSession()
    {
        $this->session_token = \Firebase\JWT\JWT::encode(['id' => $this->id, 'exp' => strtotime('+10 minutes')], config('app.key'), 'HS256');
        $this->save();

        session(['session_token' => $this->session_token]);
    }

    public function invalidateSession()
    {
        $this->session_token = null;
        $this->save();

        session()->forget('session_token');
    }

    public function invalidateRenewToken()
    {
        $this->renew_token = null;
        $this->save();
    }

    public function sendForgotPasswordNotification()
    {
        $this->renew_token = encrypt(['exp' => now()->addMinutes(10), 'id' => $this->id]);
        $this->save();

        $this->notify(new UserForgotPassword(base64_encode($this->renew_token)));
    }
}
