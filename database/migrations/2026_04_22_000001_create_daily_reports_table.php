<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->string('project');
            $table->date('report_date')->default(now());
            $table->integer('quality_rating')->default(3);
            $table->integer('spirit_rating')->default(3);
            $table->text('notes')->nullable();
            $table->boolean('submit_to_gform')->default(true);
            $table->string('gform_response_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
