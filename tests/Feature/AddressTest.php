<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateSuccess(): void
    {
        $this->seed([
            UserSeeder::class,
            ContactSeeder::class,
        ]);
        $contact = Contact::first();
        $this->post('/api/contacts/'.$contact->id.'/addresses', [
            'street' => 'Candi Sumberadi',
            'state' => 'Yogyakarta',
            'city' => 'Sleman',
            'country' => 'indonesia',
            'postal_code' => '1234',
        ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(201)
            ->assertJson([
                'data' => [
                    'street' => 'Candi Sumberadi',
                    'state' => 'Yogyakarta',
                    'city' => 'Sleman',
                    'country' => 'indonesia',
                    'postal_code' => '1234',
                ]
            ]);

    }
    public function testCreateFailed(): void
    {
        $this->seed([
            UserSeeder::class,
            ContactSeeder::class,
        ]);
        $contact = Contact::first();
        $this->post('/api/contacts/'.$contact->id.'/addresses', [
            'street' => 'Candi Sumberadi',
            'state' => 'Yogyakarta',
            'city' => 'Sleman',
            'country' => '',
            'postal_code' => '',
        ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors'=> [
                    'country' => [
                        'The country field is required.'
                    ]
                ]
            ]);
    }
    public function testCreateContactNotFound(): void
    {
        $this->seed([
            UserSeeder::class,
            ContactSeeder::class,
        ]);
        $contact = Contact::first();
        $this->post('/api/contacts/'.($contact->id+1).'/addresses', [
            'street' => 'Candi Sumberadi',
            'state' => 'Yogyakarta',
            'city' => 'Sleman',
            'country' => 'Indonesia',
            'postal_code' => '1234',
        ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors'=> [
                    'message' => [
                        'Contact not found'
                    ]
                ]
            ]);
    }
}
