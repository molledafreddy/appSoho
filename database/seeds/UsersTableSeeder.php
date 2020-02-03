<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'Admin';
        $user->email = 'admin@admin.com';
        $user->username = 'userAdmin';
        $user->password = bcrypt('123456');
        $user->remember_token = Str::random(10);
        $user->email_verified_at = now();
        $user->save();
    }
}
