<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['Public', 'Private'])->default('Public');
            $table->enum('progress', ['New', 'In progress', 'Completed', 'Pause', 'Canceled'])->default('New');
            $table->unsignedBigInteger('todo_list_id');
            $table->timestamps();

            // Обратите внимание на имя таблицы 'todo_lists', а не 'todolists'
            $table->foreign('todo_list_id')->references('id')->on('todo_lists')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
