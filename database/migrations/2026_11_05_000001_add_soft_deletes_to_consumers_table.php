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
        Schema::table('consumers', function (Blueprint $table) {
            if (!Schema::hasColumn('consumers', 'deleted_at')) {
                $table->softDeletes()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumers', function (Blueprint $table) {
            if (Schema::hasColumn('consumers', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};

