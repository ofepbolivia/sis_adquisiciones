<?php
/**
*@package pXP
*@file gen-MatrizModalidad.php
*@author  (maylee.perez)
*@date 22-09-2020 13:33:53
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.MatrizModalidad=Ext.extend(Phx.gridInterfaz,{

    fwidth: '60%',
    fheight: '70%',
	constructor:function(config){
		this.maestro=config.maestro;

        this.buildGrupos();

    	//llama al constructor de la clase padre
		Phx.vista.MatrizModalidad.superclass.constructor.call(this,config);
		this.init();
		this.load({params:{start:0, limit:this.tam_pag}})
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
                                    width: '115%',
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
                    ]

        }]
    },
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_matriz_modalidad'
			},
			type:'Field',
			form:true 
		},

		{
			config:{
				name: 'referencia',
				fieldLabel: 'Ref.',
				allowBlank: true,
                anchor: '100%',
				gwidth: 100,
				maxLength:300
			},
				type:'TextField',
				filters:{pfiltro:'matriz.referencia',type:'string'},
				id_grupo:0,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'tipo_contratacion',
				fieldLabel: 'Tipo Contratación',
				allowBlank: false,
                anchor: '100%',
				gwidth: 100,
				maxLength:500
			},
				type:'TextField',
				filters:{pfiltro:'matriz.tipo_contratacion',type:'string'},
				id_grupo:0,
				grid:true,
				form:true
		},
        {
			config:{
				name: 'nacional',
				fieldLabel: 'Nacional',
				allowBlank: false,
                anchor: '100%',
				gwidth: 100,
				maxLength:100,
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                store: ['si', 'no']
			},
				type:'ComboBox',
				filters:{pfiltro:'matriz.nacional',type:'string'},
                valorInicial: 'no',
				id_grupo:0,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'internacional',
				fieldLabel: 'Internacional',
				allowBlank: true,
                anchor: '100%',
				gwidth: 100,
				maxLength:100,
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                store: ['si', 'no']
			},
				type:'ComboBox',
				filters:{pfiltro:'matriz.internacional',type:'string'},
                valorInicial: 'no',
				id_grupo:0,
				grid:true,
				form:true
		},
		/*{
			config: {
				name: 'id_uo',
				fieldLabel: 'Responsable',
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
				hiddenName: 'id_uo',
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
            config:{
                name:'id_uo',
                origen:'UO',
                fieldLabel:'Responsable',
                allowBlank:false,
                gdisplayField:'nombre_uo',//mapea al store del grid
                gwidth:200,
                anchor: '100%',
                baseParams:{presupuesta:'si'},
                renderer:function (value, p, record){return String.format('{0} {1}' , record.data['codigo_uo'], record.data['nombre_uo']);}
            },
            type:'ComboRec',
            id_grupo:0,
            filters:{pfiltro:'nombre_uo',type:'string'},
            grid:true,
            form:true
        },

		{
			config:{
				name: 'nivel_importancia',
				fieldLabel: 'Nivel de importancia',
				allowBlank: true,
                anchor: '100%',
				gwidth: 100,
				maxLength:100,
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                store: ['1', '2','3']
			},
				type:'ComboBox',
				filters:{pfiltro:'matriz.nivel_importancia',type:'string'},
				id_grupo:0,
				grid:true,
				form:true
		},
		/*{
			config: {
				name: 'id_cargo',
				fieldLabel: 'Nivel de Aprobación',
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
				hiddenName: 'id_cargo',
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
        /*{
            config: {
                name: 'id_cargo',
                fieldLabel: 'Nivel de Aprobación',
                allowBlank: false,
                emptyText: 'Elija una opción...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_organigrama/control/TemporalCargo/listarTemporalCargo',
                    id: 'id_temporal_cargo',
                    root: 'datos',
                    sortInfo: {
                        field: 'nombre',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_temporal_cargo', 'nombre'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'cargo.nombre'}
                }),
                valueField: 'id_temporal_cargo',
                displayField: 'nombre',
                gdisplayField: 'nombre',
                hiddenName: 'id_temporal_cargo',
                forceSelection: false,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 15,
                queryDelay: 1000,
                anchor: '100%',
                gwidth: 200,
                minChars: 2,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['nombre']);
                }
            },
            type: 'ComboBox',
            id_grupo: 0,
            filters: {pfiltro: 'tcargo.nombre',type: 'string'},
            grid: true,
            form: true
        },*/
        {
            config: {
                name: 'id_cargo',
                fieldLabel: 'Titular',
                allowBlank: true,
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
                    fields: ['id_cargo', 'nombre','codigo'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'nombre'}
                }),
                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p></div></tpl>',
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
                gwidth: 150,
                minChars: 2,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['nombre']);
                }
            },
            type: 'ComboBox',
            id_grupo: 0,
            filters: {pfiltro: 'tcargo.nombre',type: 'string'},
            grid: true,
            form: true
        },
		{
			config:{
				name: 'contrato_global',
				fieldLabel: 'Contrato Global',
				allowBlank: true,
                anchor: '100%',
				gwidth: 100,
				maxLength:100,
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                store: ['si', 'no']
			},
				type:'ComboBox',
				filters:{pfiltro:'matriz.contrato_global',type:'string'},
                valorInicial: 'no',
				id_grupo:0,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'modalidad_menor',
				fieldLabel: 'Modalidad Menor',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:100,
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                store: ['si', 'no']
			},
				type:'ComboBox',
				filters:{pfiltro:'matriz.modalidad_menor',type:'string'},
                valorInicial: 'no',
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'modalidad_anpe',
				fieldLabel: 'Modalidad ANPE',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:100,
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                store: ['si', 'no']
			},
				type:'ComboBox',
				filters:{pfiltro:'matriz.modalidad_anpe',type:'string'},
                valorInicial: 'no',
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'modalidad_licitacion',
				fieldLabel: 'Modalidad Licitación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:100,
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                store: ['si', 'no']
			},
				type:'ComboBox',
				filters:{pfiltro:'matriz.modalidad_licitacion',type:'string'},
                valorInicial: 'no',
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'modalidad_directa',
				fieldLabel: 'Modalidad Directa',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:100,
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                store: ['si', 'no']
			},
				type:'ComboBox',
				filters:{pfiltro:'matriz.modalidad_directa',type:'string'},
                valorInicial: 'no',
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'modalidad_excepcion',
				fieldLabel: 'Modalidad Excepcion',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:100,
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                store: ['si', 'no']
			},
				type:'ComboBox',
				filters:{pfiltro:'matriz.modalidad_excepcion',type:'string'},
                valorInicial: 'no',
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'modalidad_desastres',
				fieldLabel: 'Modalidad Desastres',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:100,
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                store: ['si', 'no']
			},
				type:'ComboBox',
				filters:{pfiltro:'matriz.modalidad_desastres',type:'string'},
                valorInicial: 'no',
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'punto_reorden',
				fieldLabel: 'Punto Reorden',
				allowBlank: true,
                anchor: '100%',
				gwidth: 100,
				maxLength:100,
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                store: ['si', 'no']
			},
				type:'ComboBox',
				filters:{pfiltro:'matriz.punto_reorden',type:'string'},
                valorInicial: 'no',
				id_grupo:0,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'observaciones',
				fieldLabel: 'Observaciones',
				allowBlank: true,
				anchor: '81%',
				gwidth: 100,
				maxLength:600
			},
				type:'TextArea',
				filters:{pfiltro:'matriz.observaciones',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
        {
            config:{
                name: 'estado_reg',
                fieldLabel: 'Estado Reg.',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:100
            },
            type:'TextField',
            filters:{pfiltro:'matriz.estado_reg',type:'string'},
            id_grupo:1,
            grid:true,
            form:false
        },
		{
			config:{
				name: 'usr_reg',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:100
			},
				type:'Field',
				filters:{pfiltro:'usu1.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'matriz.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:100
			},
				type:'Field',
				filters:{pfiltro:'matriz.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:300
			},
				type:'TextField',
				filters:{pfiltro:'matriz.usuario_ai',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usr_mod',
				fieldLabel: 'Modificado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:100
			},
				type:'Field',
				filters:{pfiltro:'usu2.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'matriz.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'Matriz',
	ActSave:'../../sis_adquisiciones/control/MatrizModalidad/insertarMatrizModalidad',
	ActDel:'../../sis_adquisiciones/control/MatrizModalidad/eliminarMatrizModalidad',
	ActList:'../../sis_adquisiciones/control/MatrizModalidad/listarMatrizModalidad',
	id_store:'id_matriz_modalidad',
	fields: [
		{name:'id_matriz_modalidad', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'referencia', type: 'string'},
		{name:'tipo_contratacion', type: 'string'},
		{name:'nacional', type: 'string'},
		{name:'internacional', type: 'string'},
		{name:'id_uo', type: 'numeric'},
		{name:'nivel_importancia', type: 'string'},
		{name:'id_cargo', type: 'numeric'},
		{name:'contrato_global', type: 'string'},
		{name:'modalidad_menor', type: 'string'},
		{name:'modalidad_anpe', type: 'string'},
		{name:'modalidad_licitacion', type: 'string'},
		{name:'modalidad_directa', type: 'string'},
		{name:'modalidad_excepcion', type: 'string'},
		{name:'modalidad_desastres', type: 'string'},
		{name:'punto_reorden', type: 'string'},
		{name:'observaciones', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'nombre_uo', type: 'string'},
		{name:'codigo_uo', type: 'string'},
		{name:'nombre', type: 'string'},

	],
	sortInfo:{
		field: 'id_matriz_modalidad',
		direction: 'DESC'
	},

    tabsouth : [{
        url : '../../../sis_adquisiciones/vista/matriz_concepto/MatrizConcepto.php',
        title : 'Conceptos de Gasto',
        //width:'50%',
        height : '50%',
        cls : 'MatrizConcepto'
    }],

	bdel:true,
	bsave:true
	}
)
</script>
		
		