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
        Schema::table('CoC', function (Blueprint $table) {
            $table->dropColumn('Address');
            $table->string('Motto')->after('VerificationCode');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('CoC', function (Blueprint $table) {
            $table->date('Address')->after('VerificationCode');
            $table->dropColumn('Motto');
        });
    }
};
