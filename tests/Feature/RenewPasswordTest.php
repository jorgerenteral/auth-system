<?php

namespace Tests\Feature;

use Tests\TestCase;
use Livewire\Livewire;
use Illuminate\Support\Str;
use App\Http\Livewire\RenewPassword;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RenewPasswordTest extends TestCase
{

    use WithFaker;
    use RefreshDatabase;

    /**
     * Asserts that with a valid user, the component emits an 'alert' event and redirects to '/login'.
     *
     * @test
     *
     * @return void
     */
    public function with_valid_user()
    {
        $user = $this->createUserAndSendEmail();
        $password = Str::random(8);

        Livewire::test(RenewPassword::class, ['token' => base64_encode($user->renew_token)])
            ->set([
                'password' => $password,
                'password_confirmation' => $password,
            ])
            ->call('renewPassword')->assertEmitted('alert', 'Your password has been changed successfully.')->assertRedirect('/login');
    }

    /**
     * Asserts that with an empty from, the password key will have required error.
     *
     * @test
     *
     * @return void
     */
    public function with_empty_form()
    {
        $user = $this->createUserAndSendEmail();

        Livewire::test(RenewPassword::class, ['token' => base64_encode($user->renew_token)])
            ->call('renewPassword')->assertHasErrors(['password' => 'required']);
    }

    /**
     * Asserts that with short password, the password key will have min error.
     *
     * @test
     *
     * @return void
     */
    public function with_short_password()
    {
        $user = $this->createUserAndSendEmail();

        Livewire::test(RenewPassword::class, ['token' => base64_encode($user->renew_token)])
            ->set([
                'password' => Str::random(7)
            ])
            ->call('renewPassword')->assertHasErrors(['password' => 'min']);
    }

    /**
     * Asserts that with unmatched passwords, the password key will have confirmed error.
     *
     * @test
     *
     * @return void
     */
    public function with_unmatch_password()
    {
        $user = $this->createUserAndSendEmail();

        Livewire::test(RenewPassword::class, ['token' => base64_encode($user->renew_token)])
            ->set([
                'password' => Str::random(7),
                'password_confirmation' => Str::random(7),
            ])
            ->call('renewPassword')->assertHasErrors(['password' => 'confirmed']);
    }

    /**
     * Asserts that with a bad token, the component will redirect to '/login.
     *
     * @test
     *
     * @return void
     */
    public function with_bad_token()
    {
        Livewire::test(RenewPassword::class, ['token' => base64_encode(Str::random(64))])
            ->assertRedirect('/login');
    }
}
