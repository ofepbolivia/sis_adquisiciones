<?php
/**
 *@package pXP
 *@file    ProcesosIniciadosAdjudicadosEjecutados.php
 *@author  Gonzalo Sarmiento Sejas
 *@date    24-11-2016
 *@description Reporte Procesos Iniciados, Adjudicados y Ejecutados
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ProcesosIniciadosAdjudicadosEjecutados = Ext.extend(Phx.frmInterfaz, {
        Atributos : [
            {
                config:{
                    name:'id_depto',
                    hiddenName: 'id_depto',
                    url: '../../sis_parametros/control/Depto/listarDepto',
                    origen:'DEPTO',
                    allowBlank:true,
                    fieldLabel: 'Depto',
                    gdisplayField:'desc_depto',//dibuja el campo extra de la consulta al hacer un inner join con orra tabla
                    width:250,
                    gwidth:180,
                    qtip: 'Si este campo esta vacio, se listara todos los Deptos',
                    baseParams:{estado:'activo',codigo_subsistema:'ADQ'}

                },
                //type:'TrigguerCombo',
                type:'ComboRec',
                id_grupo:0,
                filters:{pfiltro:'depto.nombre',type:'string'},
                form:true
            },
            {
                config:{
                    name: 'monto_mayor',
                    fieldLabel: 'Montos mayor a',
                    allowBlank: true,
                    width:250,
                    gwidth: 80,
                    maxLength:6
                },
                type:'NumberField',
                valorInicial: 1,
                grid:true,
                form:true
            },
            {
                config:{
                    name : 'id_gestion',
                    origen : 'GESTION',
                    fieldLabel : 'Gestión',
                    allowBlank : true,
                    resizable:true,
                    gdisplayField : 'gestion',//mapea al store del grid
                    width:250,
                    gwidth : 100,
                    qtip: 'Gestión según Solicitudes de Compra',
                    pageSize: 5,
                    renderer : function (value, p, record){return String.format('{0}', record.data['gestion']);}
                },
                type : 'ComboRec',
                id_grupo : 2,
                filters : {
                    pfiltro : 'ges.gestion',
                    type : 'numeric'
                },
                form : true
            },
            {
                config:{
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: false,
                    disabled: false,
                    width:250,
                    gwidth: 100,
                    format: 'd/m/Y',
                    qtip: 'Fecha inicio del Proceso de Compra'

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
                    width:250,
                    gwidth: 100,
                    format: 'd/m/Y',
                    qtip: 'Fecha fin del Proceso de Compra'

                },
                type:'DateField',
                id_grupo:0,
                form:true
            }
        ],
        title : 'Generar Reporte',
        ActSave : '../../sis_adquisiciones/control/ProcesoCompra/procesosIniciadosAdjudicadosEjecutados',
        topBar : true,
        botones : false,
        labelSubmit : 'Imprimir',
        tooltipSubmit : '<b>Generar Reporte</b>',
        constructor : function(config) {
            Phx.vista.ProcesosIniciadosAdjudicadosEjecutados.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
        },

        iniciarEventos: function () {
            this.Cmp.id_gestion.on('change', function (cmb, newval, oldval) {
                if (newval == '') {
                    this.mostrarComponente(this.Cmp.fecha_ini);
                    this.mostrarComponente(this.Cmp.fecha_fin);
                }else{
                    this.ocultarComponente(this.Cmp.fecha_ini);
                    this.ocultarComponente(this.Cmp.fecha_fin);
                }
            }, this);

            this.Cmp.fecha_ini.on('change', function (cmb, newval, oldval) {
                if (newval == '') {
                    this.mostrarComponente(this.Cmp.id_gestion);
                }else{
                    this.ocultarComponente(this.Cmp.id_gestion);
                }
            }, this);
        },
        tipo : 'reporte',
        clsSubmit : 'bprint'
    })
</script>