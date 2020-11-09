<?php
/**
 * @package pXP
 * @file gen-MatrizModalidad.php
 * @author  (maylee.perez)
 * @date 22-09-2020 13:33:53
 * @description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.MatrizModalidad = Ext.extend(Phx.gridInterfaz, {

            fwidth: '80%',
            fheight: '70%',
            constructor: function (config) {
                this.maestro = config.maestro;

                this.buildGrupos();

                //llama al constructor de la clase padre
                Phx.vista.MatrizModalidad.superclass.constructor.call(this, config);
                this.init();
                this.iniciarEventos();
                //this.onEdit();
                this.load({params: {start: 0, limit: this.tam_pag}})
            },

            buildGrupos: function () {
                var me = this;

                me.Grupos = [{
                    xtype: 'fieldset',
                    border: false,
                    split: true,
                    layout: 'column',
                    region: 'north',
                    autoScroll: true,
                    collapseFirst: false,
                    collapsible: true,
                    collapseMode: 'mini',
                    width: '100%',
                    height: me.heightHeader,
                    padding: '0 0 0 10',
                    items: [
                        {
                            items: [
                                {
                                    bodyStyle: 'padding-right:5px;',
                                    width: '100%',
                                    autoHeight: true,
                                    border: true,
                                    items: [
                                        {
                                            xtype: 'fieldset',
                                            frame: true,
                                            border: false,
                                            layout: 'form',
                                            title: ' Datos Generales ',
                                            width: '50%',

                                            //margins: '0 0 0 5',
                                            padding: '0 0 0 10',
                                            bodyStyle: 'padding-left:5px;',
                                            id_grupo: 0,
                                            items: [],


                                        }]
                                }
                            ]
                        },
                        {
                            items: [
                                {
                                    bodyStyle: 'padding-right:5px;',
                                    width: '100%',
                                    border: true,
                                    autoHeight: true,
                                    items: [{
                                        xtype: 'fieldset',
                                        frame: true,
                                        layout: 'form',
                                        title: ' Modalidades ',
                                        width: '100%',
                                        border: false,
                                        //margins: '0 0 0 5',
                                        padding: '0 0 0 10',
                                        bodyStyle: 'padding-left:5px;',
                                        id_grupo: 1,
                                        items: [],
                                    }]
                                }

                            ]

                        },
                        {
                            items: [
                                {
                                    bodyStyle: 'padding-right:5px;',
                                    width: '100%',
                                    border: true,
                                    autoHeight: true,
                                    items: [{
                                        xtype: 'fieldset',
                                        frame: true,
                                        layout: 'form',
                                        title: ' Proceso de Contratación ',
                                        width: '100%',
                                        border: false,
                                        //margins: '0 0 0 5',
                                        padding: '0 0 0 10',
                                        bodyStyle: 'padding-left:5px;',
                                        id_grupo: 2,
                                        items: [],
                                    }]
                                }

                            ]
                        }

                    ]

                }]
            },

            Atributos: [
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_matriz_modalidad'
                    },
                    type: 'Field',
                    form: true
                },

                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'modalidad_anpe'
                    },
                    type: 'Field',
                    form: true
                },

                {
                    config: {
                        name: 'referencia',
                        fieldLabel: 'Ref.',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 35,
                        maxLength: 300
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'matriz.referencia', type: 'string'},
                    bottom_filter: true,
                    id_grupo: 0,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'tipo_contratacion',
                        fieldLabel: 'Tipo Contratación',
                        allowBlank: false,
                        anchor: '100%',
                        gwidth: 300,
                        maxLength: 500
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'matriz.tipo_contratacion', type: 'string'},
                    bottom_filter: true,
                    id_grupo: 0,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'nacional',
                        fieldLabel: 'Nacional',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 50,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['si', 'no']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.nacional', type: 'string'},
                    valorInicial: 'no',
                    id_grupo: 0,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'internacional',
                        fieldLabel: 'Internacional',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 50,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['si', 'no']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.internacional', type: 'string'},
                    valorInicial: 'no',
                    id_grupo: 0,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'id_uo',
                        origen: 'UO',
                        fieldLabel: 'Unidad Responsable',
                        allowBlank: false,
                        gdisplayField: 'nombre_uo',//mapea al store del grid
                        gwidth: 250,
                        anchor: '100%',
                        //baseParams:{presupuesta:'si'},
                        renderer: function (value, p, record) {
                            return String.format('{0} {1}', record.data['codigo_uo'], record.data['nombre_uo']);
                        }
                    },
                    type: 'ComboRec',
                    id_grupo: 0,
                    filters: {pfiltro: 'nombre_uo', type: 'string'},
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'nombre_gerencia',
                        fieldLabel: 'Gerencia',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 400,
                        gdisplayField: 'nombre_gerencia',//mapea al store del grid
                        maxLength: 100

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'nombre_gerencia', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'nivel_importancia',
                        fieldLabel: 'Nivel de importancia',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['1', '2', '3']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.nivel_importancia', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    form: true
                },

                {
                    config: {
                        name: 'id_cargo',
                        fieldLabel: 'Nivel Aprobación (Aprobador)',
                        allowBlank: false,
                        resizable: true,
                        emptyText: 'Elija una opción...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_organigrama/control/Cargo/listarCargo',
                            id: 'id_cargo',
                            root: 'datos',
                            sortInfo: {
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_cargo', 'nombre', 'codigo'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'cargo.nombre'}
                        }),
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p></div></tpl>',
                        valueField: 'id_cargo',
                        displayField: 'nombre',
                        gdisplayField: 'nombre',
                        hiddenName: 'id_cargo',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        anchor: '100%',
                        listWidth: 350,
                        gwidth: 250,
                        minChars: 2,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['nombre']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    filters: {pfiltro: 'car.nombre', type: 'string'},
                    bottom_filter: true,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'contrato_global',
                        fieldLabel: 'Contrato Global',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['si', 'no']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.contrato_global', type: 'string'},
                    valorInicial: 'no',
                    id_grupo: 0,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'modalidad_menor',
                        fieldLabel: 'Modalidad Menor',
                        allowBlank: true,
                        anchor: '93%',
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['si', 'no']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.modalidad_menor', type: 'string'},
                    valorInicial: 'no',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'modalidad_anpe',
                        fieldLabel: 'Modalidad ANPE',
                        allowBlank: true,
                        anchor: '93%',
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['si', 'no']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.modalidad_anpe', type: 'string'},
                    valorInicial: 'no',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'modalidad_licitacion',
                        fieldLabel: 'Modalidad Licitación',
                        allowBlank: true,
                        anchor: '93%',
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['si', 'no']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.modalidad_licitacion', type: 'string'},
                    valorInicial: 'no',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'modalidad_directa',
                        fieldLabel: 'Modalidad Directa',
                        allowBlank: true,
                        anchor: '93%',
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['si', 'no']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.modalidad_directa', type: 'string'},
                    valorInicial: 'no',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config : {
                        name:'flujo_mod_directa',
                        fieldLabel : 'Flujo',
                        resizable:true,
                        allowBlank:false,
                        anchor: '93%',
                        gwidth: 100,
                        emptyText:'Seleccione ...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
                            id: 'id_catalogo',
                            root: 'datos',
                            sortInfo:{
                                field: 'orden',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_catalogo','codigo','descripcion'],
                            // turn on remote sorting
                            remoteSort: true,
                            baseParams: {par_filtro:'descripcion',cod_subsistema:'ADQ',catalogo_tipo:'tmatriz_concepto'}
                        }),
                        enableMultiSelect:true,
                        valueField: 'codigo',
                        displayField: 'descripcion',
                        gdisplayField: 'flujo_mod_directa',
                        forceSelection:true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender:true,
                        mode:'remote',
                        pageSize:10,
                        listWidth: 350,
                        queryDelay:1000
                    },
                    type : 'ComboBox',
                    id_grupo: 1,
                    form : true,
                    grid: false,
                },
                {
                    config: {
                        name: 'modalidad_excepcion',
                        fieldLabel: 'Modalidad Excepción',
                        allowBlank: true,
                        anchor: '93%',
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['si', 'no']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.modalidad_excepcion', type: 'string'},
                    valorInicial: 'no',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'modalidad_desastres',
                        fieldLabel: 'Modalidad Desastres',
                        allowBlank: true,
                        anchor: '93%',
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['si', 'no']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.modalidad_desastres', type: 'string'},
                    valorInicial: 'no',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'punto_reorden',
                        fieldLabel: 'Punto Reorden',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['si', 'no']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.punto_reorden', type: 'string'},
                    valorInicial: 'no',
                    id_grupo: 0,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'resp_proc_contratacion_menor',
                        fieldLabel: 'Resp. Mod. Menor',
                        allowBlank: false,
                        width: 60,
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['RPA', 'RPC']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.resp_proc_contratacion_menor', type: 'string'},
                    valorInicial: 'RPA',
                    id_grupo: 2,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'resp_proc_contratacion_anpe',
                        fieldLabel: 'Resp. Mod. ANPE',
                        allowBlank: false,
                        width: 60,
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['RPA', 'RPC']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.resp_proc_contratacion_anpe', type: 'string'},
                    valorInicial: 'RPA',
                    id_grupo: 2,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'resp_proc_contratacion_licitacion',
                        fieldLabel: 'Resp. Mod. Licitación',
                        allowBlank: false,
                        width: 60,
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['RPA', 'RPC']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.resp_proc_contratacion_licitacion', type: 'string'},
                    //valorInicial: 'RPC',
                    id_grupo: 2,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'resp_proc_contratacion_directa',
                        fieldLabel: 'Resp. Mod. Directa',
                        allowBlank: false,
                        width: 60,
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['RPA', 'RPC']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.resp_proc_contratacion_directa', type: 'string'},
                    //valorInicial: 'RPC',
                    id_grupo: 2,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'resp_proc_contratacion_excepcion',
                        fieldLabel: 'Resp. Mod. Excepción',
                        allowBlank: false,
                        width: 60,
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['MAE']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.resp_proc_contratacion_excepcion', type: 'string'},
                    valorInicial: 'MAE',
                    id_grupo: 2,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'resp_proc_contratacion_desastres',
                        fieldLabel: 'Resp. Mod. Desastres',
                        allowBlank: false,
                        width: 60,
                        gwidth: 100,
                        maxLength: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        store: ['RPA', 'RPC']
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'matriz.resp_proc_contratacion_desastres', type: 'string'},
                    //valorInicial: 'RPC',
                    id_grupo: 2,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'list_concepto_gasto',
                        fieldLabel: 'List Conceptos de Gasto',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 400,
                        maxLength: 100

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'matriz.list_concepto_gasto', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },

                {
                    config: {
                        name: 'observaciones',
                        fieldLabel: 'Observaciones',
                        allowBlank: true,
                        anchor: '93%',
                        gwidth: 100,
                        maxLength: 600
                    },
                    type: 'TextArea',
                    filters: {pfiltro: 'matriz.observaciones', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 100
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'matriz.estado_reg', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'usr_reg',
                        fieldLabel: 'Creado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 100
                    },
                    type: 'Field',
                    filters: {pfiltro: 'usu1.cuenta', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'fecha_reg',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y H:i:s') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'matriz.fecha_reg', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },

                {
                    config: {
                        name: 'id_usuario_ai',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 100
                    },
                    type: 'Field',
                    filters: {pfiltro: 'matriz.id_usuario_ai', type: 'numeric'},
                    id_grupo: 1,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'usuario_ai',
                        fieldLabel: 'Funcionaro AI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 300
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'matriz.usuario_ai', type: 'string'},
                    id_grupo: 1,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'usr_mod',
                        fieldLabel: 'Modificado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 100
                    },
                    type: 'Field',
                    filters: {pfiltro: 'usu2.cuenta', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'fecha_mod',
                        fieldLabel: 'Fecha Modif.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y H:i:s') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'matriz.fecha_mod', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                }
            ],
            tam_pag: 50,
            title: 'Matriz',
            ActSave: '../../sis_adquisiciones/control/MatrizModalidad/insertarMatrizModalidad',
            ActDel: '../../sis_adquisiciones/control/MatrizModalidad/eliminarMatrizModalidad',
            ActList: '../../sis_adquisiciones/control/MatrizModalidad/listarMatrizModalidad',
            id_store: 'id_matriz_modalidad',
            fields: [
                {name: 'id_matriz_modalidad', type: 'numeric'},
                {name: 'estado_reg', type: 'string'},
                {name: 'referencia', type: 'string'},
                {name: 'tipo_contratacion', type: 'string'},
                {name: 'nacional', type: 'string'},
                {name: 'internacional', type: 'string'},
                {name: 'id_uo', type: 'numeric'},
                {name: 'nivel_importancia', type: 'string'},
                {name: 'id_cargo', type: 'numeric'},
                {name: 'contrato_global', type: 'string'},
                {name: 'modalidad_menor', type: 'string'},
                {name: 'modalidad_anpe', type: 'string'},
                {name: 'modalidad_licitacion', type: 'string'},
                {name: 'modalidad_directa', type: 'string'},
                {name: 'modalidad_excepcion', type: 'string'},
                {name: 'modalidad_desastres', type: 'string'},
                {name: 'punto_reorden', type: 'string'},
                {name: 'observaciones', type: 'string'},
                {name: 'id_usuario_reg', type: 'numeric'},
                {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_ai', type: 'numeric'},
                {name: 'usuario_ai', type: 'string'},
                {name: 'id_usuario_mod', type: 'numeric'},
                {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'usr_reg', type: 'string'},
                {name: 'usr_mod', type: 'string'},
                {name: 'nombre_uo', type: 'string'},
                {name: 'codigo_uo', type: 'string'},
                {name: 'nombre', type: 'string'},
                {name: 'resp_proc_contratacion', type: 'string'},
                {name: 'list_concepto_gasto', type: 'text'},

                {name: 'resp_proc_contratacion_menor', type: 'string'},
                {name: 'resp_proc_contratacion_anpe', type: 'string'},
                {name: 'resp_proc_contratacion_directa', type: 'string'},
                {name: 'resp_proc_contratacion_licitacion', type: 'string'},
                {name: 'resp_proc_contratacion_excepcion', type: 'string'},
                {name: 'resp_proc_contratacion_desastres', type: 'string'},
                {name: 'flujo_mod_directa', type: 'string'},

                {name: 'nombre_gerencia', type: 'string'},

            ],
            sortInfo: {
                field: 'tipo_contratacion',
                direction: 'ASC'
            },

            arrayDefaultColumHidden: ['list_concepto_gasto', 'resp_proc_contratacion_menor', 'resp_proc_contratacion_anpe', 'resp_proc_contratacion_directa',
                'resp_proc_contratacion_licitacion', 'resp_proc_contratacion_excepcion', 'resp_proc_contratacion_desastres', 'nombre_gerencia'],


            tabsouth: [{
                url: '../../../sis_adquisiciones/vista/matriz_concepto/MatrizConcepto.php',
                title: 'Conceptos de Gasto',
                //width:'50%',
                height: '50%',
                cls: 'MatrizConcepto'
            }],

            iniciarEventos: function () {

                this.ocultarComponente(this.Cmp.flujo_mod_directa);
                this.ocultarComponente(this.Cmp.resp_proc_contratacion_directa);
                this.ocultarComponente(this.Cmp.resp_proc_contratacion_menor);
                this.ocultarComponente(this.Cmp.resp_proc_contratacion_anpe);
                this.ocultarComponente(this.Cmp.resp_proc_contratacion_licitacion);
                this.ocultarComponente(this.Cmp.resp_proc_contratacion_excepcion);
                this.ocultarComponente(this.Cmp.resp_proc_contratacion_desastres);

                this.Cmp.modalidad_directa.on('select', function (cmp, rec) {
                    if (this.Cmp.modalidad_directa.getValue() == 'si') {
                        this.mostrarComponente(this.Cmp.flujo_mod_directa);
                        this.mostrarComponente(this.Cmp.resp_proc_contratacion_directa);

                        this.Cmp.modalidad_menor.reset();
                        this.Cmp.resp_proc_contratacion_menor.reset();
                        this.Cmp.modalidad_anpe.reset();
                        this.Cmp.resp_proc_contratacion_anpe.reset();
                        this.Cmp.modalidad_licitacion.reset();
                        this.Cmp.resp_proc_contratacion_licitacion.reset();
                        this.Cmp.modalidad_excepcion.reset();
                        this.Cmp.resp_proc_contratacion_excepcion.reset();
                        this.Cmp.modalidad_desastres.reset();
                        this.Cmp.resp_proc_contratacion_desastres.reset();
                        this.ocultarComponente(this.Cmp.modalidad_menor);
                        this.ocultarComponente(this.Cmp.resp_proc_contratacion_menor);
                        this.ocultarComponente(this.Cmp.modalidad_anpe);
                        this.ocultarComponente(this.Cmp.resp_proc_contratacion_anpe);
                        this.ocultarComponente(this.Cmp.modalidad_licitacion);
                        this.ocultarComponente(this.Cmp.resp_proc_contratacion_licitacion);
                        this.ocultarComponente(this.Cmp.modalidad_excepcion);
                        this.ocultarComponente(this.Cmp.resp_proc_contratacion_excepcion);
                        this.ocultarComponente(this.Cmp.modalidad_desastres);
                        this.ocultarComponente(this.Cmp.resp_proc_contratacion_desastres);

                    }else {
                        this.Cmp.flujo_mod_directa.reset();
                        this.Cmp.resp_proc_contratacion_directa.reset();

                        this.Cmp.modalidad_menor.setValue('no');
                        this.ocultarComponente(this.Cmp.flujo_mod_directa);
                        this.ocultarComponente(this.Cmp.resp_proc_contratacion_directa);
                        this.mostrarComponente(this.Cmp.modalidad_menor);
                        this.mostrarComponente(this.Cmp.modalidad_anpe);
                        this.mostrarComponente(this.Cmp.modalidad_licitacion);
                        this.mostrarComponente(this.Cmp.modalidad_excepcion);
                        this.mostrarComponente(this.Cmp.modalidad_desastres);
                    }

                }, this);

                this.Cmp.modalidad_menor.on('select', function (cmp, rec) {
                    if (this.Cmp.modalidad_menor.getValue() == 'si') {
                        this.mostrarComponente(this.Cmp.resp_proc_contratacion_menor);
                        this.ocultarComponente(this.Cmp.modalidad_directa);
                    }else {
                        this.Cmp.modalidad_directa.reset();
                        this.Cmp.flujo_mod_directa.reset();
                        this.ocultarComponente(this.Cmp.resp_proc_contratacion_menor);
                        this.mostrarComponente(this.Cmp.modalidad_directa);
                    }

                }, this);

                this.Cmp.modalidad_anpe.on('select', function (cmp, rec) {
                    if (this.Cmp.modalidad_anpe.getValue() == 'si') {
                        this.mostrarComponente(this.Cmp.resp_proc_contratacion_anpe);
                        this.ocultarComponente(this.Cmp.modalidad_directa);
                    }else {
                        this.ocultarComponente(this.Cmp.resp_proc_contratacion_anpe);
                        this.mostrarComponente(this.Cmp.modalidad_directa);
                    }

                }, this);

                this.Cmp.modalidad_licitacion.on('select', function (cmp, rec) {
                    if (this.Cmp.modalidad_licitacion.getValue() == 'si') {
                        this.mostrarComponente(this.Cmp.resp_proc_contratacion_licitacion);
                        this.ocultarComponente(this.Cmp.modalidad_directa);
                    }else {
                        this.ocultarComponente(this.Cmp.resp_proc_contratacion_licitacion);
                        this.mostrarComponente(this.Cmp.modalidad_directa);
                    }

                }, this);

                this.Cmp.modalidad_excepcion.on('select', function (cmp, rec) {
                    if (this.Cmp.modalidad_excepcion.getValue() == 'si') {
                        this.mostrarComponente(this.Cmp.resp_proc_contratacion_excepcion);
                        this.ocultarComponente(this.Cmp.modalidad_directa);
                    }else {
                        this.ocultarComponente(this.Cmp.resp_proc_contratacion_excepcion);
                        this.mostrarComponente(this.Cmp.modalidad_directa);
                    }

                }, this);

                this.Cmp.modalidad_desastres.on('select', function (cmp, rec) {
                    if (this.Cmp.modalidad_desastres.getValue() == 'si') {
                        this.mostrarComponente(this.Cmp.resp_proc_contratacion_desastres);
                        this.ocultarComponente(this.Cmp.modalidad_directa);
                    }else {
                        this.ocultarComponente(this.Cmp.resp_proc_contratacion_desastres);
                        this.mostrarComponente(this.Cmp.modalidad_directa);
                    }

                }, this);


            },

        onButtonEdit: function () {
            datos = this.sm.getSelected().data;
            Phx.vista.MatrizModalidad.superclass.onButtonEdit.call(this); //sobrecarga enable select

            if (this.Cmp.modalidad_directa.getValue() == 'si') {
                this.mostrarComponente(this.Cmp.flujo_mod_directa);
                this.mostrarComponente(this.Cmp.resp_proc_contratacion_directa);

            }else {
                this.Cmp.modalidad_directa.setValue('no')
                this.ocultarComponente(this.Cmp.flujo_mod_directa);
                this.ocultarComponente(this.Cmp.resp_proc_contratacion_directa);
                this.mostrarComponente(this.Cmp.modalidad_menor);
            }

            if (this.Cmp.modalidad_menor.getValue() == 'si') {
                this.mostrarComponente(this.Cmp.resp_proc_contratacion_menor);
                this.ocultarComponente(this.Cmp.flujo_mod_directa);
                //this.ocultarComponente(this.Cmp.modalidad_directa);
            }else {
                this.Cmp.modalidad_menor.setValue('no')
                this.ocultarComponente(this.Cmp.resp_proc_contratacion_menor);
            }

            if (this.Cmp.modalidad_anpe.getValue() == 'si') {
                this.mostrarComponente(this.Cmp.resp_proc_contratacion_anpe);
            }else {
                this.Cmp.modalidad_anpe.setValue('no')
                this.ocultarComponente(this.Cmp.resp_proc_contratacion_anpe);
            }

            if (this.Cmp.modalidad_licitacion.getValue() == 'si') {
                this.mostrarComponente(this.Cmp.resp_proc_contratacion_licitacion);
            }else {
                this.Cmp.modalidad_licitacion.setValue('no')
                this.ocultarComponente(this.Cmp.resp_proc_contratacion_licitacion);
            }

            if (this.Cmp.modalidad_excepcion.getValue() == 'si') {
                this.mostrarComponente(this.Cmp.resp_proc_contratacion_excepcion);
            }else {
                this.Cmp.modalidad_excepcion.setValue('no')
                this.ocultarComponente(this.Cmp.resp_proc_contratacion_excepcion);
            }

            if (this.Cmp.modalidad_desastres.getValue() == 'si') {
                this.mostrarComponente(this.Cmp.resp_proc_contratacion_desastres);
            }else {
                this.Cmp.modalidad_desastres.setValue('no')
                this.ocultarComponente(this.Cmp.resp_proc_contratacion_desastres);
            }

            this.Cmp.modalidad_menor.on('select', function (cmp, rec) {
                if (this.Cmp.modalidad_menor.getValue() == 'si') {
                    this.mostrarComponente(this.Cmp.resp_proc_contratacion_menor);
                    this.Cmp.resp_proc_contratacion_menor.setValue('RPA');
                    this.Cmp.modalidad_directa.reset();
                    this.Cmp.flujo_mod_directa.reset();
                    this.Cmp.resp_proc_contratacion_directa.reset();
                    this.ocultarComponente(this.Cmp.modalidad_directa);
                    this.ocultarComponente(this.Cmp.flujo_mod_directa);
                    this.ocultarComponente(this.Cmp.resp_proc_contratacion_directa);

                }else {
                    this.Cmp.resp_proc_contratacion_menor.reset();
                    this.Cmp.modalidad_directa.setValue('no')
                    this.ocultarComponente(this.Cmp.resp_proc_contratacion_menor);
                    this.mostrarComponente(this.Cmp.modalidad_directa);
                }

            }, this);

            this.Cmp.modalidad_directa.on('select', function (cmp, rec) {
                if (this.Cmp.modalidad_directa.getValue() == 'si') {
                    this.mostrarComponente(this.Cmp.flujo_mod_directa);
                    this.mostrarComponente(this.Cmp.resp_proc_contratacion_directa);
                    this.Cmp.modalidad_menor.reset();
                    this.Cmp.resp_proc_contratacion_menor.reset();
                    this.Cmp.modalidad_anpe.reset();
                    this.Cmp.resp_proc_contratacion_anpe.reset();
                    this.Cmp.modalidad_licitacion.reset();
                    this.Cmp.resp_proc_contratacion_licitacion.reset();
                    this.Cmp.modalidad_excepcion.reset();
                    this.Cmp.resp_proc_contratacion_excepcion.reset();
                    this.Cmp.modalidad_desastres.reset();
                    this.Cmp.resp_proc_contratacion_desastres.reset();
                    this.ocultarComponente(this.Cmp.modalidad_menor);
                    this.ocultarComponente(this.Cmp.resp_proc_contratacion_menor);
                    this.ocultarComponente(this.Cmp.modalidad_anpe);
                    this.ocultarComponente(this.Cmp.resp_proc_contratacion_anpe);
                    this.ocultarComponente(this.Cmp.modalidad_licitacion);
                    this.ocultarComponente(this.Cmp.resp_proc_contratacion_licitacion);
                    this.ocultarComponente(this.Cmp.modalidad_excepcion);
                    this.ocultarComponente(this.Cmp.resp_proc_contratacion_excepcion);
                    this.ocultarComponente(this.Cmp.modalidad_desastres);
                    this.ocultarComponente(this.Cmp.resp_proc_contratacion_desastres);
                }else {
                    //this.detCmp.id_orden_trabajo.reset();
                    this.Cmp.flujo_mod_directa.reset();
                    this.Cmp.resp_proc_contratacion_directa.reset();
                    this.Cmp.modalidad_menor.setValue('no');
                    this.ocultarComponente(this.Cmp.flujo_mod_directa);
                    this.ocultarComponente(this.Cmp.resp_proc_contratacion_directa);
                    this.mostrarComponente(this.Cmp.modalidad_menor);
                    this.mostrarComponente(this.Cmp.modalidad_anpe);
                    this.mostrarComponente(this.Cmp.modalidad_licitacion);
                    this.mostrarComponente(this.Cmp.modalidad_excepcion);
                    this.mostrarComponente(this.Cmp.modalidad_desastres);

                }

            }, this);

            this.Cmp.modalidad_anpe.on('select', function (cmp, rec) {
                if (this.Cmp.modalidad_anpe.getValue() == 'si') {
                    this.mostrarComponente(this.Cmp.resp_proc_contratacion_anpe);
                    this.ocultarComponente(this.Cmp.modalidad_directa);
                    this.ocultarComponente(this.Cmp.flujo_mod_directa);
                    this.ocultarComponente(this.Cmp.resp_proc_contratacion_directa);
                    this.Cmp.modalidad_directa.reset();
                    this.Cmp.flujo_mod_directa.reset();
                    this.Cmp.resp_proc_contratacion_directa.reset();
                    this.Cmp.resp_proc_contratacion_anpe.setValue('RPA');
                }else {
                    this.Cmp.resp_proc_contratacion_anpe.reset();
                    this.ocultarComponente(this.Cmp.resp_proc_contratacion_anpe);
                    this.mostrarComponente(this.Cmp.modalidad_menor);
                }

            }, this);

            this.Cmp.modalidad_licitacion.on('select', function (cmp, rec) {
                if (this.Cmp.modalidad_licitacion.getValue() == 'si') {
                    this.mostrarComponente(this.Cmp.resp_proc_contratacion_licitacion);
                    this.ocultarComponente(this.Cmp.modalidad_directa);
                }else {
                    this.Cmp.resp_proc_contratacion_licitacion.reset();
                    this.ocultarComponente(this.Cmp.resp_proc_contratacion_licitacion);
                    this.mostrarComponente(this.Cmp.modalidad_menor);
                }

            }, this);

            this.Cmp.modalidad_excepcion.on('select', function (cmp, rec) {
                if (this.Cmp.modalidad_excepcion.getValue() == 'si') {
                    this.mostrarComponente(this.Cmp.resp_proc_contratacion_excepcion);
                    this.ocultarComponente(this.Cmp.modalidad_directa);
                    this.Cmp.resp_proc_contratacion_excepcion.setValue('MAE')
                }else {
                    this.Cmp.resp_proc_contratacion_excepcion.reset();
                    this.ocultarComponente(this.Cmp.resp_proc_contratacion_excepcion);
                    this.mostrarComponente(this.Cmp.modalidad_menor);
                }

            }, this);

            this.Cmp.modalidad_desastres.on('select', function (cmp, rec) {
                if (this.Cmp.modalidad_desastres.getValue() == 'si') {
                    this.mostrarComponente(this.Cmp.resp_proc_contratacion_desastres);
                    this.ocultarComponente(this.Cmp.modalidad_directa);
                }else {
                    this.Cmp.resp_proc_contratacion_desastres.reset();
                    this.ocultarComponente(this.Cmp.resp_proc_contratacion_desastres);
                    this.mostrarComponente(this.Cmp.modalidad_menor);
                }

            }, this);


        },

            bdel: true,
            bsave: true
        }
    )
</script>
		
		