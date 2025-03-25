<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mail', 256);
            $table->string('name_kana', 32);
            $table->smallInteger('gender');
            $table->date('birthday');
            $table->string('work_name', 256)->nullable();
            $table->string('work_zipcode')->nullable();
            $table->smallInteger('work_prefecture')->nullable();
            $table->string('work_address1', 256)->nullable();
            $table->string('work_address2', 256)->nullable();
            $table->string('work_building', 256)->nullable();
            $table->string('work_section', 64)->nullable();
            $table->string('work_phone', 32)->nullable();
            $table->boolean('send_flag')->nullable()->default(false);
            $table->string('zipcode')->nullable();
            $table->smallInteger('prefecture')->nullable();
            $table->string('address1', 256)->nullable();
            $table->string('address2', 256)->nullable();
            $table->string('building', 256)->nullable();
            $table->smallInteger('status')->default(0);
            $table->string('certification_number', 16)->nullable();
            $table->date('certification_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->smallInteger('exp_edu')->default(0);
            $table->string('temp_mail', 256)->nullable();
            $table->string('certification_text', 256)->nullable();
            $table->timestamp('password_updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            Schema::dropIfExists('users');
        });
    }
};
