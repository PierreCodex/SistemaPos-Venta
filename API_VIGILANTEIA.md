# API para VigilanteIA — endpoints a habilitar en SistemaPos-Venta

**Fecha:** 2026-07-12 · **Consumidor:** backend Python de VigilanteIA (solo LECTURA)
**Referencia:** `CONTRATO_API_CLIENTE.md` §3 del repo de VigilanteIA.

## Encargo para el agente (leer primero)

**Alcance:** crear UN controller nuevo + 2 rutas. Nada más.

Reglas:
1. **NO modificar nada existente**: ni `ProductoApiController`, ni modelos, ni
   migraciones, ni `web.php`. Solo se AGREGA (`VigilanteApiController` + 2 líneas en
   `routes/api.php` dentro del grupo `auth:sanctum` que ya existe).
2. El código del controller está completo en la sección 1 — úsalo tal cual; si algo
   no compila contra el proyecto real, ajústalo con el criterio de las reglas de
   negocio (no cambies la forma del JSON de respuesta: es un contrato).
3. Comentarios e identificadores en español (convención del proyecto).
4. Sin dependencias nuevas de composer.

Pasos:
- [ ] Crear `app/Http/Controllers/Api/VigilanteApiController.php` (sección 1).
- [ ] Registrar las 2 rutas en `routes/api.php` (sección 1, dentro de `auth:sanctum`).
- [ ] Crear el usuario dedicado + token Sanctum (sección 3) y guardar el token
      donde indique Jean (NO commitearlo).
- [ ] Correr la prueba de humo (sección 4) con un producto real y pegar los 3
      resultados como evidencia.

**Terminado =** los 3 curl de la sección 4 responden como se describe (venta real
sumada, SKU inexistente con `producto_encontrado:false`, sin token → 401) y
`php artisan route:list` muestra las 2 rutas nuevas bajo `api/vigilante/*`.

## Contexto (por qué esto existe)

VigilanteIA vigila un stand con una cámara y cuenta los productos cada 5 minutos.
Cuando un producto BAJA (había 2 cocas, ahora hay 1), necesita preguntarle al POS:
*"¿se vendió una coca-500 entre las 18:37 y las 18:42?"*

- **SÍ hubo venta** → baja legítima ✅ (no pasa nada).
- **NO hubo venta** → 🚨 discrepancia (merma/robo) → alerta al dueño.

Todo es **solo lectura**: VigilanteIA jamás escribe en el POS. La integración completa
se reduce a UN endpoint obligatorio y dos opcionales.

## Lo que YA existe y se reutiliza (no tocar)

| Pieza | Estado | Para qué le sirve a VigilanteIA |
|---|---|---|
| `routes/api.php` con `auth:sanctum` | ✅ | El mecanismo de auth por token ya está |
| `GET /api/productos?buscar=` | ✅ | Catálogo: autocompletar etiquetas de slots en el panel |
| `Venta.fecha_emision` + campos de anulación | ✅ | Filtrar ventas por rango y excluir anuladas |
| `DetalleVenta (venta_id, producto_id, cantidad)` | ✅ | La fuente de "unidades vendidas" |
| `Producto.codigo` / `codigo_barras` / `stock` | ✅ | El "sku" que manda VigilanteIA se busca en ambos |

## 1. Endpoint OBLIGATORIO — ventas por producto en un rango

```
GET /api/vigilante/ventas?sku=7750182ooo45&desde=2026-07-12T18:37:00-05:00&hasta=2026-07-12T18:42:00-05:00
Authorization: Bearer <token sanctum de solo lectura>

200 → { "sku": "7750182ooo45", "unidades_vendidas": 1 }
```

Reglas de negocio:

1. `sku` se busca en `productos.codigo_barras` **o** `productos.codigo` (el dueño puede
   etiquetar el slot con cualquiera de los dos).
2. `unidades_vendidas` = SUMA de `detalle_ventas.cantidad` de las ventas cuya
   `fecha_emision` esté en `[desde, hasta]`.
3. **Excluir ventas anuladas**: `fecha_anulacion IS NULL` (más robusto que comparar
   el string de `estado`).
4. SKU inexistente → `200` con `unidades_vendidas: 0` y `"producto_encontrado": false`
   (NO 404: para VigilanteIA "no existe" equivale a "no se vendió", pero el flag le
   permite avisar al dueño que la etiqueta del slot está mal escrita).
5. `desde`/`hasta` en ISO 8601; si vienen sin timezone, asumir la de la app (Lima).

### Código sugerido (mismo estilo del `ProductoApiController` existente)

`app/Http/Controllers/Api/VigilanteApiController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VigilanteApiController extends Controller
{
    /**
     * Unidades vendidas de un producto en un rango de tiempo.
     * Lo consulta VigilanteIA para clasificar una baja de stock físico:
     * hubo venta = baja legítima; no hubo = posible merma/robo.
     */
    public function ventas(Request $request)
    {
        $request->validate([
            'sku'   => 'required|string|max:120',
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
        ]);

        $sku = $request->get('sku');
        $producto = Producto::withTrashed()
            ->where('codigo_barras', $sku)
            ->orWhere('codigo', $sku)
            ->first();

        if (!$producto) {
            return response()->json([
                'sku' => $sku,
                'unidades_vendidas' => 0,
                'producto_encontrado' => false,
            ]);
        }

        $unidades = DetalleVenta::where('producto_id', $producto->id)
            ->whereHas('venta', function ($q) use ($request) {
                $q->whereBetween('fecha_emision', [
                        Carbon::parse($request->get('desde')),
                        Carbon::parse($request->get('hasta')),
                    ])
                  ->whereNull('fecha_anulacion'); // las anuladas no justifican bajas
            })
            ->sum('cantidad');

        return response()->json([
            'sku' => $sku,
            'unidades_vendidas' => (float) $unidades,
            'producto_encontrado' => true,
        ]);
    }

    /**
     * Stock teórico actual de un producto (OPCIONAL — mejora el panel:
     * "teórico vs observado por la cámara").
     */
    public function stock(Request $request)
    {
        $request->validate(['sku' => 'required|string|max:120']);
        $sku = $request->get('sku');

        $producto = Producto::where('codigo_barras', $sku)
            ->orWhere('codigo', $sku)
            ->first();

        if (!$producto) {
            return response()->json([
                'sku' => $sku,
                'producto_encontrado' => false,
            ], 200);
        }

        return response()->json([
            'sku' => $sku,
            'nombre' => $producto->nombre,
            'stock' => (float) $producto->stock,
            'producto_encontrado' => true,
        ]);
    }
}
```

En `routes/api.php`, dentro del grupo `auth:sanctum` existente:

```php
use App\Http\Controllers\Api\VigilanteApiController;

// VigilanteIA (solo lectura)
Route::get('/vigilante/ventas', [VigilanteApiController::class, 'ventas']);
Route::get('/vigilante/stock',  [VigilanteApiController::class, 'stock']);
```

> Nota: `Producto::withTrashed()` en `ventas()` es a propósito — un producto
> descontinuado (soft-deleted) igual pudo venderse dentro del rango consultado.
> En `stock()` NO se usa: el stock de un descontinuado no interesa.

## 2. Endpoints OPCIONALES (mejoran el panel, no bloquean)

| Endpoint | Estado | Uso |
|---|---|---|
| `GET /api/vigilante/stock?sku=` | código arriba | Mostrar teórico vs observado |
| `GET /api/productos?buscar=` | **ya existe** | Autocompletar etiquetas al configurar slots |

## 3. Token de solo lectura para VigilanteIA

Crear un usuario dedicado (ej. `vigilante@sistema.local`) y emitirle un token Sanctum:

```php
// php artisan tinker
$user = \App\Models\User::where('email', 'vigilante@sistema.local')->first();
$token = $user->createToken('vigilante-ia', ['vigilante:read'])->plainTextToken;
```

Ese token se pega en el módulo **"Integración"** del panel VigilanteIA (tipo:
SistemaPos-Venta, URL base: `http://<host>/SistemaPos-Venta/public`, token). No usar
el token de un usuario real: si algún día se filtra, se revoca sin afectar a nadie.

## 4. Prueba de humo (antes de conectar nada)

```bash
# 1. Producto real con ventas de hoy:
curl -H "Authorization: Bearer TOKEN" \
  "http://localhost/SistemaPos-Venta/public/api/vigilante/ventas?sku=CODIGO_REAL&desde=2026-07-12T00:00:00-05:00&hasta=2026-07-12T23:59:59-05:00"
# → unidades_vendidas > 0

# 2. SKU inventado:
curl -H "Authorization: Bearer TOKEN" \
  ".../api/vigilante/ventas?sku=no-existe&desde=...&hasta=..."
# → {"sku":"no-existe","unidades_vendidas":0,"producto_encontrado":false}

# 3. Sin token → 401.
```

## 5. Qué hace VigilanteIA con esto (contexto, nada que implementar aquí)

El backend Python tendrá un adaptador `SistemaPosVentaAdapter` (puerto
`IClienteVentas`) que llama a `/api/vigilante/ventas` cuando el conteo de un SKU baja.
La URL y el token se configuran por tienda en el panel (tabla `integraciones`) — este
POS es la integración de demo. Regla anti-falso-positivo: si este endpoint no
responde, VigilanteIA marca el evento `pendiente_conciliar` y reintenta — **nunca
alerta merma sin haber podido consultar ventas**.
