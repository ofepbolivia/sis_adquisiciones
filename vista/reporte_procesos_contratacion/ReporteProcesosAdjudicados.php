<?php

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ReporteProcesosAdjudicados = Ext.extend(Phx.frmInterfaz, {
        Atributos: [
            {
                config: {
                    name: 'monto_mayor',
                    fieldLabel: 'Montos mayor a',
                    allowBlank: true,
                    anchor: '35%',
                    gwidth: 80,
                    maxLength: 10
                },
                type: 'NumberField',
                valorInicial: '20000',
                form: true
            },
            {
                config: {
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: false,
                    disabled: false,
                    gwidth: 100,
                    format: 'd/m/Y'

                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            },
            {
                config: {
                    name: 'fecha_fin',
                    fieldLabel: 'Fecha Fin',
                    allowBlank: false,
                    disabled: false,
                    gwidth: 100,
                    format: 'd/m/Y'

                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            }
        ],
        title: 'Generar Reporte',
        ActSave: '../../sis_adquisiciones/control/ProcesoCompra/reporteProcesosContraAdj',
        topBar: true,
        botones: false,
        labelSubmit: 'Imprimir',
        tooltipSubmit: '<b>Generar Reporte</b>',
        constructor: function (config) {
            Phx.vista.ReporteProcesosAdjudicados.superclass.constructor.call(this, config);
            this.init();
        },
        tipo: 'reporte',
        clsSubmit: 'bprint'
    })
</script>