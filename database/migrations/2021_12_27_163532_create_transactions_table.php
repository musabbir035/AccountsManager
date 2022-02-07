<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id()->from(10001);
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->foreignId('receipt_id')->nullable()->constrained();
            $table->string('description');
            $table->integer('type');
            $table->decimal('amount');
            $table->date('date');
            $table->foreignId('added_by_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
