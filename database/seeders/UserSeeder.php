<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'user@user.com'
            ],
            [
                'name' => 'Usuario del sistema',
                'email' => 'user@user.com',
                'password' => '$2y$10$eb7p9MZbxa2SrQZfV4r4dueqK6ezOQLx13D3hMu4GcxKtkh5KDXte'
            ]
        );
    }
}
