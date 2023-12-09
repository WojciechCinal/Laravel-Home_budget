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
        // Klucz obcy - Users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_role'); // Nowa kolumna klucza obcego
            $table->foreign('id_role')->references('id_role')->on('roles'); // Definicja klucza obcego
        });

        // Klucz obcy - Transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_category');
            $table->unsignedBigInteger('id_subCategory')->nullable();

            $table->foreign('id_user')->references('id_user')->on('users');
            $table->foreign('id_category')->references('id_category')->on('categories');
            $table->foreign('id_subCategory')->references('id_subCategory')->on('sub_categories');
        });

        // Klucz obcy - Categories
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id_user')->on('users');
        });

        // Klucz obcy - SubCategories
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('id_category');
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id_user')->on('users');
            $table->foreign('id_category')->references('id_category')->on('categories');
        });

        // Klucz obcy - Savings_plans
        Schema::table('savings_plans', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_priority');
            $table->foreign('id_user')->references('id_user')->on('users');
            $table->foreign('id_priority')->references('id_priority')->on('priorities');
        });

        // Klucz obcy - Shopping_lists
        Schema::table('shopping_lists', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id_user')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Klucz obcy - Users
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_role']); // Usunięcie klucza obcego
            $table->dropColumn('id_role'); // Usunięcie kolumny
        });

        // Klucz obcy - Transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_category']);
            $table->dropForeign(['id_subCategory']);
            $table->dropColumn(['id_user', 'id_category', 'id_subCategory']);
        });

        // Klucz obcy - Categories
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropColumn('id_user');
        });

        // Klucz obcy - SubCategories
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropForeign(['id_category']);
            $table->dropColumn('id_category');

            $table->dropForeign(['id_user']);
            $table->dropColumn('id_user');
        });

        // Klucz obcy - Savings_plans
        Schema::table('savings_plans', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_priority']);
            $table->dropColumn(['id_user', 'id_priority']);
        });

        // Klucz obcy - Shopping_list
        Schema::table('shopping_lists', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropColumn('id_user');
        });
    }
};
