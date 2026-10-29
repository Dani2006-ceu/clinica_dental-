<?php
// Íconos SVG en línea (estilo de trazo, 24x24). Uso: icono('calendario')
function icono(string $nombre, string $clase = 'ico'): string
{
    static $trazos = [
        'inicio'       => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M10 21v-6h4v6"/>',
        'pacientes'    => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><circle cx="17.5" cy="9" r="2.5"/><path d="M17 14.2c2.6.3 4.5 2.4 4.5 5.3"/>',
        'odontologos'  => '<path d="M6 3v6a5 5 0 0 0 10 0V3"/><path d="M4 3h4M14 3h4"/><path d="M11 14v2a5 5 0 0 0 10 0v-1"/><circle cx="21" cy="12.5" r="2"/>',
        'citas'        => '<rect x="3" y="4.5" width="18" height="16.5" rx="2.5"/><path d="M3 9.5h18M8 2.5v4M16 2.5v4"/><path d="m9 15 2 2 4-4"/>',
        'diagnosticos' => '<rect x="5" y="3.5" width="14" height="18" rx="2"/><path d="M9 3.5V2.5h6v1"/><path d="M9 10h6M12 7v6M9 17h6"/>',
        'tratamientos' => '<path d="M7.5 3C5 3 3.5 5 3.5 7.5c0 2.6 1.3 4.4 1.8 7 .5 3 1 6.5 2.6 6.5 1.7 0 1.6-3.6 2.6-5.6.4-.8.9-1.4 1.5-1.4s1.1.6 1.5 1.4c1 2 .9 5.6 2.6 5.6 1.6 0 2.1-3.5 2.6-6.5.5-2.6 1.8-4.4 1.8-7C20.5 5 19 3 16.5 3c-2 0-2.8 1-4.5 1S9.5 3 7.5 3z"/>',
        'servicios'    => '<rect x="3" y="7" width="18" height="13.5" rx="2.5"/><path d="M8.5 7V5a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v2"/><path d="M12 11.5v5M9.5 14h5"/>',
        'pagos'        => '<rect x="2.5" y="5" width="19" height="14" rx="2.5"/><path d="M2.5 10h19M6.5 15h4"/>',
        'reportes'     => '<path d="M3 3v18h18"/><rect x="7" y="11" width="3" height="6" rx=".5"/><rect x="12" y="7" width="3" height="10" rx=".5"/><rect x="17" y="13" width="3" height="4" rx=".5"/>',
        'especialidad' => '<circle cx="12" cy="9" r="6"/><path d="m8.5 14 -1.5 7.5 5-2.5 5 2.5-1.5-7.5"/>',
        'usuarios'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/>',
        'telefono'     => '<path d="M5 3h3.5l1.8 4.5-2.3 1.4a11 11 0 0 0 5.1 5.1l1.4-2.3L19 13.5V17a2 2 0 0 1-2.2 2A16 16 0 0 1 3 5.2 2 2 0 0 1 5 3z"/>',
        'ubicacion'    => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
        'reloj'        => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
        'correo'       => '<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="m3.5 6.5 8.5 6.5 8.5-6.5"/>',
        'candado'      => '<rect x="4.5" y="10.5" width="15" height="10.5" rx="2.5"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/>',
        'ojo'          => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
        'salir'        => '<path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3"/><path d="M10 16l4-4-4-4M14 12H4"/>',
        'mas'          => '<path d="M12 5v14M5 12h14"/>',
        'buscar'       => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'escudo'       => '<path d="M12 3 4.5 6v5.5c0 4.6 3.2 8.4 7.5 9.5 4.3-1.1 7.5-4.9 7.5-9.5V6z"/><path d="m9 12 2 2 4-4"/>',
        'imprimir'     => '<path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="2"/><path d="M6 14h12v7H6z"/>',
        'flecha'       => '<path d="m9 6 6 6-6 6"/>',
        'menu'         => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'dinero'       => '<rect x="2.5" y="6" width="19" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M6 9.5v5M18 9.5v5"/>',
        'pendiente'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3 1.8"/><path d="M12 3v1.5"/>',
        'usuario'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/>',
        'sonrisa'      => '<circle cx="12" cy="12" r="9"/><path d="M8 14.5c1 1.4 2.4 2 4 2s3-.6 4-2"/><path d="M9 9.5h.01M15 9.5h.01"/>',
    ];
    $d = $trazos[$nombre] ?? $trazos['flecha'];
    return '<svg class="' . $clase . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
}
