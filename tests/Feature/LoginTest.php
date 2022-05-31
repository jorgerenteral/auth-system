<?php

namespace Tests\Feature;

use App\Enum\UserStatusEnum;
use App\Http\Livewire\Login;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{

    use WithFaker;
    use RefreshDatabase;

    /**
     * Asserts the redirect to Dashboard/Users when login with a correct user.
     *
     * @test
     *
     * @return void
     */
    public function active_user()
    {
        $email = $this->faker->email;
        $password = Str::random(8);

        User::create([
            'name' => $this->faker->name,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => UserStatusEnum::ACTIVE,
        ]);

        Livewire::test(Login::class)
            ->set([
                'email' => $email,
                'password' => $password,
            ])
            ->call('login')->assertRedirect('/dashboard/users');
    }

    /**
     * Asserts that when login with nonexistente User, the component emit the 'alert' event.
     *
     * @test
     *
     * @return void
     */
    public function nonexistente_user()
    {
        Livewire::test(Login::class)
            ->set([
                'email' => $this->faker->email,
                'password' => Str::random(8),
            ])
            ->call('login')->assertEmitted('alert', 'Wrong credentials');
    }

    /**
     * Asserts that when login with a wrong password, the component emit the 'alert' event.
     *
     * @test
     *
     * @return void
     */
    public function with_wrong_password()
    {
        $email = $this->faker->email;

        User::create([
            'name' => $this->faker->name,
            'email' => $email,
            'password' => Hash::make(Str::random(8)),
            'status' => UserStatusEnum::ACTIVE,
        ]);

        Livewire::test(Login::class)
            ->set([
                'email' => $this->faker->email,
                'password' => Str::random(8),
            ])
            ->call('login')->assertEmitted('alert', 'Wrong credentials');
    }

    /**
     * Asserts that when login with a suspended account, the component emit the 'alert' event.
     *
     * @test
     *
     * @return void
     */
    public function with_suspended_account()
    {
        $email = $this->faker->email;
        $password = Str::random(8);

        User::create([
            'name' => $this->faker->name,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => UserStatusEnum::SUSPENDED,
        ]);

        Livewire::test(Login::class)
            ->set([
                'email' => $email,
                'password' => $password,
            ])
            ->call('login')->assertEmitted('alert', "Your account is suspended, please contact the administrator");
    }

    /**
     * Asserts that when login and there\'s a valid session, the component emit the 'alert' event.
     *
     * @test
     *
     * @return void
     */
    public function login_and_then_try_to_login_with_a_valid_session()
    {
        $email = $this->faker->email;
        $password = Str::random(8);

        User::create([
            'name' => $this->faker->name,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => UserStatusEnum::ACTIVE,
        ]);

        Livewire::test(Login::class)
            ->set([
                'email' => $email,
                'password' => $password,
            ])
            ->call('login')->assertRedirect('/dashboard/users');

        Livewire::test(Login::class)
            ->set([
                'email' => $email,
                'password' => $password,
            ])
            ->call('login')->assertEmitted('alert', "You are already logged in! Close the session before you can login again");
    }

    /**
     * Asserts that with an empty login form, name, email and password keys has required errors.
     *
     * @test
     *
     * @return void
     */
    public function with_empty_form()
    {
        Livewire::test(Login::class)
            ->call('login')->assertHasErrors(['email' => 'required', 'password' => 'required']);
    }

    /**
     * Asserts that with an invalid address, email key has email error.
     *
     * @test
     *
     * @return void
     */
    public function with_malformed_email()
    {
        Livewire::test(Login::class)
            ->set([
                'email' => 'malformedemail',
            ])
            ->call('login')->assertHasErrors(['email' => 'email']);
    }

    /**
     * Asserts that with a less than 8 characters, password key has min error.
     *
     * @test
     *
     * @return void
     */
    public function with_short_password()
    {
        Livewire::test(Login::class)
            ->set([
                'email' => $this->faker->email,
                'password' => Str::random(7)
            ])
            ->call('login')->assertHasErrors(['password' => 'min']);
    }
}
