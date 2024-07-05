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
        Schema::create('recharge_trans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('txn_id');
            $table->string('order_id');
            $table->decimal('amount', 8, 2);
            $table->integer('status');
            $table->date('txn_date');
            $table->dateTime('datetime');
            $table->timestamps();

            // Assuming you have a users table and want to enforce referential integrity
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recharge_trans');
    }
};
