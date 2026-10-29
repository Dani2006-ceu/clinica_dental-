// =========================================================
//  Dental Alfadent - Imprimir / PDF / Excel de documentos
//  Usa las librerías locales de assetes/js/lib (funcionan sin internet)
// =========================================================
(function () {
    'use strict';

    var hoja = document.querySelector('.hoja');
    var datos = document.body.dataset; // data-archivo, data-titulo, data-periodo, data-clinica

    var btnImprimir = document.getElementById('btnImprimir');
    var btnPdf = document.getElementById('btnPdf');
    var btnExcel = document.getElementById('btnExcel');

    if (btnImprimir) {
        btnImprimir.addEventListener('click', function () { window.print(); });
    }

    // Descarga la hoja como PDF en tamaño A4
    if (btnPdf) {
        btnPdf.addEventListener('click', function () {
            var original = btnPdf.innerHTML;
            btnPdf.disabled = true;
            btnPdf.textContent = 'Generando PDF...';
            var estilo = { margin: hoja.style.margin, boxShadow: hoja.style.boxShadow };
            hoja.style.margin = '0';
            hoja.style.boxShadow = 'none';

            html2pdf().set({
                margin: 0,
                filename: datos.archivo + '.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, backgroundColor: '#ffffff' },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak: { mode: ['css', 'legacy'], avoid: 'tr' }
            }).from(hoja).save().then(function () {
                hoja.style.margin = estilo.margin;
                hoja.style.boxShadow = estilo.boxShadow;
                btnPdf.disabled = false;
                btnPdf.innerHTML = original;
            });
        });
    }

    // Exporta cada tabla marcada con .tabla-excel a una hoja de un archivo .xlsx
    if (btnExcel) {
        btnExcel.addEventListener('click', function () {
            var libro = XLSX.utils.book_new();
            document.querySelectorAll('table.tabla-excel').forEach(function (tabla) {
                var hojaXls = XLSX.utils.aoa_to_sheet([
                    [datos.clinica],
                    [datos.titulo + ' - ' + tabla.dataset.hoja],
                    [datos.periodo],
                    []
                ]);
                XLSX.utils.sheet_add_dom(hojaXls, tabla, { origin: -1 });

                // Ancho de columnas según el contenido
                var anchos = [];
                tabla.querySelectorAll('tr').forEach(function (fila) {
                    Array.prototype.forEach.call(fila.cells, function (celda, i) {
                        anchos[i] = Math.max(anchos[i] || 10, Math.min(celda.textContent.trim().length + 3, 60));
                    });
                });
                hojaXls['!cols'] = anchos.map(function (w) { return { wch: w }; });
                XLSX.utils.book_append_sheet(libro, hojaXls, tabla.dataset.hoja.substring(0, 31));
            });
            XLSX.writeFile(libro, datos.archivo + '.xlsx');
        });
    }
})();
