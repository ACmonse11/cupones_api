<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Schema::table('coupons', function (Blueprint $table) {
        //     $table->string('image')->nullable()->after('status'); // 🖼️ campo para guardar la URL o nombre de la imagen
        // });
    }

    public function down(): void
    {
        // Schema::table('coupons', function (Blueprint $table) {
        //     $table->dropColumn('image');
        // });
    }
};
