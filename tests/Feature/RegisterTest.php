<?php

namespace Tests\Feature;

use App\Enum\UserStatusEnum;
use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Support\Str;
use App\Http\Livewire\Register;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{

    use WithFaker;
    use RefreshDatabase;

    /**
     * Asserts that after creating an user, the component emits an 'alert' event and redirects to '/login'.
     *
     * @test
     *
     * @return void
     */
    public function user_registered()
    {
        $name = $this->faker->name;
        $email = $this->faker->email;
        $password = Str::random(8);
        $password_confirmation = $password;

        Livewire::test(Register::class)
            ->set([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password_confirmation,
            ])
            ->call('register')->assertEmitted('alert', 'Your account was created succesfully')->assertRedirect('/login');
    }

    /**
     * Asserts that a user cannot be created if the email address is already register. The component emits an 'alert' event.
     *
     * @test
     *
     * @return void
     */
    public function email_is_already_registered()
    {
        $name = $this->faker->name;
        $email = $this->faker->email;
        $password = Str::random(8);
        $password_confirmation = $password;

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => UserStatusEnum::ACTIVE,
        ]);

        Livewire::test(Register::class)
            ->set([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password_confirmation,
            ])
            ->call('register')->assertEmitted('alert', 'This email is already registered');
    }

    /**
     * Asserts that with an empty register form, name, email and password keys has required errors.
     *
     * @test
     *
     * @return void
     */
    public function with_empty_form()
    {
        Livewire::test(Register::class)
            ->call('register')->assertHasErrors(['name' => 'required', 'email' => 'required', 'password' => 'required']);
    }

    /**
     * Asserts that with an 2 characters, name key has min error.
     *
     * @test
     *
     * @return void
     */
    public function with_short_name()
    {
        Livewire::test(Register::class)
            ->set([
                'name' => 'ab'
            ])
            ->call('register')->assertHasErrors(['name' => 'min']);
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
        Livewire::test(Register::class)
            ->set([
                'name' => 'John Doe',
                'email' => 'malformedemail',
            ])
            ->call('register')->assertHasErrors(['email' => 'email']);
    }

    /**
     * Asserts that without confirmation, password key has confirmed error.
     *
     * @test
     *
     * @return void
     */
    public function without_confirmation()
    {
        Livewire::test(Register::class)
            ->set([
                'name' => 'John Doe',
                'email' => 'noemailaddress',
                'password' => Str::random(8)
            ])
            ->call('register')->assertHasErrors(['password' => 'confirmed']);
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
        Livewire::test(Register::class)
            ->set([
                'name' => 'John Doe',
                'email' => $this->faker->email,
                'password' => Str::random(7)
            ])
            ->call('register')->assertHasErrors(['password' => 'min']);
    }
}
