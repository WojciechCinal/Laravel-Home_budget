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
        Schema::create('Savings_plan', function (Blueprint $table) {
            $table->id('id_savings_plan');
            $table->string('name_savings_plan');
            $table->float('goal_savings_plan', 10, 2);
            $table->float('amount_savings_plan', 10, 2);
            $table->date('end_date_savings_plan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Savings_plan');
    }
};
