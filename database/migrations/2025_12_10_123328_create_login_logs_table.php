<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('login_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('client_id')->nullable();
        $table->unsignedBigInteger('client_user_id')->nullable();

        $table->string('ip_address')->nullable();
        $table->string('user_agent')->nullable();
        $table->timestamp('logged_in_at')->useCurrent();

        $table->timestamps();

        $table->foreign('client_id')
            ->references('id')
            ->on('clients')
            ->onDelete('cascade');

        $table->foreign('client_user_id')
            ->references('id')
            ->on('client_users')
            ->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
