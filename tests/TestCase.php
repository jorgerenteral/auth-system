<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use WithFaker;

    /**
     * Create a new User
     */
    public function createUser($password = null)
    {
        $email = $this->faker->email;

        $user = User::create([
            'name' => $this->faker->name,
            'email' => $email,
            'password' => Hash::make($password ?? Str::random(8))
        ]);

        return $user;
    }

    /**
     * Create a new User and Send the Renew Password Email
     */
    public function createUserAndSendEmail()
    {
        $user = $this->createUser();

        $user->sendForgotPasswordNotification();

        return $user;
    }
}
