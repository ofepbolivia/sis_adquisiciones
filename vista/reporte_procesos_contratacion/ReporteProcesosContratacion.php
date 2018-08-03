<?php
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ReporteProcesosContratacion = Ext.extend(Phx.frmInterfaz, {
        Atributos : [
            // {
            //     config:{
            //         name:'id_depto',
            //         hiddenName: 'id_depto',
            //         url: '../../sis_parametros/control/Depto/listarDepto',
            //         origen:'DEPTO',
            //         allowBlank:false,
            //         fieldLabel: 'Depto',
            //         gdisplayField:'desc_depto',//dibuja el campo extra de la consulta al hacer un inner join con orra tabla
            //         width:250,
            //         gwidth:180,
            //         baseParams:{estado:'activo',_adicionar:'Todos',codigo_subsistema:'ADQ'}
            //
            //     },
            //     //type:'TrigguerCombo',
            //     type:'ComboRec',
            //     id_grupo:0,
            //     filters:{pfiltro:'depto.nombre',type:'string'},
            //     form:true
            // },
            {
                config:{
                    name: 'monto_mayor',
                    fieldLabel: 'Montos mayor a',
                    allowBlank: true,
                    anchor: '35%',
                    gwidth: 180,
                    maxLength:6
                },
                type:'NumberField',
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: false,
                    disabled: false,
                    gwidth: 180,
                    format: 'd/m/Y'

                },
                type:'DateField',
                id_grupo:0,
                form:true
            },
            {
                config:{
                    name: 'fecha_fin',
                    fieldLabel: 'Fecha Fin',
                    allowBlank: false,
                    disabled: false,
                    gwidth: 180,
                    format: 'd/m/Y'

                },
                type:'DateField',
                id_grupo:0,
                form:true
            }
        ],
        title : 'Generar Reporte',
        ActSave : '../../sis_adquisiciones/control/ProcesoCompra/reporteProcesosContratacion',
        topBar : true,
        botones : false,
        labelSubmit : 'Imprimir',
        tooltipSubmit : '<b>Generar Reporte</b>',
        constructor : function(config) {
            Phx.vista.ReporteProcesosContratacion.superclass.constructor.call(this, config);
            this.init();
        },
        tipo : 'reporte',
        clsSubmit : 'bprint'
    })
</script>