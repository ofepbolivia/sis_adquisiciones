<?php
/**
 *@package pXP
 *@file SolicitudAproXCategoria.php
 *@author  (breydi.vasquez)
 *@date 06-01-2020
 *@description Archivo con la interfaz de usuario que permite verificar tramites bloqueados a nivel centro de costos.
 *agregada funcionalidad para aprobar tramites con un presupuesto a nivel categoria programatica.
 *
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.SolicitudAproXCategoria = {
        bedit:false,
        bnew:false,
        bsave:false,
        bdel:false,
        require: '../../../sis_adquisiciones/vista/solicitud/Solicitud.php',
        requireclase: 'Phx.vista.Solicitud',
        title: 'Solicitud',
        nombreVista: 'SolicitudAproXCategoria',

        beditGroups: [0,1],
        bdelGroups:  [],
        bactGroups:  [0,1],
        btestGroups: [],
        bexcelGroups: [0,1],

        constructor: function(config) {

            this.maestro=config.maestro;

            Phx.vista.SolicitudAproXCategoria.superclass.constructor.call(this,config);            
            this.store.baseParams={tipo_interfaz:this.nombreVista, tramite_sin_presupuesto_centro_c : 'sin'};
            this.getBoton('btnDetalleGasto').setVisible(false);
            this.getBoton('btnObs').setVisible(false);                        

            this.addButton('btnPresuAprobado', {                
                text: 'Autorizar',            
                iconCls:'bball_white',            
                handler:this.validarPresupuesto,
                tooltip: '<b>Autoriza</b> el tramite a nivel centro de costo<br/>'
            });
            //carga inicial de la pagina
            this.load({params:{start:0, limit:this.tam_pag}});

        },

        preparaMenu:function(n){ 
            var data = this.getSelectedData();          
            var tb =this.tbar;
            Phx.vista.SolicitudAproXCategoria.superclass.preparaMenu.call(this,n);
            this.menuAdq.enable();
            this.getBoton('btnDetalleGasto').setVisible(false);
            this.getBoton('btnObs').setVisible(false);
            if (data['presupuesto_aprobado'] == 'sin_presupuesto_cc') {
                this.getBoton('btnPresuAprobado').setIconClass('bball_green');
                this.getBoton('btnPresuAprobado').enable();
            }                                                       
            return tb
        },

        validarPresupuesto:function(){
            var d= this.sm.getSelected().data; 
            Phx.CP.loadingHide();            
            Ext.Ajax.request({                
                url:'../../sis_adquisiciones/control/Solicitud/aprobarPresupuestoSolicitud',
                params: { id_solicitud: d.id_solicitud, aprobar: 'si'},                
                success: function(resp){
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));                    
                    if(!reg.ROOT.error) {
                        this.reload();
                    }
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });            
        },        

        south:
            {
                url:'../../../sis_adquisiciones/vista/solicitud_det/SolicitudReqDet.php',
                title:'Detalle',
                height:'50%',
                cls:'SolicitudReqDet'
            }
    };
</script>
