<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->text('content');
            $table->string('image')->nullable();
            $table->unsignedBigInteger('author_id');
            $table->foreign('author_id')->references('id')->on('users')->onDelete('restrict');
            $table->unsignedBigInteger('tag_id')->nullable();
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('restrict');
            $table->enum('status', ['Draft', 'Pending', 'Published', 'Rejected'])->default('Draft');
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
