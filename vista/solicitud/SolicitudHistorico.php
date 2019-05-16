<?php
/**
*@package pXP
*@file gen-SistemaDist.php
*@author  (fprudencio)
*@date 20-09-2011 10:22:05
*@description Archivo con la interfaz de usuario que permite 
*dar el visto a solicitudes de compra
*
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.SolicitudHistorico = {
    bedit:false,
    bnew:false,
    bsave:true,
    bdel:false,
	require:'../../../sis_adquisiciones/vista/solicitud/Solicitud.php',
	requireclase:'Phx.vista.Solicitud',
	title:'Solicitud',
	nombreVista: 'SolicitudHistorico',
	
	constructor: function(config) {
	    
	    this.maestro=config.maestro;
	    
	    this.Atributos[this.getIndAtributo('id_funcionario')].form=false;
        this.Atributos[this.getIndAtributo('id_funcionario_aprobador')].form=false;
        this.Atributos[this.getIndAtributo('id_moneda')].form=false;
        //this.Atributos[this.getIndAtributo('id_proceso_macro')].form=false;
        this.Atributos[this.getIndAtributo('fecha_soli')].form=false;
        this.Atributos[this.getIndAtributo('id_categoria_compra')].form=false;
        this.Atributos[this.getIndAtributo('id_uo')].form=false;
        this.Atributos[this.getIndAtributo('id_depto')].form=false;
        this.Atributos[this.getIndAtributo('revisado_asistente')].grid=true;
        //temporal 16/05/2019 campo CUCE para aumentar datos a la tabla solicitud
        this.Atributos[this.getIndAtributo('cuce')].grid = true;
        this.Atributos[this.getIndAtributo('cuce')].form = true;
        
        //funcionalidad para listado de historicos
        this.historico = 'no';
        this.tbarItems = ['-',{
            text: 'Histórico',
            enableToggle: true,
            pressed: false,
            toggleHandler: function(btn, pressed) {
               
                if(pressed){
                     this.historico = 'si';
                    
                }
                else{
                   this.historico = 'no' 
                }
                
                this.store.baseParams.historico = this.historico;
                this.reload();
             },
            scope: this
           }];
        
    	Phx.vista.SolicitudHistorico.superclass.constructor.call(this,config);

        this.addButton('verificar_presupuesto', {
            text : 'Revertir Presup.',
            grupo:[0,1,2],
            iconCls : 'bassign',
            disabled : false,
            handler : this.onVerificarPresu,
            tooltip : '<b>Revertir  presupuestos</b><br>Permite ver la evolución presupuestaria y revertir parcialmente.</b>'
        });
       
        this.store.baseParams={tipo_interfaz:this.nombreVista};
        //coloca filtros para acceso directo si existen
        if(config.filtro_directo){
           this.store.baseParams.filtro_valor = config.filtro_directo.valor;
           this.store.baseParams.filtro_campo = config.filtro_directo.campo;
        }
        //carga inicial de la pagina
        this.load({params:{start:0, limit:this.tam_pag}}); 
        
       
        
        
		
	},

    preparaMenu:function(n){
        var data = this.getSelectedData();
        var tb =this.tbar;

        this.getBoton('btnChequeoDocumentosWf').setDisabled(false);
        Phx.vista.Solicitud.superclass.preparaMenu.call(this,n);
        this.getBoton('diagrama_gantt').enable();
        this.getBoton('btnObs').enable();
        this.getBoton('btnDetalleGasto').enable();
        this.getBoton('verificar_presupuesto').enable();


        return tb
    },
    liberaMenu:function(){
        var tb = Phx.vista.Solicitud.superclass.liberaMenu.call(this);
        if(tb){
            
            this.getBoton('btnChequeoDocumentosWf').setDisabled(true);
            this.getBoton('diagrama_gantt').disable();
            this.getBoton('btnObs').disable();
            this.getBoton('btnDetalleGasto').disable();
            this.getBoton('verificar_presupuesto').disable();

        }
        return tb
    },
    
    onVerificarPresu : function() {
        var rec = this.sm.getSelected();
        var moneda = rec.data.desc_moneda=='$us'?'Dolares Americanos':'Bolivianos';
        //Se define el nombre de la columna de la llave primaria
        Phx.CP.loadWindows('../../../sis_adquisiciones/vista/solicitud/VerificarPresupuesto.php', 'Evolución presupuestaria ('+moneda+')', {
            modal : true,
            width : '98%',
            height : '70%'
        }, rec.data, this.idContenedor, 'VerificarPresupuesto');
    },
	
	south:
          { 
          url:'../../../sis_adquisiciones/vista/solicitud_det/SolicitudVbDet.php',
          title:'Detalle', 
          height:'50%',
          cls:'SolicitudVbDet'
         }
};
</script>
