<?php
/**
*@package pXP
*@file gen-TresolucionesInfoPre.php
*@author  (maylee.perez)
*@date 07-12-2020 19:01:00
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.TresolucionesInfoPre=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.TresolucionesInfoPre.superclass.constructor.call(this,config);
		this.init();
		this.load({params:{start:0, limit:this.tam_pag}})
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_resoluciones_info_pre'
			},
			type:'Field',
			form:true 
		},

        {
            config:{
                name : 'id_gestion',
                origen : 'GESTION',
                fieldLabel : 'Gestión',
                allowBlank : false,
                resizable:true,
                gdisplayField : 'gestion',//mapea al store del grid
                anchor: '80%',
                gwidth : 100,
                renderer : function (value, p, record){return String.format('{0}', record.data['gestion']);}
            },
            type : 'ComboRec',
            id_grupo : 2,
            filters : {
                pfiltro : 'reinpre.gestion',
                type : 'numeric'
            },

            grid : false,
            form : true
        },
        {
            config:{
                name: 'gestion',
                fieldLabel: 'Gestión',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:200
            },
            type:'NumberField',
            filters:{pfiltro:'reinpre.gestion',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:false
        },
		{
			config:{
				name: 'nro_directorio',
				fieldLabel: 'Resolución Administrativa de Directorio',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:500
			},
				type:'TextField',
				filters:{pfiltro:'reinpre.nro_directorio',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'nro_nota',
				fieldLabel: 'Nota Entregado Ministerio de Economía y Finanzas Públicas',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:500
			},
				type:'TextField',
				filters:{pfiltro:'reinpre.nro_nota',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'nro_nota2',
				fieldLabel: 'Nota Ministerio de Economía y Finanzas Públicas ',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:500
			},
				type:'TextField',
				filters:{pfiltro:'reinpre.nro_nota2',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
        {
            config: {
                name: 'fecha_certificacion',
                fieldLabel: 'Fecha Certificación Presupuestaria',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                format: 'd/m/Y'
            },
            type: 'DateField',
            id_grupo: 1,
            grid:true,
            form:true
        },
		{
			config:{
				name: 'observaciones',
				fieldLabel: 'Observaciones',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:500
			},
				type:'TextField',
				filters:{pfiltro:'reinpre.observaciones',type:'string'},
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
                maxLength:10
            },
            type:'TextField',
            filters:{pfiltro:'reinpre.estado_reg',type:'string'},
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
				maxLength:4
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
				filters:{pfiltro:'reinpre.fecha_reg',type:'date'},
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
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'reinpre.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'reinpre.usuario_ai',type:'string'},
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
				maxLength:4
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
				filters:{pfiltro:'reinpre.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'Resolucion  información presupuestaria',
	ActSave:'../../sis_adquisiciones/control/TresolucionesInfoPre/insertarTresolucionesInfoPre',
	ActDel:'../../sis_adquisiciones/control/TresolucionesInfoPre/eliminarTresolucionesInfoPre',
	ActList:'../../sis_adquisiciones/control/TresolucionesInfoPre/listarTresolucionesInfoPre',
	id_store:'id_resoluciones_info_pre',
	fields: [
		{name:'id_resoluciones_info_pre', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'nro_directorio', type: 'string'},
		{name:'nro_nota', type: 'string'},
		{name:'nro_nota2', type: 'string'},
		{name:'observaciones', type: 'string'},
		{name:'id_gestion', type: 'numeric'},
		{name:'gestion', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'fecha_certificacion', type: 'string'},

	],
	sortInfo:{
		field: 'id_resoluciones_info_pre',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true
	}
)
</script>
		
		