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
    }

    public function testGetSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->get('api/users/current',[
            'Authorization' => 'test'
            ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'username' => 'SagAsh',
                'name' => 'Daud Hidayat Ramadhan',
            ],
        ]);
    }
    public function testGetUnAuthorized()
    {
        $this->seed(UserSeeder::class);
        $this->get('api/users/current',[
            'Authorization' => ''
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }
    public function testGetInvalidToken()
    {
        $this->seed(UserSeeder::class);
        $this->get('api/users/current',[
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed(UserSeeder::class);
        $oldUser = User::where('username', 'SagAsh')->first();
        $this->patch('api/users/current',
            [
                'password' => 'new',
            ],
            [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'SagAsh',
                    'name' => 'Daud Hidayat Ramadhan',
                ],
            ]);
        $newUser = User::where('username', 'SagAsh')->first();
        self::assertNotEquals($oldUser->password,$newUser->password);
    }
    public function testUpdateAPasswordSuccess()
    {
        $this->seed(UserSeeder::class);
        $oldUser = User::where('username', 'SagAsh')->first();
        $this->patch('api/users/current',
            [
                'name' => 'daud',
            ],
            [
                'Authorization' => 'test'
            ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'SagAsh',
                    'name' => 'daud',
                ],
            ]);
        $newUser = User::where('username', 'SagAsh')->first();
        self::assertNotEquals($oldUser->name,$newUser->name);
    }
    public function testUpdateFailed()
    {
        $this->seed(UserSeeder::class);
        $this->patch('api/users/current',
            [
                'name' => 'dauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddauddaudv',
            ],
            [
                'Authorization' => 'test'
            ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        "The name field must not be greater than 100 characters."
                    ],
                ],
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->delete( 'api/users/logout', headers:[
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
        $user = User::where('username', 'SagAsh')->first();
        self::assertNull($user->token);
    }
    public function testLogoutFailed()
    {
        $this->seed(UserSeeder::class);
        $this->delete('api/users/logout', [
            'Authorization' => ''
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

}

