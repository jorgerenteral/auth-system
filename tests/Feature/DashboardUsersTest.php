<?php

namespace Tests\Feature;

use App\Enum\UserStatusEnum;
use App\Http\Livewire\Dashboard\Users;
use App\Http\Livewire\Dashboard\Users\Create;
use App\Http\Livewire\Dashboard\Users\User;
use Tests\TestCase;
use Livewire\Livewire;
use Illuminate\Support\Str;
use App\Http\Livewire\Login;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardUsersTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     * Asserts that the Dashboard / Users component has the "Create Button".
     *
     * @test
     *
     * @return void
     */
    public function has_create_button()
    {
        $password = Str::random(8);
        $user = $this->createUser($password);

        Livewire::test(Login::class)->set([
            'email' => $user->email,
            'password' => $password,
        ])->call('login');

        Livewire::test(Users::class)
            ->assertSeeHtml('<a id="create-user" class="inline-block rounded ml-auto p-2 px-4 bg-green-500 hover:bg-green-700 text-white" href="' . route('dashboard.users.create') . '">+ Create User</a>');
    }

    /**
     * Asserts that the Dashboard / Users / Create component create a new user, emit the 'alert' event and redirects to back route.
     *
     * @test
     *
     * @return void
     */
    public function create_user()
    {
        $user = $this->createUser();

        Livewire::test(Login::class)->set([
            'email' => $user->email,
            'password' => Str::random(8),
        ])->call('login');

        $password = Str::random(8);

        Livewire::test(Create::class)
            ->set([
                'name' => $this->faker->name,
                'email' => $this->faker->email,
                'password' => $password,
                'password_confirmation' => $password,
                'status' => UserStatusEnum::ACTIVE->value,
            ])
            ->call('create')
            ->assertEmitted('alert', 'User created successfully')
            ->assertRedirect();
    }

    /**
     * Asserts that the Dashboard / Users / Create component can't create a new user with a registered email address, emit the 'alert' event.
     *
     * @test
     *
     * @return void
     */
    public function cant_create_user_with_registered_email()
    {
        $user = $this->createUser();

        Livewire::test(Login::class)->set([
            'email' => $user->email,
            'password' => Str::random(8),
        ])->call('login');

        $password = Str::random(8);

        Livewire::test(Create::class)
            ->set([
                'name' => $this->faker->name,
                'email' => $user->email,
                'password' => $password,
                'password_confirmation' => $password,
                'status' => UserStatusEnum::ACTIVE->value,
            ])
            ->call('create')
            ->assertEmitted('alert', 'Email is already registered');
    }

    /**
     * Asserts that the Dashboard / Users / {user} component update an user, emit the 'alert' event.
     *
     * @test
     *
     * @return void
     */
    public function update_user()
    {
        $currentUser = $this->createUser();

        Livewire::test(Login::class)->set([
            'email' => $currentUser->email,
            'password' => Str::random(8),
        ])->call('login');

        $user = $this->createUser();

        Livewire::test(User::class, ['user' => $user->id])
            ->set([
                'currentUser' => $currentUser,
                'email' => $user->email,
                'status' => UserStatusEnum::SUSPENDED->value,
            ])
            ->call('update')
            ->assertEmitted('alert', 'User updated successfully');
    }

    /**
     * Asserts that the Dashboard / Users / {user} component can't update an user with an already registered email, emit the 'alert' event.
     *
     * @test
     *
     * @return void
     */
    public function cant_update_user_with_registered_email()
    {
        $currentUser = $this->createUser();

        Livewire::test(Login::class)->set([
            'email' => $currentUser->email,
            'password' => Str::random(8),
        ])->call('login');

        $user = $this->createUser();

        Livewire::test(User::class, ['user' => $user->id])
            ->set([
                'currentUser' => $currentUser,
                'email' => $currentUser->email,
                'status' => UserStatusEnum::SUSPENDED->value,
            ])
            ->call('update')
            ->assertEmitted('alert', 'Email is already registered, and is not yours');
    }

    /**
     * Asserts that the Dashboard / Users / {user} component can't update the current user, emit the 'alert' event.
     *
     * @test
     *
     * @return void
     */
    public function cant_update_current_user()
    {
        $currentUser = $this->createUser();

        Livewire::test(Login::class)->set([
            'email' => $currentUser->email,
            'password' => Str::random(8),
        ])->call('login');

        Livewire::test(User::class, ['user' => $currentUser->id])
            ->set([
                'currentUser' => $currentUser,
                'email' => $this->faker->email,
                'status' => UserStatusEnum::SUSPENDED->value,
            ])
            ->call('update')
            ->assertEmitted('alert', 'You cannot edit your own account.');
    }

    /**
     * Asserts that the Dashboard / Users / {user} with empty form, has required errors.
     *
     * @test
     *
     * @return void
     */
    public function with_empty_form()
    {
        $currentUser = $this->createUser();

        Livewire::test(Login::class)->set([
            'email' => $currentUser->email,
            'password' => Str::random(8),
        ])->call('login');

        $user = $this->createUser();

        Livewire::test(User::class, ['user' => $user->id])
            ->set([
                'currentUser' => $currentUser,
                'email' => null,
                'status' => null,
            ])
            ->call('update')
            ->assertHasErrors(['email' => 'required', 'status' => 'required']);
    }

    /**
     * Asserts that the Dashboard / Users / {user} when malformed email, email key has email errors.
     *
     * @test
     *
     * @return void
     */
    public function with_malformed_email()
    {
        $currentUser = $this->createUser();

        Livewire::test(Login::class)->set([
            'email' => $currentUser->email,
            'password' => Str::random(8),
        ])->call('login');

        $user = $this->createUser();

        Livewire::test(User::class, ['user' => $user->id])
            ->set([
                'currentUser' => $currentUser,
                'email' => 'malformedemail',
            ])
            ->call('update')
            ->assertHasErrors(['email' => 'email']);
    }

    /**
     * Asserts that the Dashboard / Users / {user} when invalid status, status key has in errors.
     *
     * @test
     *
     * @return void
     */
    public function with_invalid_status()
    {
        $currentUser = $this->createUser();

        Livewire::test(Login::class)->set([
            'email' => $currentUser->email,
            'password' => Str::random(8),
        ])->call('login');

        $user = $this->createUser();

        Livewire::test(User::class, ['user' => $user->id])
            ->set([
                'currentUser' => $currentUser,
                'status' => 'Some Status',
            ])
            ->call('update')
            ->assertHasErrors(['status' => 'in']);
    }
}
