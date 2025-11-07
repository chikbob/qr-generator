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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('subject'); // тема обращения
            $table->text('message'); // само сообщение
            $table->string('category')->default('general'); // категория, например "Ошибка", "Идея", "Другое"
            $table->enum('status', ['new', 'in_progress', 'resolved'])->default('new');
            $table->enum('priority', ['low', 'medium', 'high'])->default('low'); // вычисляется по тарифу
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
