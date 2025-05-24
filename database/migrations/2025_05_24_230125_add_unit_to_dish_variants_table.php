<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitToDishVariantsTable extends Migration
{
    public function up()
    {
        Schema::table('dish_variants', function (Blueprint $table) {
            $table->string('unit', 50)->nullable()->after('price');
        });
    }

    public function down()
    {
        Schema::table('dish_variants', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
    }
}