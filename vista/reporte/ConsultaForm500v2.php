<?php
/**
 * @package pXP
 * @file ConsultaForm500v2.php
 * @author  Maylee Perez Pastor
 * @date 21-05-2019 01:52:07
 * @description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ConsultaForm500v2 = Ext.extend(Phx.gridInterfaz, {

        bnew: false,
        bedit: false,
        bdel: false,
        ActList: '../../sis_adquisiciones/control/Reporte/listarForm500v2',
        gruposBarraTareas: [
            {
                name: 'no',
                title: '<H1 align="center"><i class="fa fa-file-o"></i>Sin Formulario</h1>',
                grupo: 1,
                height: 0,
                width: 100
            },
            {
                name: 'si',
                title: '<H1 align="center"><i class="fa fa-file-text"></i>Con Formulario</h1>',
                grupo: 0,
                height: 0,
                width: 100
            }
        ],
        bactGroups: [0, 1],
        bexcelGroups: [0, 1],
        formulario: 'no',
        actualizarSegunTab: function (name, indice) {
            this.store.baseParams.chequeado = name;
            this.formulario = name;
            this.load({params: {start: 0, limit: this.tam_pag}});
        },

        constructor: function (config) {
            this.tbarItems = [
                '-', this.cmbGestion, '-', this.cmbAux, '-'
            ];
            this.maestro = config;

            Ext.Ajax.request({
                url: '../../sis_adquisiciones/control/Reporte/getDatosUsuario',
                params: {id_usuario: 0},
                success: function (resp) {
                    var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                    this.cmbAux.setValue(reg.ROOT.datos.id_usuario);
                    this.cmbAux.setRawValue(reg.ROOT.datos.desc_usuario);
                    this.store.baseParams.chequeado = this.formulario;
                    this.store.baseParams.id_usuario = reg.ROOT.datos.id_usuario;
                    this.load({params: {start: 0, limit: this.tam_pag}});
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
            Ext.Ajax.request({
                url: '../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                params: {fecha: new Date()},
                success: function (resp) {
                    var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                    this.cmbGestion.setValue(reg.ROOT.datos.id_gestion);
                    this.cmbGestion.setRawValue(reg.ROOT.datos.anho);
                    this.store.baseParams.id_gestion = reg.ROOT.datos.id_gestion;
                    this.load({params: {start: 0, limit: this.tam_pag}});
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });


            //llama al constructor de la clase padre
            Phx.vista.ConsultaForm500v2.superclass.constructor.call(this, config);
            this.init();
            this.cmbGestion.on('select', this.capturarEventos, this);
            //this.load({params:{start:0, limit: 50}});

            this.addButton('btnChequeoDocumentosWf', {
                text: 'Documentos',
                grupo: [0, 1, 2, 3, 4, 5],
                iconCls: 'bchecklist',
                disabled: true,
                handler: this.loadCheckDocumentosRecWf,
                tooltip: '<b>Documentos del Reclamo</b><br/>Subir los documetos requeridos en el Reclamo seleccionado.'
            });

            this.addButton('diagrama_gantt', {
                grupo: [0, 1, 2, 3, 4, 5],
                text: 'Gant',
                iconCls: 'bgantt',
                disabled: true,
                handler: this.diagramGantt,
                tooltip: '<b>Diagrama Gantt de proceso macro</b>'
            });

            /*this.addButton('form500',{
                grupo:[0,1,2,3,4,5],
                text :'Form. 500',
                iconCls : 'bballot',
                disabled: false,
                handler : this.reporteForm500,
                tooltip : '<b>Procesos Pendientes</b><br/>Reporte que muestra los procesos pendientes del formulario 500.'
            });*/
            this.cmbAux.on('select', this.capturarFiltros, this);
        },

        capturarFiltros: function () {
            this.store.baseParams.id_usuario = this.cmbAux.getValue();
            this.store.baseParams.id_gestion = this.cmbGestion.getValue();
            this.store.baseParams.chequeado = this.formulario;
            this.load({params: {start: 0, limit: this.tam_pag}});
        },

        cmbGestion: new Ext.form.ComboBox({
            name: 'gestion',
            //id: 'gestion_reg',
            fieldLabel: 'Gestion',
            allowBlank: true,
            emptyText: 'Gestion...',
            blankText: 'Año',
            editable: false,
            store: new Ext.data.JsonStore(
                {
                    url: '../../sis_parametros/control/Gestion/listarGestion',
                    id: 'id_gestion',
                    root: 'datos',
                    sortInfo: {
                        field: 'gestion',
                        direction: 'DESC'
                    },
                    totalProperty: 'total',
                    fields: ['id_gestion', 'gestion'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams: {par_filtro: 'gestion'}
                }),
            valueField: 'id_gestion',
            triggerAction: 'all',
            displayField: 'gestion',
            hiddenName: 'id_gestion',
            mode: 'remote',
            pageSize: 50,
            queryDelay: 500,
            listWidth: '280',
            hidden: false,
            width: 80
        }),


        cmbAux: new Ext.form.ComboBox({
            name: 'id_usuario',
            hiddenName: 'id_depto_usuario',
            fieldLabel: 'Auxiliar',
            listWidth: 280,
            allowBlank: true,
            store: new Ext.data.JsonStore(
                {
                    url: '../../sis_parametros/control/DeptoUsuario/listarDeptoUsuario',
                    id: 'id_usuario',
                    root: 'datos',
                    sortInfo: {
                        field: 'id_usuario',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_depto_usuario', 'id_usuario', 'desc_usuario', 'cargo'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams: {par_filtro: 'person.nombre_completo1', id_depto: 2, _adicionar:'Todos'}
                }),
            valueField: 'id_usuario',
            displayField: 'desc_usuario',
            forceSelection: true,
            typeAhead: false,
            triggerAction: 'all',
            lazyRender: true,
            mode: 'remote',
            pageSize: 50,
            queryDelay: 500,
            width: 210,
            gwidth: 220,
            minChars: 2,
            tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>{desc_usuario}</b></p>Tarea: <strong style="color: green;">{cargo}</strong> </div></tpl>'

        }),

        reporteForm500: function () {
            Ext.Ajax.request({
                url: '../../sis_adquisiciones/control/ProcesoCompra/reportePendientesForm500',
                params: {
                    id_usuario: this.cmbAux.getValue()
                },
                success: function (resp) {
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    window.open('../../../lib/lib_control/Intermediario.php?r=' + reg.ROOT.detalle.archivo_generado + '&t=' + new Date().toLocaleTimeString());
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },

        loadCheckDocumentosRecWf: function () {
            var rec = this.sm.getSelected();
            Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
                'Chequear documento del WF',
                {
                    width: '90%',
                    height: 500
                },
                rec.data,
                this.idContenedor,
                'DocumentoWf'
            )
        },

        diagramGantt: function () {
            var data = this.sm.getSelected().data.id_proceso_wf;
            //Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_workflow/control/ProcesoWf/diagramaGanttTramite',
                params: {'id_proceso_wf': data},
                success: this.successExport,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },

        cmbTipo: new Ext.form.ComboBox({
            name: 'origen',
            //fieldLabel: 'Origen',
            allowBlank: true,
            anchor: '80%',
            gwidth: 100,
            maxLength: 25,
            typeAhead: true,
            forceSelection: true,
            triggerAction: 'all',
            mode: 'local',
            store: ['Todos', 'Proceso']
        }),

        capturarEventos: function () {
            this.store.baseParams.id_usuario = this.cmbAux.getValue();
            this.store.baseParams.tipo = this.cmbTipo.getValue();
            this.store.baseParams.id_gestion = this.cmbGestion.getValue();
            this.load({params: {start: 0, limit: 50}});
        },

        Atributos: [
            {
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_proceso_compra'
                },
                type: 'Field',
                form: false
            },
            {
                config: {
                    name: 'num_tramite',
                    fieldLabel: 'Nro. Trámite',
                    allowBlank: true,
                    anchor: '50%',
                    gwidth: 125,
                    maxLength: 20,
                    readOnly: true,
                    renderer: function (value, p, record) {
                        return String.format('<b><font color="green">{0}</font></b>', value);
                    }
                },
                type: 'TextField',
                bottom_filter: true,
                filters: {pfiltro: 'num_tramite', type: 'string'},
                id_grupo: 0,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'nro_cuota',
                    fieldLabel: 'Nro. Cuota',
                    allowBlank: true,
                    anchor: '50%',
                    gwidth: 125,
                    maxLength: 20,
                    readOnly: true,
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['nro_cuota']);
                    }

                },
                type: 'TextField',
                bottom_filter: true,
                filters: {pfiltro: 'nro_cuota', type: 'string'},
                id_grupo: 0,
                grid: true,
                form: false
            },

            {
                config: {
                    name: 'fecha_inicio',
                    fieldLabel: 'Fecha Solicitud',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    format: 'd/m/Y H:i',
                    renderer: function (value, p, record) {
                        var fecha = value ? value.dateFormat('d/m/Y') : '';
                        return String.format('{0}', "<div style='text-align:center;color:#0A5A88'><b>" + fecha + "</b></div>");

                    }

                },
                type: 'DateField',
                filters: {pfiltro: 'ts.fecha_inicio', type: 'date'},
                id_grupo: 0,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'fecha_contrato',
                    fieldLabel: 'Fecha inicio',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y H:i',
                    renderer: function (value, p, record) {
                        var fecha = value ? value.dateFormat('d/m/Y') : '';
                        return String.format('{0}', "<div style='text-align:center;color:orange'><b>" + fecha + "</b></div>");

                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'tleg.fecha_contrato', type: 'date'},
                id_grupo: 0,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'fecha_conclusion',
                    fieldLabel: 'Fecha conclusión',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y H:i',
                    renderer: function (value, p, record) {
                        var fecha = value ? value.dateFormat('d/m/Y') : '';
                        return String.format('{0}', "<div style='text-align:center;color:coral'><b>" + fecha + "</b></div>");

                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'fecha_conclusion', type: 'date'},
                id_grupo: 0,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'dias_form_500',
                    fieldLabel: 'Dias Hábiles para Presentar Form. 500',
                    allowBlank: true,
                    anchor: '50%',
                    gwidth: 180,
                    maxLength: 20,
                    readOnly: true,
                    renderer: function (value, p, record) {
                        var numero = record.data.dias_form_500;
                        if (record.data.tieneform500 == 'TIENE FORM 500') {
                            return String.format('{0}', "<div style='text-align:center'><img title='Cuenta con el formulario 500'  src = '../../../lib/images/numeros/respondido.png' align='center' width='24' height='24'/></div>");
                        } else if (numero == null || numero > 15 || numero < 0) {
                            return String.format('{0}', "<div style='text-align:center'><img title='Vencio el plazo para adjuntar formulario 500'  src = '../../../lib/images/numeros/cancel-event.png' align='center' width='24' height='24'/></div>");
                        } else {
                            return String.format('{0}', "<div style='text-align:center'><img title='Usted tiene " + numero + " dias para adjuntar formulario 500.'  src = '../../../lib/images/numeros/" + numero + ".png' align='center' width='24' height='24'/></div>");
                        }

                    }
                },
                type: 'TextField',

                id_grupo: 0,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'fecha_venc',
                    fieldLabel: 'Fecha Vencimiento',
                    allowBlank: false,
                    anchor: '50%',
                    gwidth: 100,


                    renderer: function (value, p, record) {
                        if (value != '') {
                            var fecha = new Date(value);
                            var fecha2 = fecha ? fecha.dateFormat('d/m/Y') : '';
                            var hoy = new Date();
                            var hoy2 = hoy ? hoy.dateFormat('d/m/Y') : '';
                            if (fecha2 == hoy2) {
                                return String.format('<b><font color="#8b0000">{0}</font></b>', fecha2);
                            } else {
                                return String.format('<b><font color="red">{0}</font></b>', fecha2);
                            }
                        } else {
                            return String.format(value);
                        }


                    }
                },
                type: 'TextField',
                bottom_filter: true,
                filters: {pfiltro: 'fecha_venc', type: 'string'},
                id_grupo: 0,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'fecha_conformidad',
                    fieldLabel: 'Fecha Conformidad',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 110,
                    format: 'd/m/Y H:i',

                    renderer: function (value, p, record) {

                        var fecha = value ? value.dateFormat('d/m/Y') : '';
                        if (fecha != '')
                            return String.format('{0}', "<div style='text-align:center;color: #0A5A88'><b>" + fecha + "</b></div>");
                        else
                            return String.format('{0}', "<div style='text-align:center;color: #0A5A88'><b>" + fecha + "</b></div>");
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'tpp.fecha_conformidad', type: 'date'},
                id_grupo: 0,
                grid: true,
                form: true
            },

            {
                config: {
                    name: 'fun_solicitante',
                    fieldLabel: 'Funcionario Solicitante',
                    allowBlank: false,
                    anchor: '50%',
                    gwidth: 250/*,
                     renderer:function (value, p, record){return String.format('{0}', record.data['desc_funcionario']);}*/
                },
                type: 'TextField',
                bottom_filter: true,
                filters: {pfiltro: 'fun_solicitante', type: 'string'},
                id_grupo: 0,
                grid: true,
                form: false
            },

            {
                config: {
                    name: 'tieneform500',
                    fieldLabel: 'Formulario 500',
                    allowBlank: true,
                    anchor: '50%',
                    height: 80,
                    gwidth: 150,
                    maxLength: 100,
                    renderer: function (value, p, record) {
                        if (record.data.tieneform500 == 'TIENE FORM 500')
                            return String.format('{0}', "<div style='text-align:center;color: green'><b>" + value + "</b></div>");
                        else
                            return String.format('{0}', "<div style='text-align:center;color: red'><b>" + value + "</b></div>");
                    }
                },
                type: 'TextField',
                filters: {pfiltro: 'tieneform500', type: 'string'},
                id_grupo: 0,
                grid: true,
                form: false
            },

            {
                config: {
                    name: 'conformidad',
                    fieldLabel: 'Conformidad Ultima Cuota',
                    allowBlank: true,
                    anchor: '50%',
                    height: 80,
                    gwidth: 150,
                    maxLength: 100,
                    renderer: function (value, p, record) {
                        if (record.data.conformidad == 'TIENE CONFORMIDAD')
                            return String.format('{0}', "<div style='text-align:center;color: green'><b>" + value + "</b></div>");
                        else
                            return String.format('{0}', "<div style='text-align:center;color: red'><b>" + value + "</b></div>");
                    }
                },
                type: 'TextField',
                filters: {pfiltro: 'conformidad', type: 'string'},
                id_grupo: 0,
                grid: true,
                form: false
            },

            {
                config: {
                    name: 'cuce',
                    fieldLabel: 'Cuce',
                    allowBlank: false,
                    anchor: '50%',
                    gwidth: 100,
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['cuce']);
                    }
                },
                type: 'TextField',
                bottom_filter: true,
                filters: {pfiltro: 'cuce', type: 'string'},
                id_grupo: 0,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'fundepto',
                    fieldLabel: 'Funcionario Responsable',
                    allowBlank: false,
                    anchor: '50%',
                    gwidth: 250,
                     //renderer:function (value, p, record){return String.format('{0}', record.data['desc_funcionario']);}*/
                },
                type: 'TextField',
                bottom_filter: true,
                filters: {pfiltro: 'fundepto', type: 'string'},
                id_grupo: 0,
                grid: true,
                form: false
            },



    /*{
     config:{
     name: 'fun_resp',
     fieldLabel: 'asignado a Funcionario',
     allowBlank: false,
     anchor: '50%',
     gwidth: 150/!*,
     renderer:function (value, p, record){return String.format('{0}', record.data['desc_funcionario']);}*!/
     },
     type:'TextField',
     /!*filters:{pfiltro:'trec.id_funcionario',type:'string'},*!/
     id_grupo:0,
     grid:true,
     form:false
     },*/


            /*,

             {
             config:{
             name: 'nro_cuota',
             fieldLabel: 'Nro. Cuota',
             allowBlank: true,
             anchor: '50%',
             gwidth: 150,
             maxLength:20,
             readOnly:true,
             renderer: function(value,p,record) {
             return String.format('<b><font color="green">{0}</font></b>', value);
             }
             },
             type:'TextField',
             filters:{pfiltro:'tpp.nro_cuota',type:'string'},
             id_grupo:0,
             grid:true,
             form:false
             }*/
        ],
        tam_pag: 50,
        title: 'ConsultaForm500v2',
        id_store: 'id_proceso_compra',
        fields: [
            {name: 'id_cotizacion', type: 'numeric'},
            {name: 'id_proceso_wf', type: 'numeric'},
            {name: 'id_estado_wf', type: 'numeric'},
            {name: 'estado', type: 'string'},
            {name: 'num_tramite', type: 'string'},
            {name: 'fun_solicitante', type: 'string'},
            {name: 'fun_resp', type: 'string'},
            {name: 'conformidad', type: 'string'},
            {name: 'tipo_doc', type: 'string'},
            {name: 'nro_cuota', type: 'numeric'},
            {name: 'tieneform500', type: 'numeric'},
            {name: 'dias_form_500', type: 'numeric'},
            {name: 'fecha_inicio', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'fecha_fin', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'fecha_conformidad', type: 'date', dateFormat: 'Y-m-d'},

            {name: 'fecha_contrato', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'fecha_conclusion', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'cuce', type: 'string'},
            {name: 'fecha_venc', type: 'string'},

            {name: 'fundepto', type: 'string'},


        ],
        arrayDefaultColumHidden: ['tieneform500'],


        sortInfo: {
            field: 'dias_form_500',
            direction: 'ASC'
        },
        bsave: false,
        btest: false,
        preparaMenu: function (n) {
            var rec = this.getSelectedData();
            var tb = this.tbar;
            Phx.vista.ConsultaForm500v2.superclass.preparaMenu.call(this, n);
            this.getBoton('diagrama_gantt').enable();
            this.getBoton('btnChequeoDocumentosWf').setDisabled(false);

        },

        liberaMenu: function () {
            var tb = Phx.vista.ConsultaForm500v2.superclass.liberaMenu.call(this);
            if (tb) {
                this.getBoton('btnChequeoDocumentosWf').setDisabled(true);
                this.getBoton('diagrama_gantt').disable();
            }
            return tb
        }

    });
</script>