<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Greenter para Facturación Electrónica SUNAT
    |--------------------------------------------------------------------------
    |
    | Aquí se configuran los parámetros necesarios para la integración con
    | los servicios de facturación electrónica de SUNAT usando Greenter.
    |
    */

    // Modo de operación: 'beta' para pruebas, 'production' para producción
    'mode' => env('GREENTER_MODE', 'beta'),

    // Credenciales Clave SOL
    'credentials' => [
        'ruc' => env('GREENTER_RUC', '20000000001'),
        'username' => env('GREENTER_USERNAME', 'MODDATOS'),
        'password' => env('GREENTER_PASSWORD', 'moddatos'),
    ],

    // Ruta del certificado digital (.pem)
    'certificate' => [
        'path' => env('GREENTER_CERTIFICATE_PATH', storage_path('certificates/certificate.pem')),
    ],

    // Endpoints de SUNAT
    'endpoints' => [
        'beta' => [
            'fe' => 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService',
            'guia' => 'https://e-beta.sunat.gob.pe/ol-ti-itemision-guia-gem-beta/billService',
            'retenciones' => 'https://e-beta.sunat.gob.pe/ol-ti-itemision-otroscpe-gem-beta/billService',
        ],
        'production' => [
            'fe' => 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService',
            'guia' => 'https://e-guiaremision.sunat.gob.pe/ol-ti-itemision-guia-gem/billService',
            'retenciones' => 'https://e-factura.sunat.gob.pe/ol-ti-itemision-otroscpe-gem/billService',
        ],
    ],

    // Configuración de la empresa emisora
    'company' => [
        'ruc' => env('COMPANY_RUC', '20000000001'),
        'razon_social' => env('COMPANY_RAZON_SOCIAL', 'EMPRESA DE PRUEBA'),
        'nombre_comercial' => env('COMPANY_NOMBRE_COMERCIAL', 'EMPRESA PRUEBA'),
        'address' => [
            'ubigeo' => env('COMPANY_UBIGEO', '150101'), // Lima - Lima - Lima
            'departamento' => env('COMPANY_DEPARTAMENTO', 'LIMA'),
            'provincia' => env('COMPANY_PROVINCIA', 'LIMA'),
            'distrito' => env('COMPANY_DISTRITO', 'LIMA'),
            'urbanizacion' => env('COMPANY_URBANIZACION', '-'),
            'direccion' => env('COMPANY_DIRECCION', 'AV. PRUEBA 123'),
            'codigo_pais' => env('COMPANY_CODIGO_PAIS', 'PE'),
        ],
    ],

    // Configuración de series por tipo de comprobante
    'series' => [
        'factura' => env('GREENTER_SERIE_FACTURA', 'F001'),
        'boleta' => env('GREENTER_SERIE_BOLETA', 'B001'),
        'nota_credito' => env('GREENTER_SERIE_NOTA_CREDITO', 'FC01'),
        'nota_debito' => env('GREENTER_SERIE_NOTA_DEBITO', 'FD01'),
        'guia_remision' => env('GREENTER_SERIE_GUIA', 'T001'),
    ],

    // Rutas para almacenar XMLs y PDFs generados
    'storage' => [
        'xml' => storage_path('app/greenter/xml'),
        'pdf' => storage_path('app/greenter/pdf'),
        'cdr' => storage_path('app/greenter/cdr'), // Constancia de Recepción
    ],

    // Configuración de logs
    'logs' => [
        'enabled' => env('GREENTER_LOGS_ENABLED', true),
        'path' => storage_path('logs/greenter.log'),
    ],
];
