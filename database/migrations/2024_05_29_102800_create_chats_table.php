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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Assuming you have a users table
            $table->string('latest_message'); // You might want to use an enum or a set of predefined values
            $table->timestamp('latest_msg_time'); // Adding the latest message time as a timestamp
            $table->foreignId('chat_user_id')->constrained('users'); // Assuming chat_user_id also references the users table
            $table->boolean('msg_seen')->default(false); // Adding the message seen flag as a boolean
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
        Schema::dropIfExists('chats');
    }
};
