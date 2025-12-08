<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_assigned_folders', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('client_user_id');
            $table->unsignedBigInteger('folder_id');

            $table->timestamps();

            // FK constraints
            $table->foreign('client_user_id')
                ->references('id')
                ->on('client_users')
                ->onDelete('cascade');

            $table->foreign('folder_id')
                ->references('id')
                ->on('folders')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_assigned_folders');
    }
};
