<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsWebForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results_web_forms', function (Blueprint $table) {
            $table->id();
            $table->biginteger('web_form_id')->default(null)->nullable()->comment('ИД формы');
            $table->string('name',1024)->nullable()->default('')->comment('Наименование формы');
            $table->json('body')->default(null)->nullable()->comment('Содержание формы');
            $table->json('results')->default(null)->nullable()->comment('Результаты');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('results_web_forms');
    }
}
