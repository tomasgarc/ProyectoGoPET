<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('location')->nullable()->after('role');
        });

        Schema::table('care_requests', function (Blueprint $table) {
            // Using a default value here ensures existing rows are populated with it automatically
            $table->string('location')->default('El Puerto de Santa María')->after('description');
        });

        // Set all existing users to 'El Puerto de Santa María'
        DB::table('users')->update(['location' => 'El Puerto de Santa María']);
        // Ensure all existing requests are also 'El Puerto de Santa María' (already covered by default, but to be sure)
        DB::table('care_requests')->update(['location' => 'El Puerto de Santa María']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('care_requests', function (Blueprint $table) {
            $table->dropColumn('location');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
};
