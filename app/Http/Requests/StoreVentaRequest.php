<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar la creación de una venta
 */
class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Datos de la venta
            'comprobante' => 'required|in:BOLETA,FACTURA,TICKET',
            'serie' => 'nullable|string|max:10',
            'cliente_id' => 'nullable|exists:clientes,id',
            'nombre_cliente_generico' => 'nullable|string|max:150',
            'metodo_pago' => 'required|in:EFECTIVO,TARJETA,YAPE,PLIN,TRANSFERENCIA,MIXTO,CREDITO',
            'monto_recibido' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',

            // Pagos mixtos (opcionales)
            'pago_efectivo' => 'nullable|numeric|min:0',
            'pago_tarjeta' => 'nullable|numeric|min:0',
            'pago_yape' => 'nullable|numeric|min:0',
            'pago_plin' => 'nullable|numeric|min:0',
            'pago_transferencia' => 'nullable|numeric|min:0',

            // Crédito
            'es_credito' => 'nullable|boolean',
            'fecha_vencimiento_credito' => 'nullable|date|after:today',
            'saldo_pendiente' => 'nullable|numeric|min:0',
            'estado_pago' => 'nullable|in:PAGADO,PENDIENTE,PARCIAL',

            // Detalles (productos)
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.001',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.descuento' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'comprobante.required' => 'Debe seleccionar un tipo de comprobante',
            'comprobante.in' => 'Tipo de comprobante no válido',
            'metodo_pago.required' => 'Debe seleccionar un método de pago',
            'monto_recibido.required' => 'Debe ingresar el monto recibido',
            'monto_recibido.min' => 'El monto recibido debe ser mayor a 0',
            'detalles.required' => 'Debe agregar al menos un producto',
            'detalles.min' => 'Debe agregar al menos un producto',
            'detalles.*.producto_id.required' => 'Producto inválido',
            'detalles.*.producto_id.exists' => 'El producto no existe',
            'detalles.*.cantidad.required' => 'La cantidad es requerida',
            'detalles.*.cantidad.min' => 'La cantidad debe ser mayor a 0',
            'detalles.*.precio_unitario.required' => 'El precio es requerido',
        ];
    }

    /**
     * Prepara los datos antes de la validación
     */
    protected function prepareForValidation()
    {
        // Establecer serie por defecto según el tipo de comprobante
        if (!$this->serie) {
            $this->merge([
                'serie' => match($this->comprobante) {
                    'FACTURA' => 'F001',
                    'TICKET' => 'T001',
                    default => 'B001'
                }
            ]);
        }

        // Calcular pago_efectivo automáticamente si no viene del frontend
        $pagoEfectivo = $this->pago_efectivo ?? 0;
        $pagoTarjeta = $this->pago_tarjeta ?? 0;
        $pagoYape = $this->pago_yape ?? 0;
        $pagoPlin = $this->pago_plin ?? 0;
        $pagoTransferencia = $this->pago_transferencia ?? 0;
        
        // Si es pago en EFECTIVO y no se especificó pago_efectivo, usar monto_recibido
        if ($this->metodo_pago === 'EFECTIVO' && $pagoEfectivo == 0) {
            $pagoEfectivo = $this->monto_recibido ?? 0;
        }
        
        // Si es método único (no mixto ni crédito), asignar el monto al campo correspondiente
        if (!in_array($this->metodo_pago, ['MIXTO', 'CREDITO'])) {
            $montoRecibido = $this->monto_recibido ?? 0;
            
            match($this->metodo_pago) {
                'EFECTIVO' => $pagoEfectivo = $montoRecibido,
                'TARJETA' => $pagoTarjeta = $montoRecibido,
                'YAPE' => $pagoYape = $montoRecibido,
                'PLIN' => $pagoPlin = $montoRecibido,
                'TRANSFERENCIA' => $pagoTransferencia = $montoRecibido,
                default => null
            };
        }

        // Valores por defecto
        $this->merge([
            'descuento' => $this->descuento ?? 0,
            'es_credito' => $this->es_credito ?? ($this->metodo_pago === 'CREDITO'),
            'pago_efectivo' => $pagoEfectivo,
            'pago_tarjeta' => $pagoTarjeta,
            'pago_yape' => $pagoYape,
            'pago_plin' => $pagoPlin,
            'pago_transferencia' => $pagoTransferencia,
        ]);
    }
}
