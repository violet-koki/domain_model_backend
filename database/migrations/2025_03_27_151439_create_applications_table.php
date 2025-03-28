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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('examine_number')->nullable()->comment('試験番号');
            $table->string('attendance_number')->nullable()->comment('出席番号');
            $table->boolean('pass_flag')->default(false)->comment('合格フラグ');
            $table->boolean('attendance_flag')->default(false)->comment('出席フラグ');
            $table->string('status')->nullable()->comment('申請ステータス');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};