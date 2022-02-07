<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptItemsTable extends Migration
{
    public function up()
    {
        Schema::create('receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('receipt_id')->constrained();
            $table->integer('quantity');
            $table->decimal('total');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('receipt_items');
    }
}
