<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration {
    public function up(){
        Schema::create('orders', function(Blueprint $table){
            $table->id();
            $table->foreignId('hold_id')->constrained('holds')->cascadeOnDelete();
            $table->enum('status', ['pending','paid','cancelled'])->default('pending');
            $table->string('payment_ref')->nullable()->unique();
            $table->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('orders'); }
}
