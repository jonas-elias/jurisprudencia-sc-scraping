<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateJurisprudenciaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jurisprudencia', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('processo')->nullable(true);
            $table->string('relator')->nullable(true);
            $table->string('origem')->nullable(true);
            $table->string('orgao_julgador')->nullable(true);
            $table->string('julgado_em')->nullable(true);
            $table->string('classe')->nullable(true);
            $table->string('texto', 10485760)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurisprudencia');
    }
}
