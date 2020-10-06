<?php
/**
 * @package pXP
 * @file gen-MatrizConcepto.php
 * @author  (maylee.perez)
 * @date 22-09-2020 17:47:40
 * @description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.MatrizConcepto = Ext.extend(Phx.gridInterfaz, {

            constructor: function (config) {
                this.maestro = config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.MatrizConcepto.superclass.constructor.call(this, config);
                this.init();
                this.load({params: {start: 0, limit: this.tam_pag}})
            },

            Atributos: [
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_matriz_concepto'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config:{
                        name: 'id_matriz_modalidad',
                        labelSeparator:'',
                        anchor: '80%',
                        inputType:'hidden',
                        maxLength:4
                    },
                    type:'Field',
                    form:true
                },

                /*{
                    config: {
                        name: 'id_concepto_ingas',
                        fieldLabel: 'Concepto de Gasto',
                        allowBlank: true,
                        emptyText: 'Elija una opción...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_/control/Clase/Metodo',
                            id: 'id_',
                            root: 'datos',
                            sortInfo: {
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_', 'nombre', 'codigo'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'movtip.nombre#movtip.codigo'}
                        }),
                        valueField: 'id_',
                        displayField: 'nombre',
                        gdisplayField: 'desc_',
                        hiddenName: 'id_concepto_ingas',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        anchor: '100%',
                        gwidth: 150,
                        minChars: 2,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['desc_']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    filters: {pfiltro: 'movtip.nombre',type: 'string'},
                    grid: true,
                    form: true
                },*/
                {
                    config: {
                        name: 'id_concepto_ingas',
                        fieldLabel: 'Concepto de Gasto',
                        allowBlank: false,
                        emptyText: 'Concepto...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_parametros/control/ConceptoIngas/listarConceptoIngasMasPartida',
                            id: 'id_concepto_ingas',
                            root: 'datos',
                            sortInfo: {
                                field: 'desc_ingas',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_concepto_ingas', 'tipo', 'desc_ingas', 'movimiento', 'desc_partida', 'id_grupo_ots', 'filtro_ot', 'requiere_ot', 'desc_gestion'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'desc_ingas#par.codigo'}
                        }),
                        valueField: 'id_concepto_ingas',
                        displayField: 'desc_ingas',
                        gdisplayField: 'desc_ingas',
                        hiddenName: 'id_concepto_ingas',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        listWidth: 500,
                        resizable: true,
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 40,
                        queryDelay: 1000,
                        width: 250,
                        gwidth: 350,
                        minChars: 10,
                        qtip: 'Si el concepto de gasto que necesita no existe por favor  comuniquese con el área de presupuestos para solicitar la creación.',
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>{desc_ingas}</b></p><strong>{tipo}</strong><p>PARTIDA: {desc_partida} - ({desc_gestion})</p></div></tpl>',
                        renderer: function (value, p, record) {
                            //return String.format('{0} <br/><b>{1} - ({2}) </b>', record.data['desc_ingas'], record.data['desc_partida'], record.data['desc_gestion']);
                             return String.format('<b>{0}</b>', record.data['desc_ingas']);
                        }
                    },
                    type: 'ComboBox',
                    bottom_filter: true,
                    filters: {pfiltro: 'ci.desc_ingas', type: 'string'},
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
                        maxLength: 10
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'maconcep.estado_reg', type: 'string'},
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
                        maxLength: 4
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
                    filters: {pfiltro: 'maconcep.fecha_reg', type: 'date'},
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
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'maconcep.id_usuario_ai', type: 'numeric'},
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
                    filters: {pfiltro: 'maconcep.usuario_ai', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'usr_mod',
                        fieldLabel: 'Modificado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
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
                    filters: {pfiltro: 'maconcep.fecha_mod', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                }
            ],
            tam_pag: 50,
            title: 'Matriz - conceptos de gasto',
            ActSave: '../../sis_adquisiciones/control/MatrizConcepto/insertarMatrizConcepto',
            ActDel: '../../sis_adquisiciones/control/MatrizConcepto/eliminarMatrizConcepto',
            ActList: '../../sis_adquisiciones/control/MatrizConcepto/listarMatrizConcepto',
            id_store: 'id_matriz_concepto',
            fields: [
                {name: 'id_matriz_concepto', type: 'numeric'},
                {name: 'estado_reg', type: 'string'},
                {name: 'id_matriz_modalidad', type: 'numeric'},
                {name: 'id_concepto_ingas', type: 'numeric'},
                {name: 'id_usuario_reg', type: 'numeric'},
                {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_ai', type: 'numeric'},
                {name: 'usuario_ai', type: 'string'},
                {name: 'id_usuario_mod', type: 'numeric'},
                {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'usr_reg', type: 'string'},
                {name: 'usr_mod', type: 'string'},
                {name: 'desc_ingas', type: 'string'},

            ],
            sortInfo: {
                field: 'id_matriz_concepto',
                direction: 'ASC'
            },

            onReloadPage: function (m) {
                this.maestro = m;
                this.Atributos[1].valorInicial = this.maestro.id_matriz_modalidad;

                if (m.id != 'id') {
                    this.store.baseParams = {id_matriz_modalidad: this.maestro.id_matriz_modalidad};
                    this.load({params: {start: 0, limit: 50}})
                } else {//alert("else");
                    this.grid.getTopToolbar().disable();
                    this.grid.getBottomToolbar().disable();
                    this.store.removeAll();
                }

            },

            bdel: true,
            bsave: true
        }
    )
</script>
		
		