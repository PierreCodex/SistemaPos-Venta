# 📊 Documentación del Flujo de Base de Datos
## Sistema POS Abarrotes

---

## 1. Diagrama de Relaciones Principal

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           SEGURIDAD Y USUARIOS                               │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   ┌──────────┐    ┌──────────────────┐    ┌───────────────┐                │
│   │  users   │◄───│ model_has_roles  │───►│    roles      │                │
│   └────┬─────┘    └──────────────────┘    └───────┬───────┘                │
│        │                                          │                         │
│        │          ┌──────────────────────┐        │                         │
│        └─────────►│ model_has_permissions│◄───────┘                         │
│                   └──────────┬───────────┘                                  │
│                              │                                              │
│                   ┌──────────▼───────────┐                                  │
│                   │    permissions       │                                  │
│                   └──────────────────────┘                                  │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                              CATÁLOGOS                                       │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   ┌────────────────────┐         ┌────────────┐         ┌──────────┐       │
│   │ categorias_globales│◄────────│ categorias │         │  marcas  │       │
│   └────────────────────┘         └─────┬──────┘         └────┬─────┘       │
│                                        │                     │              │
│                                        └──────────┬──────────┘              │
│                                                   │                         │
│   ┌──────────┐                           ┌───────▼────────┐                │
│   │ unidades │──────────────────────────►│   productos    │                │
│   └──────────┘                           └────────────────┘                │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                         OPERACIONES COMERCIALES                              │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   COMPRAS                                    VENTAS                          │
│   ═══════                                    ══════                          │
│                                                                              │
│   ┌─────────────┐                         ┌──────────────┐                  │
│   │ proveedores │                         │   clientes   │                  │
│   └──────┬──────┘                         └──────┬───────┘                  │
│          │                                       │                          │
│          ▼                                       ▼                          │
│   ┌──────────────┐                        ┌─────────────┐                   │
│   │   compras    │                        │   ventas    │◄──┐               │
│   └──────┬───────┘                        └──────┬──────┘   │               │
│          │                                       │          │               │
│          ▼                                       ▼          │               │
│   ┌────────────────┐                      ┌───────────────┐ │               │
│   │ detalle_compras│                      │ detalle_ventas│ │               │
│   └───────┬────────┘                      └───────┬───────┘ │               │
│           │                                       │         │               │
│           │         ┌────────────┐                │         │               │
│           └────────►│ productos  │◄───────────────┘         │               │
│                     └─────┬──────┘                          │               │
│                           │                                 │               │
│                           ▼                                 │               │
│                     ┌──────────┐     ┌─────────────────┐   │               │
│                     │  kardex  │     │  caja_sesiones  │───┘               │
│                     └──────────┘     └────────┬────────┘                   │
│                                               │                             │
│                                               ▼                             │
│                                      ┌─────────────────┐                   │
│                                      │ caja_movimientos│                   │
│                                      └─────────────────┘                   │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Flujos de Negocio

### 2.1 🔐 Flujo de Autenticación

```
┌─────────┐    ┌───────────────┐    ┌───────────────┐    ┌─────────────┐
│ Usuario │───►│ Login Laravel │───►│ Verificar Rol │───►│ Dashboard   │
└─────────┘    └───────────────┘    └───────────────┘    └─────────────┘
                                            │
                                            ▼
                                    ┌───────────────────┐
                                    │ Cargar Permisos   │
                                    │ (Spatie)          │
                                    └───────────────────┘
```

**Tablas involucradas:**
- `users` → Credenciales y datos del usuario
- `roles` → Admin, Cajero, Almacenero, Supervisor
- `permissions` → Acciones específicas permitidas
- `model_has_roles` → Asignación usuario-rol
- `role_has_permissions` → Permisos por rol

---

### 2.2 💰 Flujo de Apertura/Cierre de Caja

```
┌──────────────────────────────────────────────────────────────────────────┐
│                           APERTURA DE CAJA                                │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                           │
│  ┌─────────┐    ┌─────────────────┐    ┌──────────────────────┐          │
│  │ Cajero  │───►│ Verificar caja  │───►│ Crear caja_sesiones  │          │
│  └─────────┘    │ abierta previa  │    │ estado = 'ABIERTA'   │          │
│                 └─────────────────┘    └──────────────────────┘          │
│                                                  │                        │
│                                                  ▼                        │
│                                        ┌──────────────────────┐          │
│                                        │ Registrar            │          │
│                                        │ monto_inicial        │          │
│                                        └──────────────────────┘          │
└──────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────┐
│                            CIERRE DE CAJA                                 │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                           │
│  1. Contar efectivo físico (monto_final)                                 │
│  2. Calcular monto_esperado = monto_inicial + ingresos - egresos         │
│  3. Calcular diferencia = monto_final - monto_esperado                   │
│  4. Actualizar estado = 'CERRADA'                                        │
│  5. Registrar fecha_cierre y user_cierre_id                              │
│                                                                           │
└──────────────────────────────────────────────────────────────────────────┘
```

**Tablas involucradas:**
- `caja_sesiones` → Sesión de caja (turno)
- `caja_movimientos` → Ingresos/egresos adicionales
- `users` → Cajero que abre/cierra

---

### 2.3 🛒 Flujo de Venta (POS)

```
┌────────────────────────────────────────────────────────────────────────────┐
│                              PROCESO DE VENTA                               │
├────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  PASO 1: Verificar Caja Abierta                                            │
│  ════════════════════════════════                                          │
│  ┌─────────┐    ┌────────────────────┐                                     │
│  │ Cajero  │───►│ ¿Tiene caja        │──► NO ──► Abrir caja primero        │
│  └─────────┘    │ abierta?           │                                     │
│                 └─────────┬──────────┘                                     │
│                           │ SÍ                                             │
│                           ▼                                                │
│  PASO 2: Escanear/Buscar Productos                                         │
│  ════════════════════════════════════                                      │
│  ┌─────────────────┐    ┌────────────────┐    ┌──────────────────┐        │
│  │ Escanear código │───►│ Buscar en      │───►│ Agregar a        │        │
│  │ o buscar nombre │    │ productos      │    │ detalle_ventas   │        │
│  └─────────────────┘    └────────────────┘    └──────────────────┘        │
│                                                        │                   │
│                                                        ▼                   │
│  PASO 3: Calcular Totales                                                  │
│  ════════════════════════                                                  │
│  ┌─────────────────────────────────────────────────────────────┐          │
│  │ subtotal = Σ(cantidad × precio_unitario - descuento)        │          │
│  │ igv = subtotal × 0.18 (si aplica)                           │          │
│  │ total = subtotal + igv                                       │          │
│  └─────────────────────────────────────────────────────────────┘          │
│                                                        │                   │
│                                                        ▼                   │
│  PASO 4: Procesar Pago                                                     │
│  ════════════════════════                                                  │
│  ┌────────────────┐    ┌────────────────────────────────────────┐         │
│  │ Seleccionar    │───►│ EFECTIVO: monto_recibido, calcular     │         │
│  │ método pago    │    │           vuelto                        │         │
│  └────────────────┘    │ YAPE/PLIN: validar transferencia       │         │
│                        │ TARJETA: procesar POS                   │         │
│                        │ MIXTO: distribuir montos                │         │
│                        │ CRÉDITO: verificar límite cliente       │         │
│                        └────────────────────────────────────────┘         │
│                                                        │                   │
│                                                        ▼                   │
│  PASO 5: Finalizar Venta                                                   │
│  ════════════════════════                                                  │
│  ┌─────────────────────────────────────────────────────────────┐          │
│  │ 1. Insertar registro en 'ventas'                            │          │
│  │ 2. Insertar items en 'detalle_ventas'                       │          │
│  │ 3. Actualizar stock en 'productos' (restar cantidad)        │          │
│  │ 4. Insertar movimientos en 'kardex'                         │          │
│  │ 5. Insertar ingreso en 'caja_movimientos'                   │          │
│  │ 6. Actualizar correlativo en 'series_comprobantes'          │          │
│  │ 7. Generar e imprimir comprobante                           │          │
│  └─────────────────────────────────────────────────────────────┘          │
└────────────────────────────────────────────────────────────────────────────┘
```

**Tablas involucradas:**
| Tabla | Operación | Descripción |
|-------|-----------|-------------|
| `caja_sesiones` | SELECT | Verificar caja abierta |
| `productos` | SELECT, UPDATE | Buscar y actualizar stock |
| `clientes` | SELECT | Obtener datos del cliente |
| `ventas` | INSERT | Crear cabecera de venta |
| `detalle_ventas` | INSERT | Crear líneas de detalle |
| `kardex` | INSERT | Registrar salida de stock |
| `caja_movimientos` | INSERT | Registrar ingreso |
| `series_comprobantes` | UPDATE | Incrementar correlativo |

---

### 2.4 📦 Flujo de Compra (Ingreso de Mercadería)

```
┌────────────────────────────────────────────────────────────────────────────┐
│                             PROCESO DE COMPRA                               │
├────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  PASO 1: Registrar Datos del Comprobante                                   │
│  ════════════════════════════════════════                                  │
│  ┌──────────────────────────────────────────────────────────────┐          │
│  │ • Seleccionar proveedor                                       │          │
│  │ • Tipo comprobante (Factura/Boleta)                          │          │
│  │ • Número de comprobante                                       │          │
│  │ • Fecha de emisión                                            │          │
│  │ • Forma de pago (Contado/Crédito)                            │          │
│  └──────────────────────────────────────────────────────────────┘          │
│                                          │                                  │
│                                          ▼                                  │
│  PASO 2: Agregar Productos                                                 │
│  ═════════════════════════                                                 │
│  ┌──────────────────────────────────────────────────────────────┐          │
│  │ Por cada producto:                                            │          │
│  │   • Buscar/crear producto                                     │          │
│  │   • Ingresar cantidad                                         │          │
│  │   • Ingresar costo unitario                                   │          │
│  │   • Ingresar fecha vencimiento (opcional)                     │          │
│  │   • Calcular subtotal                                         │          │
│  └──────────────────────────────────────────────────────────────┘          │
│                                          │                                  │
│                                          ▼                                  │
│  PASO 3: Finalizar Compra                                                  │
│  ═════════════════════════                                                 │
│  ┌──────────────────────────────────────────────────────────────┐          │
│  │ 1. Insertar registro en 'compras'                             │          │
│  │ 2. Insertar items en 'detalle_compras'                        │          │
│  │ 3. Actualizar stock en 'productos' (sumar cantidad)           │          │
│  │ 4. Actualizar precio_compra en 'productos' (si cambió)        │          │
│  │ 5. Insertar movimientos en 'kardex'                           │          │
│  │ 6. Si es crédito: registrar cuenta por pagar                  │          │
│  │ 7. Si es contado: registrar en caja_movimientos               │          │
│  └──────────────────────────────────────────────────────────────┘          │
└────────────────────────────────────────────────────────────────────────────┘
```

---

### 2.5 📈 Flujo del Kardex (Trazabilidad de Stock)

```
┌────────────────────────────────────────────────────────────────────────────┐
│                           MOVIMIENTOS DE KARDEX                             │
├────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ENTRADAS (cantidad positiva)                                              │
│  ════════════════════════════                                              │
│  ┌────────────────────────────────────────────────────────────┐            │
│  │ COMPRA            → Ingreso de mercadería del proveedor     │            │
│  │ DEVOLUCION_CLIENTE→ Cliente devuelve producto               │            │
│  │ AJUSTE_POSITIVO   → Corrección por conteo físico (+)        │            │
│  │ INVENTARIO_INICIAL→ Carga inicial del sistema               │            │
│  │ TRANSFERENCIA     → Recepción de otro almacén               │            │
│  └────────────────────────────────────────────────────────────┘            │
│                                                                             │
│  SALIDAS (cantidad negativa)                                               │
│  ═════════════════════════════                                             │
│  ┌────────────────────────────────────────────────────────────┐            │
│  │ VENTA             → Salida por venta al cliente             │            │
│  │ DEVOLUCION_PROV   → Devolución al proveedor                 │            │
│  │ AJUSTE_NEGATIVO   → Corrección por conteo físico (-)        │            │
│  │ MERMA             → Pérdida por vencimiento/daño            │            │
│  │ TRANSFERENCIA     → Envío a otro almacén                    │            │
│  └────────────────────────────────────────────────────────────┘            │
│                                                                             │
│  ESTRUCTURA DEL REGISTRO                                                    │
│  ═══════════════════════                                                   │
│  ┌────────────────────────────────────────────────────────────┐            │
│  │ producto_id      : ID del producto                          │            │
│  │ tipo_movimiento  : Tipo de operación                        │            │
│  │ referencia_tipo  : 'ventas', 'compras', 'ajustes'           │            │
│  │ referencia_id    : ID de la operación                       │            │
│  │ cantidad         : +/- según entrada o salida               │            │
│  │ stock_anterior   : Stock antes del movimiento               │            │
│  │ stock_resultante : Stock después del movimiento             │            │
│  │ costo_unitario   : Para valorización                        │            │
│  │ user_id          : Quién realizó la operación               │            │
│  └────────────────────────────────────────────────────────────┘            │
└────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Diccionario de Datos

### 3.1 Tabla: `users`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT | PK autoincremental |
| name | VARCHAR(191) | Nombre completo |
| email | VARCHAR(191) | Email único (login) |
| password | VARCHAR(191) | Contraseña hasheada |
| telefono | VARCHAR(20) | Teléfono de contacto |
| activo | TINYINT(1) | 1=activo, 0=inactivo |
| deleted_at | TIMESTAMP | Soft delete |

### 3.2 Tabla: `productos`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT | PK autoincremental |
| codigo | VARCHAR(50) | Código interno único |
| codigo_barras | VARCHAR(50) | EAN-13 o similar |
| nombre | VARCHAR(200) | Nombre del producto |
| categoria_id | BIGINT | FK a categorias |
| marca_id | BIGINT | FK a marcas |
| unidad_id | BIGINT | FK a unidades |
| stock | DECIMAL(12,3) | Stock actual |
| stock_minimo | DECIMAL(12,3) | Para alertas |
| precio_compra | DECIMAL(10,2) | Último costo |
| precio_venta | DECIMAL(10,2) | Precio al público |
| precio_mayorista | DECIMAL(10,2) | Precio por volumen |
| aplica_igv | TINYINT(1) | 1=gravado, 0=exonerado |

### 3.3 Tabla: `ventas`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT | PK autoincremental |
| caja_sesion_id | BIGINT | FK a caja_sesiones |
| cliente_id | BIGINT | FK a clientes |
| user_id | BIGINT | Cajero que vendió |
| comprobante | ENUM | BOLETA, FACTURA, TICKET |
| serie | VARCHAR(10) | Ej: B001, F001 |
| numero | VARCHAR(20) | Correlativo |
| metodo_pago | ENUM | EFECTIVO, YAPE, etc. |
| subtotal | DECIMAL(12,2) | Base imponible |
| igv | DECIMAL(12,2) | 18% del subtotal |
| total | DECIMAL(12,2) | Monto final |
| estado | ENUM | COMPLETADA, ANULADA |

### 3.4 Tabla: `kardex`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT | PK autoincremental |
| producto_id | BIGINT | FK a productos |
| tipo_movimiento | ENUM | VENTA, COMPRA, AJUSTE, etc. |
| referencia_tipo | VARCHAR(50) | Tabla de origen |
| referencia_id | BIGINT | ID en tabla origen |
| cantidad | DECIMAL(12,3) | +entrada / -salida |
| stock_anterior | DECIMAL(12,3) | Antes del movimiento |
| stock_resultante | DECIMAL(12,3) | Después del movimiento |

---

## 4. Reglas de Negocio

### 4.1 Control de Stock
```
✓ No permitir ventas si stock < cantidad (configurable)
✓ Alertar cuando stock <= stock_minimo
✓ Actualizar stock automáticamente en ventas/compras
✓ Registrar TODO movimiento en kardex
```

### 4.2 Control de Caja
```
✓ Solo una caja abierta por usuario
✓ No permitir ventas sin caja abierta
✓ Calcular diferencia al cierre automáticamente
✓ Registrar quién abrió y quién cerró
```

### 4.3 Comprobantes
```
✓ Correlativos automáticos por serie
✓ No reutilizar números de comprobante
✓ Facturas requieren RUC del cliente
✓ Boletas permiten DNI o cliente genérico
```

### 4.4 Anulaciones
```
✓ Solo usuarios con permiso pueden anular
✓ Registrar motivo y usuario que anula
✓ Revertir stock en kardex
✓ Revertir movimiento de caja
```

---

## 5. Índices Recomendados

```sql
-- Búsquedas frecuentes
INDEX idx_productos_codigo (codigo)
INDEX idx_productos_barcode (codigo_barras)
INDEX idx_productos_nombre (nombre(100))
INDEX idx_clientes_documento (numero_documento)
INDEX idx_ventas_fecha (fecha_emision)
INDEX idx_kardex_producto (producto_id)

-- Filtros de estado
INDEX idx_productos_stock (stock, stock_minimo)
INDEX idx_caja_estado (estado)
INDEX idx_ventas_estado (estado)
```

---

## 6. Consultas SQL Útiles

### Stock bajo mínimo
```sql
SELECT p.codigo, p.nombre, p.stock, p.stock_minimo 
FROM productos p 
WHERE p.stock <= p.stock_minimo AND p.estado = 1;
```

### Ventas del día
```sql
SELECT v.*, c.nombre as cliente
FROM ventas v
LEFT JOIN clientes c ON v.cliente_id = c.id
WHERE DATE(v.fecha_emision) = CURDATE()
  AND v.estado = 'COMPLETADA';
```

### Kardex de un producto
```sql
SELECT k.*, p.nombre
FROM kardex k
JOIN productos p ON k.producto_id = p.id
WHERE k.producto_id = ?
ORDER BY k.created_at DESC;
```

### Resumen de caja
```sql
SELECT 
  cs.monto_inicial,
  SUM(CASE WHEN cm.tipo = 'INGRESO' THEN cm.monto ELSE 0 END) as ingresos,
  SUM(CASE WHEN cm.tipo = 'EGRESO' THEN cm.monto ELSE 0 END) as egresos
FROM caja_sesiones cs
LEFT JOIN caja_movimientos cm ON cs.id = cm.caja_sesion_id
WHERE cs.id = ?
GROUP BY cs.id;
```

---

*Documentación generada el: 2026-01-19*
*Sistema: POS Abarrotes v1.0*
