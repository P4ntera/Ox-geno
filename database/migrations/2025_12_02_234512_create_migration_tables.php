<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Tabla: ars
        |--------------------------------------------------------------------------
        */
        Schema::create('ars', function (Blueprint $table) {
            $table->id('id_ars');
            $table->string('nombre', 50)->nullable();
            $table->decimal('precio_litro', 10, 4)->nullable();
        });

        /*
        |--------------------------------------------------------------------------
        | Tabla: centro_salud
        |--------------------------------------------------------------------------
        */
        Schema::create('centro_salud', function (Blueprint $table) {
            $table->id('id_centro');
            $table->string('nombre_centro', 50)->nullable();
            $table->string('direccion_centro', 200)->nullable();
            $table->string('ciudad_centro', 100)->nullable();
            $table->string('telefono_centro', 10)->nullable();
            $table->integer('pisos')->nullable();
        });

        /*
        |--------------------------------------------------------------------------
        | Tabla: paciente
        |--------------------------------------------------------------------------
        */
        Schema::create('paciente', function (Blueprint $table) {
            $table->id('id_paciente');
            $table->string('nombre_paciente', 50)->nullable();
            $table->string('apellido_paciente', 50)->nullable();
            $table->integer('edad_paciente')->nullable();
            $table->string('especialidad', 50)->nullable();
            $table->string('ubicacion', 20)->nullable();
        });


        /*
        |--------------------------------------------------------------------------
        | Tabla: habitaciones
        |--------------------------------------------------------------------------
        */
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->id('id_habitacion');
            $table->unsignedBigInteger('id_centro')->nullable();
            $table->integer('piso')->nullable();
            $table->string('numero_habitacion', 20)->nullable();

            $table->foreign('id_centro')->references('id_centro')->on('centro_salud');
        });

        /*
        |--------------------------------------------------------------------------
        | Tabla: orden_oxigeno
        |--------------------------------------------------------------------------
        */
        Schema::create('orden_oxigeno', function (Blueprint $table) {
            $table->id('id_orden');
            $table->unsignedBigInteger('id_paciente')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->integer('v3')->nullable();
            $table->string('relacion_ie', 10)->nullable();
            $table->integer('fio2')->nullable();
            $table->integer('tiempo')->nullable();
            $table->unsignedBigInteger('id_ars')->nullable();

            $table->dateTime('fecha_creacion')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('id_paciente')->references('id_paciente')->on('paciente');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->foreign('id_ars')->references('id_ars')->on('ars');
        });

        /*
        |--------------------------------------------------------------------------
        | Tabla: consumo_oxigeno
        |--------------------------------------------------------------------------
        */
        Schema::create('consumo_oxigeno', function (Blueprint $table) {
            $table->id('id_consumo');
            $table->unsignedBigInteger('id_orden')->nullable();
            $table->unsignedBigInteger('id_habitacion')->nullable();
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->decimal('volumen_total_litros', 10, 2)->nullable();
            $table->decimal('costo_total', 10, 2)->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();

            $table->foreign('id_orden')->references('id_orden')->on('orden_oxigeno');
            $table->foreign('id_habitacion')->references('id_habitacion')->on('habitaciones');
            $table->foreign('id_usuario')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumo_oxigeno');
        Schema::dropIfExists('orden_oxigeno');
        Schema::dropIfExists('habitaciones');
        Schema::dropIfExists('paciente');
        Schema::dropIfExists('centro_salud');
        Schema::dropIfExists('ars');
    }
};
