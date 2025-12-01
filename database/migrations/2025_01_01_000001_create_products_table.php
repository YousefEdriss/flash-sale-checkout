<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration {
    public function up(){
        Schema::create('products', function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->integer('stock')->unsigned();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('products'); }
}
