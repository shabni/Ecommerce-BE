<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('quantity'); // Item price
            $table->decimal('discount', 5, 2)->default(0)->after('price');  // Discount in percentage
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['price', 'discount']);
        });
    }
};
