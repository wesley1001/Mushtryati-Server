<?php

use Illuminate\Database\Seeder;

class MediasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userArray = App\Src\User\User::lists('id')->toArray();
        factory(App\Src\Media\Media::class, 50)->create()->each(function($media) use ($userArray) {
            $media->favorites()->sync([$userArray[array_rand($userArray)]]);
            $media->downloads()->sync([$userArray[array_rand($userArray)]]);

        });
    }
}
