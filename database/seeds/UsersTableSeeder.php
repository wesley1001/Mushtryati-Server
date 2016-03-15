<?php

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
        factory(App\Src\User\User::class, 1)->create(['email'=>'admin@test.com','admin'=>1]);
        factory(App\Src\User\User::class, 20)->create();

        $userArray = App\Src\User\User::lists('id')->toArray();
        $users =  App\Src\User\User::all();

        foreach($users as $user) {
            $user->followers()->sync([$userArray[array_rand($userArray)]]);
            $user->following()->sync([$userArray[array_rand($userArray)]]);
        }
    }
}
