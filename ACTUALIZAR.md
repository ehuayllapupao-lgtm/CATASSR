# Corrección 1 — Crear establecimientos y medicamentos

Solo el administrador puede crear nuevos establecimientos de salud y medicamentos o insumos. Los demás roles conservan sus permisos de consulta y su ámbito. El servidor también rechaza solicitudes de creación realizadas por otros roles.

## Si CATASSR ya está instalado

1. Descarga el repositorio con **Code → Download ZIP** y descomprímelo.
2. Conserva una copia del archivo actual `C:\xampp\htdocs\CATASSR\index.php`.
3. Dentro de lo descargado, abre `CATASSR-main/CATASSR/`.
4. Copia **únicamente `index.php`** a `C:\xampp\htdocs\CATASSR\index.php` y acepta reemplazarlo.
5. Abre `http://localhost/CATASSR/` e ingresa como administrador.

No vuelvas a ejecutar el instalador. No importes SQL ni reemplaces tu configuración. Esta actualización utiliza las tablas actuales y conserva los usuarios, asignaciones y datos existentes.

## Usar las nuevas opciones

- **Establecimientos:** completa el formulario **Crear nuevo establecimiento**, elige una microred existente y pulsa **Crear establecimiento**. Puedes asignarle un responsable desde Usuarios y accesos.
- **Medicamentos e insumos:** completa **Crear nuevo medicamento o insumo**, elige tipo y unidad, y pulsa **Crear medicamento o insumo**.
- Los códigos son únicos dentro de cada catálogo. Los códigos con ceros iniciales se conservan.

Para una instalación nueva, sigue el README principal.
