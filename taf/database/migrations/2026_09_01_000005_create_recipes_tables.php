<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de recetas
        if (! Schema::hasTable('recetas')) {
            Schema::create('recetas', function (Blueprint $table) {
                $table->increments('id_receta');
                $table->string('nombre_receta', 225)->unique();
                $table->text('descripcion')->nullable();
                $table->decimal('precio_base', 10, 2)->default(0);
                $table->boolean('personalizable')->default(true);
                $table->string('estado', 20)->default('activo');
                $table->unsignedInteger('id_usu_creador');
                $table->timestamp('fecha_creacion')->useCurrent();
                $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();
                $table->foreign('id_usu_creador')->references('id_usu')->on('usuarios')->cascadeOnUpdate();
            });
        }

        // Tabla de ingredientes (insumos de producción)
        if (! Schema::hasTable('ingredientes')) {
            Schema::create('ingredientes', function (Blueprint $table) {
                $table->increments('id_ingrediente');
                $table->string('nombre_ingrediente', 225)->unique();
                $table->string('tipo', 50);
                $table->text('descripcion')->nullable();
                $table->string('unidad_medida', 20);
                $table->decimal('costo_unitario', 10, 2)->default(0);
                $table->unsignedInteger('stock_actual')->default(0);
                $table->unsignedInteger('stock_minimo')->default(10);
                $table->string('estado', 20)->default('activo');
            });
        }

        // Tabla de detalles de receta (receta + ingredientes)
        if (! Schema::hasTable('detalles_receta')) {
            Schema::create('detalles_receta', function (Blueprint $table) {
                $table->increments('id_detalle_receta');
                $table->unsignedInteger('id_receta');
                $table->unsignedInteger('id_ingrediente');
                $table->decimal('cantidad_requerida', 10, 2);
                $table->string('unidad_medida', 20);
                $table->foreign('id_receta')->references('id_receta')->on('recetas')->cascadeOnDelete();
                $table->foreign('id_ingrediente')->references('id_ingrediente')->on('ingredientes');
            });
        }

        // Tabla de movimientos de inventario (auditoría)
        if (! Schema::hasTable('movimientos_inventario')) {
            Schema::create('movimientos_inventario', function (Blueprint $table) {
                $table->increments('id_movimiento');
                $table->unsignedInteger('id_ingrediente');
                $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste', 'merma']);
                $table->unsignedInteger('cantidad');
                $table->string('descripcion', 225)->nullable();
                $table->unsignedInteger('id_usu_responsable');
                $table->dateTime('fecha_movimiento')->useCurrent();
                $table->foreign('id_ingrediente')->references('id_ingrediente')->on('ingredientes');
                $table->foreign('id_usu_responsable')->references('id_usu')->on('usuarios')->cascadeOnUpdate();
            });
        }

        // Tabla de ventas
        if (! Schema::hasTable('ventas')) {
            Schema::create('ventas', function (Blueprint $table) {
                $table->increments('id_venta');
                $table->unsignedInteger('id_usu_cajero');
                $table->decimal('total', 10, 2)->default(0);
                $table->decimal('descuento', 10, 2)->default(0);
                $table->string('metodo_pago', 50)->default('efectivo');
                $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('completada');
                $table->dateTime('fecha_venta')->useCurrent();
                $table->foreign('id_usu_cajero')->references('id_usu')->on('usuarios')->cascadeOnUpdate();
            });
        }

        // Tabla de detalles de venta
        if (! Schema::hasTable('detalles_venta')) {
            Schema::create('detalles_venta', function (Blueprint $table) {
                $table->increments('id_detalle_venta');
                $table->unsignedInteger('id_venta');
                $table->unsignedInteger('id_receta');
                $table->unsignedInteger('cantidad');
                $table->decimal('precio_unitario', 10, 2);
                $table->decimal('subtotal', 10, 2);
                $table->text('personalizacion')->nullable();
                $table->foreign('id_venta')->references('id_venta')->on('ventas')->cascadeOnDelete();
                $table->foreign('id_receta')->references('id_receta')->on('recetas');
            });
        }

        // Tabla de alertas de stock
        if (! Schema::hasTable('alertas_stock')) {
            Schema::create('alertas_stock', function (Blueprint $table) {
                $table->increments('id_alerta');
                $table->unsignedInteger('id_ingrediente');
                $table->unsignedInteger('stock_actual');
                $table->unsignedInteger('stock_minimo');
                $table->enum('estado_alerta', ['activa', 'resuelta'])->default('activa');
                $table->dateTime('fecha_alerta')->useCurrent();
                $table->dateTime('fecha_resolucion')->nullable();
                $table->foreign('id_ingrediente')->references('id_ingrediente')->on('ingredientes');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas_stock');
        Schema::dropIfExists('detalles_venta');
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('movimientos_inventario');
        Schema::dropIfExists('detalles_receta');
        Schema::dropIfExists('ingredientes');
        Schema::dropIfExists('recetas');
    }
};
