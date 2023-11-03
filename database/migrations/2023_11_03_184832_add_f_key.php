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
        // Klucz obcy - User
        Schema::table('User', function (Blueprint $table) {
            $table->unsignedBigInteger('id_role'); // Nowa kolumna klucza obcego
            $table->foreign('id_role')->references('id_role')->on('Role'); // Definicja klucza obcego
        });

        // Klucz obcy - Transaction
        Schema::table('Transaction', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_category');
            $table->unsignedBigInteger('id_subCategory')->nullable();

            $table->foreign('id_user')->references('id_user')->on('User');
            $table->foreign('id_category')->references('id_category')->on('Category');
            $table->foreign('id_subCategory')->references('id_subCategory')->on('SubCategory');
        });

        // Klucz obcy - SubCategory
        Schema::table('SubCategory', function (Blueprint $table) {
            $table->unsignedBigInteger('id_category');
            $table->foreign('id_category')->references('id_category')->on('Category');
        });

        // Klucz obcy - Savings_plan
        Schema::table('Savings_plan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_priority');
            $table->foreign('id_user')->references('id_user')->on('User');
            $table->foreign('id_priority')->references('id_priority')->on('Priority');
        });

        // Klucz obcy - Shopping_list
        Schema::table('Shopping_list', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id_user')->on('User');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Klucz obcy - User
        Schema::table('User', function (Blueprint $table) {
            $table->dropForeign(['id_role']); // Usunięcie klucza obcego
            $table->dropColumn('id_role'); // Usunięcie kolumny
        });

        // Klucz obcy - Transaction
        Schema::table('Transaction', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_category']);
            $table->dropForeign(['id_subCategory']);
            $table->dropColumn(['id_user', 'id_category', 'id_subCategory']);
        });

        // Klucz obcy - SubCategory
        Schema::table('SubCategory', function (Blueprint $table) {
            $table->dropForeign(['id_category']);
            $table->dropColumn('id_category');
        });

        // Klucz obcy - Savings_plan
        Schema::table('Savings_plan', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_priority']);
            $table->dropColumn(['id_user', 'id_priority']);
        });

        // Klucz obcy - Shopping_list
        Schema::table('Shopping_list', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropColumn('id_user');
        });
    }
};
