<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testRegisterSuccess(): void
    {
        $this->post('/api/users', [
            'username' => 'SagAsh',
            'password' => 'Daud123',
            'name' => 'Daud Hidayat Ramadhan',
        ])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => 'SagAsh',
                    'name' => 'Daud Hidayat Ramadhan',
                ],
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => '',
        ])
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field is required.',
                    ],
                    'password' => [
                        'The password field is required.',
                    ],
                    'username' => [
                        'The username field is required.',
                    ],
                ],
            ]);
    }

    public function testRegisterUsernameAlreadyExists()
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => 'SagAsh',
            'password' => 'Daud123',
            'name' => 'Daud Hidayat Ramadhan',
        ])
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        'Username already registered',
                    ],
                ],
            ]);
    }
    public function testLoginSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'username' => 'SagAsh',
            'password' => 'Daud123',
            'name' => 'Daud Hidayat Ramadhan',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'SagAsh',
                    'name' => 'Daud Hidayat Ramadhan',

                ],
            ]);
        $user = User::where('username', 'SagAsh')->first();
        self::assertNotNull($user->token);


    }
    public function testLoginFailedUsernameNotFound()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'username' => 'salah',
            'password' => 'Daud1234',
            'name' => 'Daud Hidayat Ramadhan',
        ])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Username or password is wrong"
                    ]
                ]
            ]);
        $user = User::where('username', 'SagAsh')->first();
        self::assertNull($user->token);
    }
    public function testLoginFailedPasswordIsWrong()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'username' => 'SagAsh',
            'password' => 'salah',
            'name' => 'Daud Hidayat Ramadhan',
        ])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Username or password is wrong"
                    ]
                ]
            ]);
        $user = User::where('username', 'SagAsh')->first();
        self::assertNull($user->token);
    }
}

