<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('logos', function (Blueprint $table) {
            $table->boolean('active')->default(true)->after('image_url');
        });
    }

    public function down()
    {
        Schema::table('logos', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
};
