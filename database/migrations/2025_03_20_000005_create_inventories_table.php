<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration
{
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('unit', 20); // kg, g, l, ml, etc.
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('min_quantity', 10, 2)->default(0);
            $table->decimal('cost_per_unit', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventories');
    }
}