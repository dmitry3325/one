<?php

use App\Models\Auth\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('auth.users')->insert([
            'firstname' => str_random(10),
            'lastname' => str_random(10),
            'email' => str_random(8).'@gmail.com',
            'password' => bcrypt('secret'),
        ]);

        factory(User::class, 50)->create()->each(function($u) {
            $u->posts()->save(factory(Post::class)->make());
        });
    }
}
