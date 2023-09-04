<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
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

    public function testGetSuccess()
    {
        $this->seed([
            UserSeeder::class,
            ContactSeeder::class,
            AddressSeeder::class
        ]);
        $address = Address::first();
        $this->get('/api/contacts/'.$address->contact_id.'/addresses/'.$address->id, [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' =>
                [
                    'street' => 'Candi Sumberadi',
                    'state' => 'Yogyakarta',
                    'city' => 'Sleman',
                    'country' => 'Indonesia',
                    'postal_code' => '1234',
                ]
            ]);
    }
    public function testGetFailed()
    {
        $this->seed([
            UserSeeder::class,
            ContactSeeder::class,
            AddressSeeder::class
        ]);
        $address = Address::first();
        $this->get('/api/contacts/'.$address->contact_id.'/addresses/'.$address->id)
            ->assertStatus(401)
            ->assertJson([
                'errors' =>
                [
                    'message' => [
                        "Unauthorized"
                    ]
                ]
            ]);
    }
    public function testGetNotFound()
    {
        $this->seed([
            UserSeeder::class,
            ContactSeeder::class,
            AddressSeeder::class
        ]);
        $address = Address::first();
        $this->get('/api/contacts/'.$address->contact_id.'/addresses/'.($address->id+1), [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' =>
                    [
                        'message' => [
                            "Address not found"
                        ]
                    ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([
            UserSeeder::class,
            ContactSeeder::class,
            AddressSeeder::class
        ]);
        $address = Address::first();
        $this->put('api/contacts/'.$address->contact_id.'/addresses/'.$address->id,
            [
                'street' => 'test',
                'state' => 'Yogyakarta',
                'city' => 'Sleman',
                'country' => 'Indonesia',
                'postal_code' => '1234',
            ]
            ,[
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' =>
                    [
                        'street' => 'test',
                        'state' => 'Yogyakarta',
                        'city' => 'Sleman',
                        'country' => 'Indonesia',
                        'postal_code' => '1234',
                    ]
            ]);
    }
    public function testUpdateFailed()
    {
        $this->seed([
            UserSeeder::class,
            ContactSeeder::class,
            AddressSeeder::class
        ]);
        $address = Address::first();
        $this->put('api/contacts/'.$address->contact_id.'/addresses/'.$address->id,
            [
                'street' => 'test',
                'state' => 'Yogyakarta',
                'city' => 'Sleman',
                'country' => '',
                'postal_code' => '1234',
            ]
            ,[
                'Authorization' => 'test'
            ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                        'country' => [
                            "The country field is required."
                        ]
                    ]
            ]);
    }
    public function testUpdateNotFound()
    {
        $this->seed([
            UserSeeder::class,
            ContactSeeder::class,
            AddressSeeder::class
        ]);
        $address = Address::first();
        $this->put('api/contacts/'.$address->contact_id.'/addresses/'.($address->id+1),
            [
                'street' => 'test',
                'state' => 'Yogyakarta',
                'city' => 'Sleman',
                'country' => 'Indonesia',
                'postal_code' => '1234',
            ]
            ,[
                'Authorization' => 'test'
            ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        "Address not found"
                    ]
                ]
            ]);
    }

    public function testRemoveSuccess()
    {
        $this->seed([
            UserSeeder::class,
            ContactSeeder::class,
            AddressSeeder::class
        ]);
        $address = Address::first();
        $this->delete('api/contacts/'.$address->contact_id.'/addresses/'.$address->id,
            [
            ]
            ,[
                'Authorization' => 'test'
            ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }
    public function testRemoveNotFound()
    {
        $this->seed([
            UserSeeder::class,
            ContactSeeder::class,
            AddressSeeder::class
        ]);
        $address = Address::first();
        $this->delete('api/contacts/'.$address->contact_id.'/addresses/'.($address->id+1),
            [
            ]
            ,[
                'Authorization' => 'test'
            ])->assertStatus(404)
            ->assertJson([
                'errors'=>
                    [
                        'message' => [
                            "Address not found"
                        ]
                    ]
            ]);
    }


}
