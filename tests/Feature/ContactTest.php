<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
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
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }
    public function testGetSuccess()
    {
        $this->seed([
            UserSeeder::class,
            ContactSeeder::class
        ]);
        $contact = Contact::first();
        $this->get('/api/contacts/'.$contact->id, [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'Daud',
                    'last_name' => 'Ramadhan',
                    'email' => 'daud28ramadhan@gmail.com',
                    'phone' => '081234567890',
                ]
            ]);

    }

    public function testNotFound()
    {
        $this->seed([
            UserSeeder::class,
            ContactSeeder::class
        ]);
        $contact = Contact::first();
        $this->get('/api/contacts/'.($contact->id+1), [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Contact not found'
                        ]
                ]
            ]);
    }
    public function testGetOtherUserContact()
    {
        $this->seed([
            UserSeeder::class,
            ContactSeeder::class
        ]);
        $contact = Contact::first();
        $this->get('/api/contacts/'.$contact->id, [
            'Authorization' => 'test2'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Contact not found'
                    ]
                ]
            ]);
    }
    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::first();

        $this->put('/api/contacts/'.$contact->id, [
            'first_name' => 'Daud',
            'last_name' => 'Ramadhan',
            'email' => 'baru28@gmail.com',
            'phone' => '12233455'
        ],
            [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'Daud',
                    'last_name' => 'Ramadhan',
                    'email' => 'baru28@gmail.com',
                    'phone' => '12233455'
                ]
            ]);
    }
    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::first();

        $this->put('/api/contacts/'.$contact->id, [
            'first_name' => '',
            'last_name' => 'Ramadhan',
            'email' => 'baru28@gmail.com',
            'phone' => '12233455'
        ],
            [
                'Authorization' => 'test'
            ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        "The first name field is required."
                    ]
                ]
            ]);
    }

}
