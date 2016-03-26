<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private $tables = [
        'users',
        'password_resets',
        'medias',
        'favorites',
        'downloads',
        'followers',
        'comments',
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->truncateDatabaseTables();
        $this->call(UsersTableSeeder::class);
        $this->call(MediasTableSeeder::class);
        $this->call(CommentsTableSeeder::class);
        Model::reguard();

    }

    private function truncateDatabaseTables()
    {
        foreach ($this->tables as $table) {
            DB::table($table)->truncate();
        }
    }

}
