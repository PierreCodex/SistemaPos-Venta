<?php

namespace App\Services;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Roles administrativos que ven datos globales
     */
    protected array $rolesAdministrativos = ['super-admin', 'Admin', 'administrador'];

    /**
     * Verifica si el usuario actual es administrador
     */
    protected function esAdmin(): bool
    {
        $user = Auth::user();
        return $user ? $user->hasAnyRole($this->rolesAdministrativos) : false;
    }

    /**
     * Aplica filtro de usuario a una query de Venta si no es admin
     */
    protected function aplicarFiltroUsuario($query)
    {
        if (!$this->esAdmin()) {
            $query->where('user_id', Auth::id());
        }
        return $query;
    }

    /**
     * Obtiene las estadísticas resumidas para los widgets superiores
     */
    public function obtenerEstadisticasWidgets()
    {
        $hoy = Carbon::today();
        
        // Ventas de Hoy
        $queryVentasHoy = Venta::completadas()->whereDate('fecha_emision', $hoy);
        $this->aplicarFiltroUsuario($queryVentasHoy);
        $ventasHoy = $queryVentasHoy->sum('total');
            
        $queryCantHoy = Venta::completadas()->whereDate('fecha_emision', $hoy);
        $this->aplicarFiltroUsuario($queryCantHoy);
        $cantidadVentasHoy = $queryCantHoy->count();

        // Ventas del mes
        $queryVentasMes = Venta::completadas()
            ->whereMonth('fecha_emision', $hoy->month)
            ->whereYear('fecha_emision', $hoy->year);
        $this->aplicarFiltroUsuario($queryVentasMes);
        $ventasMes = $queryVentasMes->sum('total');

        // Productos con bajo stock (dato global, no depende del vendedor)
        $productosBajoStock = Producto::where('stock', '<=', DB::raw('stock_minimo'))
            ->count();

        // Clientes nuevos este mes (dato global, no depende del vendedor)
        $clientesNuevos = Cliente::whereMonth('created_at', $hoy->month)
            ->whereYear('created_at', $hoy->year)
            ->count();

        return [
            'ventas_hoy' => $ventasHoy,
            'cantidad_ventas_hoy' => $cantidadVentasHoy,
            'ventas_mes' => $ventasMes,
            'productos_bajo_stock' => $productosBajoStock,
            'clientes_nuevos' => $clientesNuevos
        ];
    }

    /**
     * Obtiene los productos más vendidos
     */
    public function obtenerProductosMasVendidos($limit = 5)
    {
        $query = DetalleVenta::select('producto_id', DB::raw('SUM(cantidad) as total_cantidad'), DB::raw('SUM(subtotal) as total_monto'))
            ->with('producto');

        // Filtrar por ventas del usuario si no es admin
        if (!$this->esAdmin()) {
            $query->whereHas('venta', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        return $query->groupBy('producto_id')
            ->orderBy('total_cantidad', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtiene el desglose por métodos de pago
     */
    public function obtenerMetodosPago($limit = 5)
    {
        $query = Venta::completadas()
            ->select('metodo_pago', DB::raw('COUNT(*) as total_ventas'), DB::raw('SUM(total) as total_monto'));

        $this->aplicarFiltroUsuario($query);

        return $query->groupBy('metodo_pago')
            ->orderBy('total_monto', 'desc')
            ->get();
    }

    /**
     * Obtiene las ventas de los últimos 7 días
     */
    public function obtenerVentasUltimos7Dias()
    {
        $fechas = [];
        $montos = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $fechas[] = $fecha->format('d M');
            
            $query = Venta::completadas()->whereDate('fecha_emision', $fecha);
            $this->aplicarFiltroUsuario($query);
            $monto = $query->sum('total');
                
            $montos[] = (float)$monto;
        }

        return [
            'fechas' => $fechas,
            'montos' => $montos
        ];
    }

    /**
     * Obtiene las ventas recientes
     */
    public function obtenerVentasRecientes($limit = 10)
    {
        $query = Venta::with(['cliente'])
            ->orderBy('fecha_emision', 'desc');

        $this->aplicarFiltroUsuario($query);

        return $query->limit($limit)->get();
    }

    /**
     * Obtiene el Top de Clientes
     */
    public function obtenerTopClientes($limit = 5)
    {
        $query = Venta::completadas()
            ->select('cliente_id', 'nombre_cliente_generico', DB::raw('COUNT(*) as total_ventas'), DB::raw('SUM(total) as total_gastado'))
            ->with('cliente');

        $this->aplicarFiltroUsuario($query);

        return $query->groupBy('cliente_id', 'nombre_cliente_generico')
            ->orderBy('total_gastado', 'desc')
            ->limit($limit)
            ->get();
    }
}
