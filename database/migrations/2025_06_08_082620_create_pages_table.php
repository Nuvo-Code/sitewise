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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->string('slug');
            $table->string('title');
            $table->enum('response_type', ['html', 'markdown', 'json'])->default('html');
            $table->longText('html_content')->nullable();
            $table->longText('markdown')->nullable();
            $table->json('json_content')->nullable();
            $table->foreignId('template_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['site_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
