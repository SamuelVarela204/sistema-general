<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->increments('id_rol');
                $table->string('nombre_rol', 50)->unique();
            });
        }
        DB::table('roles')->insertOrIgnore([
            ['id_rol' => 1, 'nombre_rol' => 'admin'],
            ['id_rol' => 2, 'nombre_rol' => 'cliente'],
            ['id_rol' => 3, 'nombre_rol' => 'inventario'],
            ['id_rol' => 4, 'nombre_rol' => 'gerente'],
        ]);

        if (! Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->increments('id_usu');
                $table->unsignedInteger('id_rol')->default(2);
                $table->string('nom_com', 225);
                $table->string('usu_con', 225);
                $table->mediumBlob('imagen')->nullable();
                $table->string('telefono', 15)->nullable();
                $table->string('correo', 225)->unique();
                $table->string('direccion', 225)->nullable();
                $table->string('alergias', 225)->nullable();
                $table->string('descripcion', 225)->nullable();
                $table->string('estado', 20)->default('activo');
                $table->foreign('id_rol')->references('id_rol')->on('roles')->cascadeOnUpdate();
            });
        }

        if (! Schema::hasTable('producto')) {
            Schema::create('producto', function (Blueprint $table) {
                $table->increments('id_pro');
                $table->string('nom_pro', 225);
                $table->string('descripcion', 100)->nullable();
                $table->decimal('precio', 10, 2);
                $table->unsignedInteger('stock')->default(0);
                $table->string('categoria', 100)->default('General');
            });
        }

        if (! Schema::hasTable('pedido')) {
            Schema::create('pedido', function (Blueprint $table) {
                $table->increments('id_ped');
                $table->unsignedInteger('id_usu');
                $table->dateTime('fecha_pedido')->useCurrent();
                $table->enum('estado', ['pendiente', 'preparando', 'enviado', 'entregado', 'cancelado'])->default('pendiente');
                $table->decimal('total', 10, 2)->default(0);
                $table->foreign('id_usu')->references('id_usu')->on('usuarios')->cascadeOnUpdate();
            });
        }

        if (! Schema::hasTable('detalles_pedido')) {
            Schema::create('detalles_pedido', function (Blueprint $table) {
                $table->increments('id_det_ped');
                $table->unsignedInteger('id_ped');
                $table->unsignedInteger('id_pro');
                $table->unsignedInteger('cantidad');
                $table->decimal('precio_unitario', 10, 2);
                $table->foreign('id_ped')->references('id_ped')->on('pedido')->cascadeOnDelete();
                $table->foreign('id_pro')->references('id_pro')->on('producto');
            });
        }
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach (['detalles_pedido', 'pedido', 'producto', 'usuarios', 'roles'] as $table) Schema::dropIfExists($table);
        Schema::enableForeignKeyConstraints();
    }
};
