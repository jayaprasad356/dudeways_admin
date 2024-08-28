<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_view_profile', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); 
            $table->string('view_user_id'); 
            $table->dateTime('view_datetime'); 
            $table->boolean('status'); 
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
        Schema::dropIfExists('auto_view_profile');
    }
};
