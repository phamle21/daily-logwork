<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->string('description');
            $table->integer('progress')->default(0);
            $table->date('expected_date')->nullable();
            $table->enum('task_type', ['today', 'tomorrow'])->default('today');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_tasks');
    }
};
