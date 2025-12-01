<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateHoldsTable extends Migration {
    public function up(){
        Schema::create('holds', function(Blueprint $table){
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('qty')->unsigned();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('used')->default(false);
            $table->boolean('released')->default(false);
            $table->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('holds'); }
}
