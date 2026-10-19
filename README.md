# SSYMILAB

Sistema de gestión (ERP) de **SSYMILAB E.I.R.L.**: venta, compra, almacén y mantenimiento de equipos de laboratorio, con facturación electrónica SUNAT.

## Tecnologías

- **Laravel 8** (PHP 7.3 / 8) con **Jetstream** y **Livewire 2**: casi todas las pantallas son componentes Livewire.
- **Tailwind CSS**, más componentes Blade propios en `resources/views/components/general` (`x-general.input`, `x-general.select`, `x-general.table`, `x-general.card-form`, ...).
- **PostgreSQL** como base de datos de producción. Las búsquedas usan `ILIKE`, que es propio de PostgreSQL.
- **DomPDF** y **TCPDF** para los PDF. Los "Excel" son vistas HTML descargadas como `.xls`.
- **`clifacturacion/`**: módulo PHP aparte, sin Laravel, que envía los comprobantes al facturador electrónico.

## Módulos

| Menú | Qué contiene |
|---|---|
| **Mantenimiento** | Personas (clientes, proveedores, personal, vendedores...), productos, categorías, marcas, unidades, principios activos, motivos y zonas. |
| **Venta** | Proformas, pedidos, ventas (facturas y boletas), caja, comisiones de vendedor y de supervisor, cuentas por cobrar, guías de venta, notas de crédito y anulaciones. |
| **Almacén** | Documentos de almacén, guías de remisión, series, lotes, stock, kardex y formatos de envío. |
| **Compras** | Requerimientos, compras, guías de entrada y cuentas por pagar. |
| **Equipos** | Cronograma de mantenimiento preventivo, mantenimientos, instalaciones y próximos mantenimientos. |
| **Facturación** | Comprobantes electrónicos, a través de `clifacturacion`. |
| **Reportes** | Lotes, series, stock, caja, compras, proveedores, productos por proveedor, productos por cliente, requerimientos y tipo de cambio. |
| **Sistema / Usuarios / Seguridad** | Empresa, sucursales, cajas, tarjetas, métodos de transferencia, usuarios, tipos de usuario, menús y acciones. |

Todos los documentos (ventas, compras, guías, proformas, pedidos, notas de crédito...) se guardan en la tabla **`documents`** y sus líneas en **`document_details`**. El campo `document_type_id` indica qué es cada uno; por ejemplo, 12 = boleta, 13 = factura, 15 = pedido y 17 = nota de crédito. Cada tipo tiene su propio modelo (`Sale`, `Purchase`, `Proforma`, `Order`, ...) que extiende `Document`.

## Permisos

El menú y los permisos **se guardan en la base de datos**, no en el código:

| Tabla | Qué guarda |
|---|---|
| `menu_categories` | Los grupos del menú lateral: MANTENIMIENTO, VENTA, REPORTES... |
| `menu_options` | Cada opción del menú. Su campo `link` es el nombre de la ruta (`brand`, `sale`, `report-product-client`...). |
| `actions` | Lo que se puede hacer dentro de una opción: `create_brand`, `edit_brand`, `delete_brand`, `pdf_brand`... |
| `permissions` | Qué tipo de usuario ve cada opción del menú (`menu_option_id`, `user_type_id`). |
| `operations` | Qué tipo de usuario puede usar cada acción (`action_id`, `user_type_id`). |

- Los permisos se asignan en **Usuarios → Tipo de usuario**, con el botón **Permisos** de cada fila.
- Cada componente consulta las acciones permitidas en su `load_actions()`. La vista muestra u oculta los botones con `in_array('codigo_accion', $arr_actions)`.

## Cómo agregar una pantalla nueva

Copia el patrón de una pantalla existente. **Marcas** es el ejemplo más simple de CRUD y **Zonas** el más reciente:

1. **Modelo** en `app/Models`, con `SoftDeletes` y un scope `search`.
2. **Trait de búsqueda** en `app/Http/Traits/<Módulo>/<Nombre>Trait.php` (por ejemplo `search_brands(...)`). Normaliza los filtros con `Libreria::getParam()`.
3. **Controlador** en `app/Http/Controllers/<Módulo>`, que solo muestra las vistas `admin`, `create` y `edit` (y las exportaciones PDF/Excel si las hay).
4. **Componentes Livewire** en `app/Http/Livewire/<Módulo>/<Nombre>/`:
   - `AdminComponent`: el listado con filtros, paginación y `SortTable`.
   - `MantComponent`: el formulario para crear y editar.
   - `DeleteComponent`: el modal de confirmación para eliminar.
5. **Validación** en `app/Http/LivewireRequest/<Módulo>/<Nombre>/<Nombre>Request.php`, con `rules()`, `messages()` y `validationAttributes()`.
6. **Vistas**:
   - `resources/views/app/<módulo>/<nombre>/`: las páginas que cargan el componente.
   - `resources/views/livewire/<módulo>/<nombre>/`: las vistas de los componentes.
7. **Rutas** en `routes/web.php`, con `Route::resource(...)->only([...])->names([...])`.
8. **Menú y acciones**: crea una migración que inserte la fila en `menu_options` y sus `actions` (ver `database/migrations/2026_10_16_000000_create_zones_table.php`). Luego asigna los permisos desde Seguridad.

Para un **reporte**, sigue el mismo patrón en `Report/` (ver *Productos por cliente*). El PDF se genera con DomPDF desde una vista Blade, y el Excel reutiliza esa misma vista HTML con la cabecera `application/vnd.ms-excel`.

## Convenciones

- **Textos en mayúsculas**: los nombres y descripciones se guardan con `mb_strtoupper`.
- **Fin de línea CRLF**: los archivos existentes usan CRLF. Respétalo al editarlos para no generar diffs innecesarios.
- **Comentarios** cortos y en mayúsculas, como en el resto del código (`//ZONA SOLO APLICA A CLIENTES`).
- **Migraciones incompletas**: la base de datos real tiene tablas que no tienen migración (por ejemplo mantenimientos, `person_products`, `schedules`, `notifications` y `jobs`). Para tener el esquema completo hay que restaurar un respaldo de la base de datos; `php artisan migrate` desde cero no basta. Los cambios nuevos sí deben ir siempre como migración.
- **Respaldos fuera del repositorio**: los respaldos SQL (`dato-ssymilab.sql`) están en `.gitignore` y no deben subirse, porque contienen credenciales de SUNAT y datos de clientes.
