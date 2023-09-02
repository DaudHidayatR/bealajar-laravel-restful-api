<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'SagAsh')->first();
        Contact::create([
            'first_name' => 'Daud',
            'last_name' => 'Ramadhan',
            'email' => 'daud28ramadhan@gmail.com',
            'phone' => '081234567890',
            'user_id' => $user->id,
        ]);
    }
}
