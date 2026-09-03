# Implementación de Alergias, Ingredientes y Detalles de Productos

## Estado de Implementación ✅

Este documento describe las nuevas funciones agregadas al sistema TAF2 integrado.

---

## 1. GESTIÓN DE ALERGIAS (COMPLETADA ✅)

### Descripción
Los usuarios ahora pueden especificar las frutas a las que son alérgicos, permitiendo personalizaciones futuras en recomendaciones de productos.

### Ubicación
- **Interfaz de usuario**: `views/taf2/alergias.php`
- **Funciones backend**: `includes/taf2/controlador.php`
- **Procesamiento**: `index.php` (acciones: `agregar_alergia`, `remover_alergia`)

### Características
✅ Ver alergias actuales como etiquetas rojas removibles
✅ Agregar nuevas alergias desde lista de frutas disponibles
✅ Remover alergias con botón ✕
✅ Ver estado de todas las frutas (alérgena o segura)
✅ Interfaz responsiva y amigable

### Cómo Acceder
1. Inicia sesión como cliente (cualquier usuario que no sea admin)
2. En el menú lateral, ve a **"Mis Alergias"**
3. O accede directamente a: `index.php?page=taf2&view=alergias`

### Tabla de Base de Datos Necesaria
```sql
CREATE TABLE IF NOT EXISTS usuario_alergias (
    id_usu INT NOT NULL,
    id_fru INT NOT NULL,
    PRIMARY KEY (id_usu, id_fru),
    FOREIGN KEY (id_usu) REFERENCES usuarios(id_usu),
    FOREIGN KEY (id_fru) REFERENCES frutas(id_fru)
);

CREATE TABLE IF NOT EXISTS frutas (
    id_fru INT PRIMARY KEY AUTO_INCREMENT,
    nom_fru VARCHAR(100) NOT NULL UNIQUE
);
```

### Funciones Disponibles en controlador.php
```php
obtenerFrutas($con)                           // Retorna todas las frutas
obtenerAlergiasUsuario($con, $id_usu)         // Retorna alergias del usuario
obtenerFrutasDisponibles($con, $id_usu)       // Retorna frutas NO marcadas como alérgenas
```

---

## 2. SISTEMA DE INGREDIENTES (PARCIALMENTE IMPLEMENTADO ⚠️)

### Descripción
Sistema para gestionar ingredientes disponibles y su stock, permitiendo rastrear inventario por receta/producto.

### Ubicación
- **Esquema de BD**: `migrations_alergias-ingredientes.sql` (no ejecutado aún)
- **Funciones backend**: `includes/taf2/controlador.php`
- **Interfaz de usuario**: Pendiente crear `views/taf2/ingredientes.php`

### Tabla de Base de Datos (AÚN NO EJECUTADA)
```sql
CREATE TABLE IF NOT EXISTS ingredientes (
    id_ing INT PRIMARY KEY AUTO_INCREMENT,
    nombre_ing VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    unidad_medida VARCHAR(50),
    stock_actual DECIMAL(10,2) DEFAULT 0,
    stock_minimo DECIMAL(10,2) DEFAULT 5,
    precio_unitario DECIMAL(10,2) DEFAULT 0,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo'
);
```

### Ingredientes Predefinidos (a insertar)
- Naranja, Fresa, Banano, Manzana, Kiwi
- Mango, Piña, Mora, Arándano, Cacao
- Leche, Yogurt, Miel, Granola, Almendra

### Funciones Disponibles
```php
obtenerIngredientes($con)                     // Retorna todos los ingredientes
obtenerIngredientesReceta($con, $id_rec)      // Retorna ingredientes de una receta específica
```

### Pendiente ⚠️
1. **Ejecutar migración**: Aplicar `migrations_alergias-ingredientes.sql` en phpMyAdmin
2. **Crear interfaz**: Página para CRUD de ingredientes
3. **Integración**: Modificar formulario de recetas para seleccionar ingredientes

---

## 3. DETALLES DE PRODUCTOS (COMPLETADO ✅)

### Descripción
Página dedicada para ver detalles completos de un producto/receta con ingredientes y productos similares.

### Ubicación
- **Interfaz de usuario**: `views/taf2/detalle.php`
- **Funciones backend**: `includes/taf2/controlador.php`

### Características
✅ Nombre y descripción del producto
✅ Precio y stock disponible
✅ Categoría
✅ Lista de ingredientes utilizados (si aplica)
✅ Stock de ingredientes con indicador de disponibilidad
✅ Productos similares (máximo 3) de la misma categoría
✅ Botón "Volver a Productos"

### Cómo Acceder
1. Ve a **Productos** (en el panel TAF2 como admin/inventario)
2. Haz clic en el botón azul **"Ver Detalles"** en cualquier producto
3. O accede directamente a: `index.php?page=taf2&view=detalle&id=ID_PRODUCTO`

### Funciones Disponibles
```php
obtenerDetalleProducto($con, $id_pro)        // Retorna info completa del producto
obtenerRecetasParecidas($con, $id_cat, $id_producto_actual, $limite=3) // Productos similares
obtenerIngredientesReceta($con, $id_rec)     // Ingredientes de la receta
```

---

## 4. INSTRUCCIONES DE INSTALACIÓN

### Paso 1: Ejecutar Migración de Base de Datos
1. Abre phpMyAdmin y selecciona la base de datos `taf2`
2. Ve a la pestaña **SQL**
3. Abre el archivo `migrations_alergias-ingredientes.sql` y cópialo
4. Pégalo en el panel SQL y ejecuta
5. Verifica que se hayan creado las tablas:
   - `ingredientes` (con 15 ingredientes)
   - Población de `frutas` (con 12 frutas)

### Paso 2: Verificar Funciones en controlador.php
✅ Las funciones ya están implementadas:
- `obtenerFrutas()`
- `obtenerAlergiasUsuario()`
- `obtenerFrutasDisponibles()`
- `obtenerIngredientes()`
- `obtenerIngredientesReceta()`
- `obtenerRecetasParecidas()`
- `obtenerDetalleProducto()`

### Paso 3: Acciones POST en index.php
✅ Ya están implementadas:
- `agregar_alergia` - Inserta en `usuario_alergias`
- `remover_alergia` - Elimina de `usuario_alergias`

### Paso 4: Verificar Enlaces en Menú
✅ Ya actualizado en `includes/layout.php`:
- Menú lateral incluye "Mis Alergias"
- Acceso desde cualquier usuario logueado

### Paso 5: Vistas TAF2 Configuradas
✅ Ya en `index.php` (línea 333-339):
- `'alergias' => 'taf2/alergias.php'`
- `'detalle' => 'taf2/detalle.php'`

---

## 5. FLUJO DE USO

### Para Clientes (Usuarios Normales)
1. **Gestionar Alergias**
   - Menú lateral → "Mis Alergias"
   - Seleccionar frutas a las que es alérgico
   - El sistema recordará sus preferencias

### Para Admins/Inventario
1. **Ver Productos con Detalles**
   - Panel TAF2 → Productos
   - Haz clic en "Ver Detalles" para cada producto
   - Visualiza ingredientes y stock
   - Ve productos similares en la misma categoría

---

## 6. PRÓXIMAS FASES

### Fase 3: Ingredientes Completos (Pendiente)
- [ ] Crear interfaz de gestión de ingredientes
- [ ] Formulario CRUD para ingredientes
- [ ] Panel de stock y alertas
- [ ] Integrar selección de ingredientes en recetas

### Fase 4: Personalización Avanzada (Futuro)
- [ ] Filtrar productos por alergias
- [ ] Recomendaciones personalizadas basadas en alergias
- [ ] Histórico de órdenes
- [ ] Productos favoritos

### Fase 5: Análisis (Futuro)
- [ ] Reportes de alérgenos más comunes
- [ ] Análisis de preferencias
- [ ] Sugerencias de nuevos productos

---

## 7. TABLA DE REFERENCIA DE BASE DE DATOS

### usuario_alergias
| Columna | Tipo | Descripción |
|---------|------|------------|
| id_usu | INT | ID del usuario (FK) |
| id_fru | INT | ID de la fruta (FK) |

### frutas
| Columna | Tipo | Descripción |
|---------|------|------------|
| id_fru | INT | ID único |
| nom_fru | VARCHAR(100) | Nombre de la fruta |

### ingredientes (Por ejecutar)
| Columna | Tipo | Descripción |
|---------|------|------------|
| id_ing | INT | ID único |
| nombre_ing | VARCHAR(100) | Nombre del ingrediente |
| descripcion | TEXT | Descripción |
| unidad_medida | VARCHAR(50) | Unidad (kg, L, etc.) |
| stock_actual | DECIMAL | Stock disponible |
| stock_minimo | DECIMAL | Mínimo para alertas |
| precio_unitario | DECIMAL | Precio |
| estado | ENUM | activo/inactivo |

---

## 8. NOTAS TÉCNICAS

### Seguridad
✅ Todas las consultas usan prepared statements (mysqli)
✅ Input validado y sanitizado
✅ Sesión de usuario verificada en cada acción

### Rendimiento
✅ Funciones utilizan índices de base de datos
✅ Limit aplicado a recetas similares (máximo 3)

### Compatibilidad
✅ PHP 7+
✅ MySQLi
✅ Frontend responsivo (CSS en taf2.css)

---

## 9. TROUBLESHOOTING

### Problema: "Alergias no se guardan"
**Solución**: Verifica que la tabla `usuario_alergias` existe en la BD

### Problema: "No aparecen frutas en el selector"
**Solución**: Ejecuta migración para popular tabla `frutas`

### Problema: "Ingredientes muestra vacío"
**Solución**: Aún no se ejecutó `migrations_alergias-ingredientes.sql`

### Problema: "Enlace 'Mis Alergias' no aparece en menú"
**Solución**: Verifica que estés logueado y que `includes/layout.php` esté actualizado

---

## 10. ARCHIVOS MODIFICADOS/CREADOS

### Nuevos Archivos ✨
- `views/taf2/alergias.php` - Interfaz de gestión de alergias
- `views/taf2/detalle.php` - Página de detalles de producto
- `migrations_alergias-ingredientes.sql` - Script de migración

### Archivos Modificados 📝
- `index.php` - Agregadas acciones POST para alergias
- `includes/taf2/controlador.php` - 9 nuevas funciones
- `views/taf2/productos.php` - Botón "Ver Detalles" agregado
- `includes/layout.php` - Enlace "Mis Alergias" en menú

---

**Última actualización**: 2025
**Versión**: 1.0
**Estado**: Producción (alergias y detalles) / Testing (ingredientes)
