<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateSuccess(): void
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/contacts', [
            'first_name' => 'daud',
            'last_name' => 'ramadhan',
            'email' => 'daud28ramadhan@gmail.com',
            'phone' => '081234567890',
            ],[
                'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            'data' => [
                'first_name' => 'daud',
                'last_name' => 'ramadhan',
                'email' => 'daud28ramadhan@gmail.com',
                'phone' => '081234567890',
                ]
        ]);
    }

    public function testCreateFailed()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'ramadhan',
            'email' => 'daud28ramadhan',
            'phone' => '081234567890',
        ],[
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ],
                    'email' => [
                        'The email field must be a valid email address.'
                    ]
                ]
            ]);
    }
    public function testCreateUnAuthorized()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'ramadhan',
            'email' => 'daud28ramadhan',
            'phone' => '081234567890',
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthenticated.'
                    ]
                ]
            ]);
    }

}
