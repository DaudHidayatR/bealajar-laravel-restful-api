<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
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

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::first();

        $this->delete('/api/contacts/'.$contact->id,[],
            [
                'Authorization' => 'test'
            ])->assertStatus(200)
            ->assertJson([
                'data' => 'true'
            ]);
    }
    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::first();

        $this->delete('/api/contacts/'.($contact->id+1),[],
            [
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

    public function testSearchFirstName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=first',
            [
            'Authorization' => 'test',
        ])->assertStatus(200)
        ->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }
    public function testSearchLastName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=last',
            [
                'Authorization' => 'test',
            ])->assertStatus(200)
            ->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }
    public function testSearchEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?email=email',
            [
                'Authorization' => 'test',
            ])->assertStatus(200)
            ->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }
    public function testSearchPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?phone=111111',
            [
                'Authorization' => 'test',
            ])->assertStatus(200)
            ->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }
    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=salah',
            [
                'Authorization' => 'test',
            ])->assertStatus(200)
            ->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, $response['meta']['total']);
    }
    public function testSearchWithPage()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?size=5&page=2',
            [
                'Authorization' => 'test',
            ])->assertStatus(200)
            ->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['current_page']);
    }


}
