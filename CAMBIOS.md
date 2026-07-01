# Registro de cambios

## 2026-06-30

### Fix: Variables indefinidas en vista de ajustes

- **Archivo:** `views/ajustes.php`
- **Problema:** Las variables `$alergiasUsuario` y `$notificacionesUsuario` se usaban en el HTML antes de definirse, generando warnings de variable indefinida y un deprecated en `htmlspecialchars()`.
- **Cambio:** Moví el bloque PHP que inicializa y consulta estas variables al inicio del archivo, eliminando el duplicado que estaba al final.
- **Líneas afectadas:** 8-41 (inicio del archivo), eliminadas las líneas 676-755 que duplicaban la inicialización después del HTML. Verificado que el `<style>` quede al final y que todo el markup se mantenga intacto con los valores cargados antes de imprimir.
