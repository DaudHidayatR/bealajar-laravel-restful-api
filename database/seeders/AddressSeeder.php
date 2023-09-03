<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $contact = Contact::first();
        Address::create([
            'contact_id' => $contact->id,
            'street' => 'Candi Sumberadi',
            'state' => 'Yogyakarta',
            'city' => 'Sleman',
            'country' => 'Indonesia',
            'postal_code' => '1234',
        ]);
    }
}
