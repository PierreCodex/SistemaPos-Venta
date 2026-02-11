<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Empresa;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Muestra la vista pública de un ticket de venta
     */
    public function publico($codigo)
    {
        $venta = Venta::with(['detalles.producto', 'cliente', 'vendedor', 'comprobanteElectronico'])
            ->where('codigo_externo', $codigo)
            ->firstOrFail();

        $empresa = Empresa::principal();

        return view('tickets.publico', compact('venta', 'empresa'));
    }
}
