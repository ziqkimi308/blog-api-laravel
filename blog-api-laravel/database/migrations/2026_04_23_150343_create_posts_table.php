<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

			$table->string('title');
			$table->string('slug')->unique()->nullable();
			$table->text('excerpt')->nullable();
			$table->longText('content');
			$table->string('featured_image')->nullable();
			$table->enum('status', ['draft','published'])->default('draft');
			$table->timestamp('published_at')->nullable();
			$table->unsignedBigInteger('views')->default(0);
			$table->softDeletes();

			// foreign relationships
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->foreignId('category_id')->constrained()->onDelete('set null');

			// Index
			$table->index('slug');
			$table->index('status');
			$table->index('published_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
