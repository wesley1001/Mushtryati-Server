<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->text('caption');
            $table->enum('type',['image','video']);
            $table->text('large_url')->nullable();
            $table->text('medium_url')->nullable();
            $table->text('thumb_url')->nullable();
            $table->text('video_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('medias');
    }
}
