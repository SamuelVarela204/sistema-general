# Tropical & Fresh - Sistema de Gestión Profesional

## 🎉 Actualización: Módulos Completos Implementados

Se ha implementado un sistema profesional y completo para gestión de inventarios, recetas y ventas en bebidas naturales. Aquí está el resumen de los nuevos módulos.

---

## 📋 Contenido

1. [Módulo de Recetas](#módulo-de-recetas)
2. [Módulo de Inventario](#módulo-de-inventario)
3. [Módulo POS/Facturación](#módulo-posfacturación)
4. [Estructura de Roles](#estructura-de-roles)
5. [Credenciales de Prueba](#credenciales-de-prueba)
6. [Características Principales](#características-principales)

---

## 📖 Módulo de Recetas

### Descripción
Gestión completa de recetas de bebidas naturales con ingredientes, costos y personalización.

### Rutas Disponibles
- **Ver Recetas:** `/recetas` - Lista todas las recetas disponibles
- **Crear Receta:** `/recetas/crear` - Formulario para crear nueva receta (Admin/Gerente)
- **Ver Detalle:** `/recetas/{id}` - Detalle completo con ingredientes
- **Editar:** `/recetas/{id}/editar` - Modificar receta (Admin/Gerente)
- **Eliminar:** Opción en detalles (Admin/Gerente)

### Funcionalidades
✅ Crear recetas con múltiples ingredientes  
✅ Asignar precio base a cada receta  
✅ Marcar recetas como personalizables o fijas  
✅ Ver stock actual de ingredientes en cada receta  
✅ Historial de creación y modificación  
✅ Control de acceso por roles  

### Ejemplo de Uso
1. Ir a "Recetas" en el menú lateral
2. Click en "+ Crear Receta"
3. Completar datos: nombre, descripción, precio
4. Agregar ingredientes (cantidad y unidad de medida)
5. Guardar receta

---

## 📦 Módulo de Inventario

### Descripción
Control total de stock de ingredientes con auditoría de movimientos, alertas automáticas y trazabilidad.

### Rutas Disponibles
- **Ver Inventario:** `/inventario` - Dashboard con todos los ingredientes
- **Nuevo Ingrediente:** `/inventario/crear` - Crear nuevo ingrediente (Admin/Gerente)
- **Detalle:** `/inventario/{id}` - Ver stock y historial de movimientos
- **Editar:** `/inventario/{id}/editar` - Modificar datos del ingrediente
- **Registrar Movimiento:** `/inventario/{id}/movimiento` - Entrada/salida de stock

### Funcionalidades
✅ Gestión de ingredientes (crear, editar, deshabilitar)  
✅ Registro automático de movimientos (entrada, salida, ajuste, merma)  
✅ Auditoría completa: quién, cuándo, cuánto, motivo  
✅ Alertas automáticas de stock bajo  
✅ Cálculo de costo unitario y costo total  
✅ Trazabilidad de todos los movimientos  

### Tipos de Movimientos
- **Entrada:** Compra de ingredientes a proveedores
- **Salida:** Uso en la producción/venta
- **Ajuste:** Correcciones de inventario
- **Merma:** Daño o vencimiento

### Ejemplo de Uso
1. Ir a "Gestión de Stock" en el menú
2. Seleccionar un ingrediente
3. En "Registrar Movimiento":
   - Seleccionar tipo (Entrada, Salida, etc.)
   - Ingresar cantidad
   - Agregar descripción
   - Guardar

---

## 🛒 Módulo POS/Facturación

### Descripción
Sistema rápido y eficiente para registro de ventas en punto de venta con descuento automático de inventario.

### Rutas Disponibles
- **POS Principal:** `/pos` - Interfaz de venta
- **Historial:** `/pos/historial` - Registro de todas las ventas
- **Recibo:** `/pos/recibo/{id}` - Ver y imprimir recibo

### Funcionalidades
✅ Interfaz intuitiva y rápida para agregar productos  
✅ Carrito de compra con actualización en tiempo real  
✅ Descuentos aplicables por venta  
✅ Múltiples métodos de pago (Efectivo, Tarjeta, Transferencia)  
✅ **Descuento automático de inventario** según receta (Regla de Negocio RN-09)  
✅ Generación de recibos imprimibles  
✅ Historial completo de transacciones  
✅ Auditoría de quién realizó cada venta  

### Regla de Negocio Crítica (RN-09)
**Descarga Automatizada de Inventario:** Al procesar una venta, el sistema automáticamente:
1. Consulta la receta de la bebida
2. Calcula los ingredientes requeridos
3. Descuenta automáticamente del stock
4. Registra movimiento en auditoría
5. Verifica alertas de stock bajo

### Ejemplo de Uso - Venta Rápida
1. Ir a "POS/Facturación"
2. Hacer click en bebidas para agregar al carrito
3. Ajustar cantidades si es necesario
4. Aplicar descuento (opcional)
5. Seleccionar método de pago
6. Click en "Completar Venta"
7. Ver recibo e imprimir si es necesario

---

## 👥 Estructura de Roles

### Admin (Administrador)
- ✅ Acceso total al sistema
- ✅ Gestión de usuarios (crear, editar, eliminar)
- ✅ Gestión de recetas (crear, editar, eliminar)
- ✅ Gestión de inventario (crear, editar, movimientos)
- ✅ Acceso a POS
- ✅ Configuración global
- ✅ Reversión de transacciones

### Gerente
- ✅ Visualización de dashboard
- ✅ Gestión de recetas
- ✅ Gestión de inventario
- ✅ Ver movimientos de usuarios
- ✅ Acceso a POS
- ✅ Reversión de transacciones
- ❌ No puede crear/eliminar usuarios

### Inventario
- ✅ Ver inventario
- ✅ Registrar movimientos
- ✅ Ver alertas
- ✅ Ver historial
- ❌ No puede modificar precios
- ❌ No puede acceder a POS

### Cajero
- ✅ Acceso a POS
- ✅ Ver historial de ventas
- ✅ Registro de transacciones
- ❌ No puede modificar recetas
- ❌ No puede modificar inventario
- ❌ No puede gestionar usuarios

### Cliente
- ✅ Ver recetas disponibles
- ✅ Ver su perfil y pedidos
- ✅ Gestionar preferencias
- ❌ Sin acceso a módulos administrativos

---

## 🔐 Credenciales de Prueba

Se han creado 5 usuarios de prueba. Contraseña para todos: **`password123`**

| Usuario | Email | Rol | Acceso |
|---------|-------|-----|--------|
| Admin Principal | admin@tropical.com | Admin | Todo |
| Gerente Operativo | gerente@tropical.com | Gerente | Recetas, Inventario, POS |
| Inventario Expert | inventario@tropical.com | Inventario | Solo Inventario |
| Cajero Principal | cajero@tropical.com | Cliente* | POS |
| Cliente Frecuente | cliente@tropical.com | Cliente | Perfil, Recetas |

*El rol Cajero se asignará en próximas actualizaciones

---

## ✨ Características Principales

### Seguridad
- 🔒 Autenticación con hash Bcrypt
- 🔒 Validación de roles en cada ruta
- 🔒 Prepared statements para prevenir SQL injection (Política P1)
- 🔒 Sesiones con timeout automático (Política P3)
- 🔒 Encriptación de contraseñas (Política P2)

### Auditoría y Trazabilidad
- 📊 Registro de todos los movimientos de inventario
- 👤 Identificación del usuario responsable de cada acción
- 📅 Timestamp exacto de cada operación
- 📝 Descripción de motivos

### Gestión de Inventario Inteligente
- 🚨 Alertas automáticas de stock bajo
- 📉 Cálculo automático de costos
- 🔄 Descuento automático en ventas
- 📈 Reportes de movimientos

### Interfaz Profesional
- 🎨 Diseño responsivo (mobile-friendly)
- 🎯 Navegación intuitiva
- ⚡ Carga rápida
- 📊 Tablas con paginación
- 🔔 Sistema de notificaciones

---

## 🗄️ Base de Datos - Nuevas Tablas

### recetas
```sql
- id_receta (PK)
- nombre_receta
- descripcion
- precio_base
- personalizable
- estado
- id_usu_creador (FK)
- fecha_creacion
- fecha_actualizacion
```

### ingredientes
```sql
- id_ingrediente (PK)
- nombre_ingrediente
- tipo
- unidad_medida
- costo_unitario
- stock_actual
- stock_minimo
- estado
```

### detalles_receta (Junction Table)
```sql
- id_detalle_receta (PK)
- id_receta (FK)
- id_ingrediente (FK)
- cantidad_requerida
- unidad_medida
```

### movimientos_inventario (Auditoría)
```sql
- id_movimiento (PK)
- id_ingrediente (FK)
- tipo_movimiento (entrada, salida, ajuste, merma)
- cantidad
- descripcion
- id_usu_responsable (FK)
- fecha_movimiento
```

### ventas
```sql
- id_venta (PK)
- id_usu_cajero (FK)
- total
- descuento
- metodo_pago
- estado
- fecha_venta
```

### detalles_venta
```sql
- id_detalle_venta (PK)
- id_venta (FK)
- id_receta (FK)
- cantidad
- precio_unitario
- subtotal
- personalizacion
```

### alertas_stock
```sql
- id_alerta (PK)
- id_ingrediente (FK)
- stock_actual
- stock_minimo
- estado_alerta
- fecha_alerta
- fecha_resolucion
```

---

## 🚀 Próximas Mejoras Planeadas

- [ ] Módulo de reportes PDF/Excel
- [ ] Historial de cambios de precios
- [ ] Sistema de proveedores
- [ ] Proyecciones de demanda
- [ ] Dashboard analytics avanzado
- [ ] API REST completa
- [ ] Aplicación móvil
- [ ] Integración con pasarela de pagos
- [ ] Importación masiva de productos
- [ ] Backup automático

---

## 📞 Soporte

Para reportar problemas o sugerencias, contacta al equipo de desarrollo.

---

**Versión:** 2.0 Professional  
**Fecha:** Septiembre 2026  
**Estado:** ✅ Producción Ready
