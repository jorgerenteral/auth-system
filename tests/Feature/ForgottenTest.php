<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Support\Str;
use App\Enum\UserStatusEnum;
use App\Http\Livewire\Forgotten;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ForgottenTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     * Asserts that with a valid email address, the component will emit 'alert' event and then redirects to '/login.
     *
     * @test
     *
     * @return void
     */
    public function with_valid_email()
    {
        $email = $this->faker->email;

        User::create([
            'name' => $this->faker->name,
            'email' => $email,
            'password' => Str::random(8),
            'status' => UserStatusEnum::ACTIVE
        ]);

        Livewire::test(Forgotten::class)
            ->set(['email' => $email])
            ->call('forgot')
            ->assertEmitted('alert', 'If this email address is registered, we\'ll send you a notification with instructions to reset your password')
            ->assertRedirect('/login');
    }

    /**
     * Asserts that with an empty form, email key will have required error.
     *
     * @test
     *
     * @return void
     */
    public function with_empty_form()
    {
        Livewire::test(Forgotten::class)
            ->call('forgot')->assertHasErrors(['email' => 'required']);
    }

    /**
     * Asserts that with a malformed address, email key will have email error.
     *
     * @test
     *
     * @return void
     */
    public function malfomed_email()
    {
        Livewire::test(Forgotten::class)
            ->set(['email' => 'malformedaddress'])
            ->call('forgot')->assertHasErrors(['email' => 'email']);
    }

    /**
     * Asserts that with a wrong address, the component will emit 'alert' event.
     *
     * @test
     *
     * @return void
     */
    public function wrong_email()
    {
        Livewire::test(Forgotten::class)
            ->set(['email' => $this->faker->email])
            ->call('forgot')->assertEmitted('alert', 'If this email address is registered, we\'ll send you a notification with instructions to reset your password');
    }

    /**
     * Asserts that with suspended account, the component will emit 'alert' event.
     *
     * @test
     *
     * @return void
     */
    public function suspended_account()
    {
        $email = $this->faker->email;

        User::create([
            'name' => $this->faker->name,
            'email' => $email,
            'password' => Str::random(8),
            'status' => UserStatusEnum::SUSPENDED,
        ]);


        Livewire::test(Forgotten::class)
            ->set(['email' => $email])
            ->call('forgot')->assertEmitted('alert', 'Your account is suspended, please contact the administrator');
    }

    /**
     * Asserts that when exists a valid renew_token, the component will emit 'alert' event.
     *
     * @test
     *
     * @return void
     */
    public function with_valid_renew_token()
    {
        $email = $this->faker->email;

        $user = User::create([
            'name' => $this->faker->name,
            'email' => $email,
            'password' => Str::random(8),
            'status' => UserStatusEnum::ACTIVE,
        ]);

        $user->sendForgotPasswordNotification();

        Livewire::test(Forgotten::class)
            ->set(['email' => $email])
            ->call('forgot')->assertEmitted('alert', 'You have already requested a password reset, please check your email');
    }

    /**
     * Asserts that when doesn't exists a valid renew_token, the component will emit 'alert' event.
     *
     * @test
     *
     * @return void
     */
    public function with_invalid_renew_token()
    {
        $email = $this->faker->email;

        User::create([
            'name' => $this->faker->name,
            'email' => $email,
            'password' => Str::random(8),
            'status' => UserStatusEnum::ACTIVE,
            'renew_token' => Str::random(64)
        ]);

        Livewire::test(Forgotten::class)
            ->set(['email' => $email])
            ->call('forgot')->assertEmitted('alert', 'There was a problem while checking your current status, please try again');
    }
}
