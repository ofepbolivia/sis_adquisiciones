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
    Phx.vista.SolicitudVb = {
        bedit:true,
        bnew:false,
        bsave:false,
        bdel:false,
        require: '../../../sis_adquisiciones/vista/solicitud/Solicitud.php',
        requireclase: 'Phx.vista.Solicitud',
        title: 'Solicitud',
        nombreVista: 'SolicitudVb',

        beditGroups: [0,1],
        bdelGroups:  [],
        bactGroups:  [0,1],
        btestGroups: [],
        bexcelGroups: [0,1],

        gruposBarraTareas:[
            {name:'local',title:'<H1 align="center">Moneda Local</h1>',grupo:0,height:0},
            {name:'extranjera',title:'<H1 align="center">Moneda Extranjera</h1>',grupo:1,height:0}
        ],

        actualizarSegunTab: function(name, indice){
            if(name == 'local'){
                this.store.baseParams.moneda_base = 'base';
            }else{
                this.getBoton('btnDetalleGasto').setVisible(true);
                this.store.baseParams.moneda_base = 'extranjera';
            }

            this.load({params:{start:0, limit:this.tam_pag}});

        },

        constructor: function(config) {

            this.maestro=config.maestro;

            this.Atributos.splice(7,0, {
                config:{
                    name: 'importe_total',
                    fieldLabel: 'Importe',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 120,
                    maxLength:100,
                    renderer:function (value,p,record){

                        Number.prototype.formatDinero = function(c, d, t){
                            var n = this,
                                c = isNaN(c = Math.abs(c)) ? 2 : c,
                                d = d == undefined ? "." : d,
                                t = t == undefined ? "," : t,
                                s = n < 0 ? "-" : "",
                                i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                                j = (j = i.length) > 3 ? j % 3 : 0;
                            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
                        };

                        return  String.format('<div style="vertical-align:middle;text-align:right; color:green;"><b><span >{0}</span></b></div>',(parseFloat(value)).formatDinero(2, ',', '.'));
                    }
                },
                type:'MoneyField',
                grid:true,
                form:false
            });


            this.Atributos[this.getIndAtributo('id_funcionario_aprobador')].form=false;
            // this.Atributos[this.getIndAtributo('id_moneda')].form=false;
            // //this.Atributos[this.getIndAtributo('id_proceso_macro')].form=false;
            // this.Atributos[this.getIndAtributo('fecha_soli')].form=false;
            // this.Atributos[this.getIndAtributo('id_categoria_compra')].form=false;
            this.Atributos[this.getIndAtributo('id_uo')].form=false;
            // this.Atributos[this.getIndAtributo('id_depto')].form=false;
            this.Atributos[this.getIndAtributo('revisado_asistente')].grid=true;

            this.Atributos[this.getIndAtributo('tipo')].form=true;
            this.Atributos[this.getIndAtributo('tipo_concepto')].form=true;
            this.Atributos[this.getIndAtributo('id_moneda')].form=true;
            this.Atributos[this.getIndAtributo('fecha_soli')].form=true;
            this.Atributos[this.getIndAtributo('id_depto')].form=true;
            this.Atributos[this.getIndAtributo('id_funcionario')].form=true;
            this.Atributos[this.getIndAtributo('id_categoria_compra')].form=true;
            this.Atributos[this.getIndAtributo('id_proveedor')].form=true;
            this.Atributos[this.getIndAtributo('justificacion')].form=true;
            this.Atributos[this.getIndAtributo('lugar_entrega')].form=true;
            this.Atributos[this.getIndAtributo('fecha_inicio')].form=true;
            this.Atributos[this.getIndAtributo('dias_plazo_entrega')].form=true;
            this.Atributos[this.getIndAtributo('id_prioridad')].form=true;

            this.Atributos[this.getIndAtributo('estado')].form=true;



            //funcionalidad para listado de historicos
            this.historico = 'no';
            this.tbarItems = ['-',{
                text: 'Histórico',
                enableToggle: true,
                pressed: false,
                toggleHandler: function(btn, pressed) {

                    if(pressed){
                        this.historico = 'si';
                        this.desBotoneshistorico();
                    }
                    else{
                        this.historico = 'no'
                    }

                    this.store.baseParams.historico = this.historico;
                    this.reload();
                },
                scope: this
            }];

            Phx.vista.SolicitudVb.superclass.constructor.call(this,config);
            this.addButton('ini_estado',{ grupo:[0,1], argument: {estado: 'inicio'},text:'Dev. al Solicitante',iconCls: 'batras',disabled:true,handler:this.antEstado,tooltip: '<b>Retorna la Solcitud al estado borrador</b>'});
            this.addButton('ant_estado',{ grupo:[0,1], argument: {estado: 'anterior'},text:'Rechazar',iconCls: 'batras',disabled:true,handler:this.antEstado,tooltip: '<b>Pasar al Anterior Estado</b>'});
            this.addButton('sig_estado',{ grupo:[0,1], text:'Aprobar', iconCls: 'badelante', disabled: true, handler: this.sigEstado, tooltip: '<b>Pasar al Siguiente Estado</b>'});

            //(f.e.a)moneda base
            this.store.baseParams={tipo_interfaz:this.nombreVista, moneda_base : 'base'};
            //coloca filtros para acceso directo si existen
            if(config.filtro_directo){
                this.store.baseParams.filtro_valor = config.filtro_directo.valor;
                this.store.baseParams.filtro_campo = config.filtro_directo.campo;
            }

            //carga inicial de la pagina
            this.load({params:{start:0, limit:this.tam_pag}});

            if(this.nombreVista == 'solicitudvbpoa') {
                this.addButton('obs_poa',{ grupo:[0,1], text:'Datos POA', disabled:true, handler: this.initObs, tooltip: '<b>Código de actividad POA</b>'});
                this.crearFormObs();
            }
            if(this.nombreVista == 'solicitudvbpresupuestos') {
                this.addButton('obs_presu',{grupo:[0,1], text:'Obs. Presupuestos', disabled:true, handler: this.initObs, tooltip: '<b>Observacioens del área de presupuesto</b>'});
                this.crearFormObs();
            }

        },


        //deshabilitas botones para informacion historica
        desBotoneshistorico:function(){

            this.getBoton('ant_estado').disable();
            this.getBoton('sig_estado').disable();
            this.getBoton('ini_estado').disable();

        },
        preparaMenu:function(n){
            var data = this.getSelectedData();
            var tb =this.tbar;
            Phx.vista.SolicitudVb.superclass.preparaMenu.call(this,n);

            if(this.historico == 'no'){
                if(data.estado =='aprobado' ){
                    this.getBoton('ant_estado').enable();
                    this.getBoton('sig_estado').disable();
                    this.getBoton('ini_estado').enable();
                }
                if(data.estado =='proceso'){
                    this.getBoton('ant_estado').disable();
                    this.getBoton('sig_estado').disable();
                    this.getBoton('ini_estado').disable();
                }

                if(data.estado =='anulado' || data.estado =='finalizado'){
                    this.getBoton('ant_estado').disable();
                    this.getBoton('sig_estado').disable();
                    this.getBoton('ini_estado').disable();
                }

                if(data.estado !='aprobado' && data.estado !='proceso' &&data.estado !='anulado' && data.estado !='finalizado' ){
                    this.getBoton('ant_estado').enable();
                    this.getBoton('sig_estado').enable();
                    this.getBoton('ini_estado').enable();
                }

                if(data.estado =='vbpoa'){
                    if(this.getBoton('obs_poa')){
                        this.getBoton('obs_poa').enable();
                    }
                }
                if(this.getBoton('obs_presu')){
                    this.getBoton('obs_presu').enable();
                }

                //habilitar reporte de colicitud de comrpa y preorden de compra
                this.menuAdq.enable();
            }
            else{
                this.desBotoneshistorico();
                if(this.getBoton('obs_poa')){
                    this.getBoton('obs_poa').enable();
                }
                if(this.getBoton('obs_presu')){
                    this.getBoton('obs_presu').disable();
                }

            }
            return tb
        },
        liberaMenu:function(){
            var tb = Phx.vista.SolicitudVb.superclass.liberaMenu.call(this);
            if(tb){
                this.getBoton('sig_estado').disable();
                this.getBoton('ini_estado').disable();
                this.getBoton('ant_estado').disable();
                if(this.getBoton('obs_poa')){
                    this.getBoton('obs_poa').disable();
                }
                if(this.getBoton('obs_presu')){
                    this.getBoton('obs_presu').disable();
                }
                //boton de reporte de solicitud y preorden de compra
                this.menuAdq.disable();

            }
            return tb
        },

        checkPresupuesto:function(){
            var d = this.getSelectedData();
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                // form:this.form.getForm().getEl(),
                url:'../../sis_adquisiciones/control/Solicitud/checkPresupuesto',
                params:{id_solicitud:d.id_solicitud, id_funcionario:d.id_funcionario},
                success:this.successCheck,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });

        },

        successCheck:function(resp){

            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if(!reg.ROOT.error){
                this.reload();
            }
        },

        sigEstado:function(){
            var rec=this.sm.getSelected();
            if (this.nombreVista == 'solicitudvbpoa' && rec.data.codigo_poa == null){
                Ext.Msg.show({
                    title:'<h1 style="font-size:15px;">Aviso!</h1>',
                    msg: '<p style="font-weight:bold; font-size:12px;">Es necesario registrar el codigo poa previamente</p>',
                    buttons: Ext.Msg.OK,
                    width:450,
                    height:200,
                    icon: Ext.MessageBox.WARNING,
                    scope:this
                });
            }else {
                this.mostrarWizard(rec);
            }


        },
        mostrarWizard : function(rec) {
            var configExtra = [],
                obsValorInicial;
            if(this.nombreVista == 'solicitudvbpresupuestos') {
                obsValorInicial = rec.data.obs_presupuestos;
            }

            //Obtenemos en codigo del proceso macro para verificar si es compra internacional
            if(rec.data.estado == 'vbrpc' && (rec.data.num_tramite.split('-')[0] == 'CINTPD' ||
                rec.data.num_tramite.split('-')[0] == 'GO' || rec.data.num_tramite.split('-')[0] == 'GM' ||
                rec.data.num_tramite.split('-')[0] == 'GA' || rec.data.num_tramite.split('-')[0] == 'GC')){
                configExtra = [
                    {
                        config:{
                            name: 'instruc_rpc',
                            fieldLabel:'Intrucciones',
                            allowBlank: false,
                            emptyText:'Tipo...',
                            typeAhead: true,
                            triggerAction: 'all',
                            lazyRender: true,
                            mode: 'local',
                            valueField: 'estilo',
                            gwidth: 100,
                            value: 'Orden de Bien/Servicio',
                            store: ['Iniciar Contrato','Orden de Bien/Servicio','Cotizar'],
                            msgTarget: 'side',
                            anchor: '50%'
                        },
                        type:'ComboBox',
                        id_grupo: 1,
                        form: true
                    },

                    {
                        config : {
                            name : 'prioridad',
                            fieldLabel : 'Prioridad',
                            allowBlank : false,
                            emptyText : 'Estación...',
                            store: new Ext.data.ArrayStore({
                                fields :['id_prioridad','valor'],
                                data :  [
                                    ['383','AOG'],
                                    ['384','A'],
                                    ['385','B'],
                                    ['386','C'],
                                    ['387','No Aplica']
                                ]}
                            ),
                            tpl: new Ext.XTemplate([
                                '<tpl for=".">',
                                '<div class="x-combo-list-item">',
                                '<div class="awesomecombo-item {checked}">',
                                '<p>Prioridad:<b style="color: green;"> {valor}</b></p>',
                                '</div>',
                                '</div><div><p><img src="./../../../sis_adquisiciones/media/images/{valor}.png" width="215" height="25"></p>',
                                '</div></tpl>'
                            ]),
                            valueField: 'id_prioridad',
                            displayField: 'valor',
                            typeAhead: true,
                            triggerAction: 'all',
                            mode: 'local',
                            selectOnFocus: true,
                            msgTarget:'side',
                            anchor: '50%',
                            editable: false
                        },
                        type : 'AwesomeCombo',
                        id_grupo : 2,
                        grid : true,
                        form : true
                    },

                    {
                        config:{
                            name: 'nro_po',
                            fieldLabel: 'Nro. de P.O.',
                            qtip:'Ingrese el nro. de P.O.',
                            allowBlank: true,
                            disabled: true,
                            anchor: '45%',
                            gwidth: 100,
                            maxLength:255,
                            value: rec.data.nro_po,
                            msgTarget: 'side'
                        },
                        type:'TextField',
                        id_grupo:1,
                        grid:false,
                        form:true
                    },

                    {
                        config:{
                            name: 'fecha_po',
                            fieldLabel: 'Fecha de P.O.',
                            qtip:'Fecha del P.O.',
                            allowBlank: false,
                            anchor: '45.5%',
                            gwidth: 100,
                            value: rec.data.fecha_po || new Date() ,
                            format: 'd/m/Y',
                            renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''},
                            msgTarget: 'side'
                        },
                        type:'DateField',
                        id_grupo:1,
                        grid:false,
                        form:true
                    },

                    {
                        config: {
                            name: 'lista_comision',
                            fieldLabel: 'Lista de Comisión',
                            allowBlank: true,
                            emptyText: 'Seleccion...',
                            store: new Ext.data.JsonStore({
                                url: '../../sis_adquisiciones/control/ComisionMem/listarComision',
                                id: 'id_integrante',
                                root: 'datos',
                                sortInfo: {
                                    field: 'orden',
                                    direction: 'ASC'
                                },
                                totalProperty: 'total',
                                fields: ['id_integrante','desc_funcionario1','orden'],
                                remoteSort: true,
                                baseParams: {par_filtro: 'tc.desc_funcionario1'}//#FUNCAR.nombre_cargo
                            }),
                            valueField: 'id_integrante',
                            displayField: 'desc_funcionario1',
                            gdisplayField: 'desc_funcionario1',
                            hiddenName: 'id_integrante',
                            //tpl:'<tpl for="."><div class="x-combo-list-item"><p>{desc_funcionario1}</p><p style="color: green">{nombre_cargo}<br>{email_empresa}</p><p style="color:green">{oficina_nombre} - {lugar_nombre}</p></div></tpl>',
                            forceSelection: true,
                            typeAhead: false,
                            triggerAction: 'all',
                            lazyRender: true,
                            mode: 'remote',
                            pageSize: 10,
                            queryDelay: 1000,
                            anchor: '59%',
                            gwidth: 200,
                            minChars: 2,
                            resizable:true,
                            //listWidth:'240',
                            enableMultiSelect: true,
                            msgTarget: 'side'
                        },

                        type:'AwesomeCombo',
                        id_grupo: 1,
                        form: true
                    }
                ];
            }else if(rec.data.estado == 'vbrpc' && rec.data.num_tramite.split('-')[0] == 'CNAPD'){

                configExtra = [
                    {
                        config:{
                            name: 'instruc_rpc',
                            fieldLabel:'Intrucciones',
                            allowBlank: false,
                            emptyText:'Tipo...',
                            typeAhead: true,
                            triggerAction: 'all',
                            lazyRender: true,
                            mode: 'local',
                            valueField: 'estilo',
                            gwidth: 100,
                            anchor: '50%',
                            value: 'Orden de Bien/Servicio',
                            store: ['Iniciar Contrato','Orden de Bien/Servicio','Cotizar']
                        },
                        type:'ComboBox',
                        id_grupo: 1,
                        form: true
                    },
                    {
                        config : {
                            name : 'prioridad',
                            fieldLabel : 'Prioridad',
                            allowBlank : false,
                            emptyText : 'Estación...',
                            store: new Ext.data.ArrayStore({
                                fields :['id_prioridad','valor'],
                                data :  [
                                    ['383','AOG'],
                                    ['384','A'],
                                    ['385','B'],
                                    ['386','C'],
                                    ['387','No Aplica']
                                ]}
                            ),
                            //tpl : '<tpl for="."><div class="x-combo-list-item"><p style="color: green;">Código: {codigo}</p><p>Nombre: {nombre}</p></div></tpl>',
                            tpl: new Ext.XTemplate([
                                '<tpl for=".">',
                                '<div class="x-combo-list-item">',
                                '<div class="awesomecombo-item {checked}">',
                                '<p>Prioridad:<b style="color: green;"> {valor}</b></p>',
                                '</div>',
                                '</div><div><p><img src="./../../../sis_adquisiciones/media/images/{valor}.png" width="215" height="25"></p>',
                                '</div></tpl>'
                            ]),
                            valueField: 'id_prioridad',
                            displayField: 'valor',
                            typeAhead: true,
                            triggerAction: 'all',
                            mode: 'local',
                            selectOnFocus: true,
                            msgTarget:'side',
                            anchor: '50%',
                            editable: false
                        },

                        type : 'AwesomeCombo',
                        id_grupo : 2,
                        grid : true,
                        form : true
                    },
                ]
            }


            this.objWizard = Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/FormEstadoWf.php',
                'Estado de Wf',
                {
                    modal: true,
                    width: 700,
                    height: 500
                }, {
                    configExtra: configExtra,
                    data:{
                        id_estado_wf: rec.data.id_estado_wf,
                        id_proceso_wf: rec.data.id_proceso_wf,
                        fecha_ini: rec.data.fecha_tentativa,
                        url_verificacion:'../../sis_adquisiciones/control/Solicitud/verficarSigEstSolWf'
                    },
                    obsValorInicial : obsValorInicial,
                }, this.idContenedor,'FormEstadoWf',
                {
                    config:[{
                        event:'beforesave',
                        delegate: this.onSaveWizard,

                    },
                        {
                            event:'requirefields',
                            delegate: function () {
                                this.onButtonEdit();
                                this.window.setTitle('Registre los campos antes de pasar al siguiente estado');
                                this.formulario_wizard = 'si';
                                this.Cmp.nro_po.setValue('ciris');
                            }

                        }],

                    scope:this
                });

            /*Ext.getCmp(this.objWizard).autoLoad.params.configExtra[1].value = 'Bolivia';
            console.log('sexo',(Ext.getCmp(this.objWizard).autoLoad.params.configExtra[1]))*/
            //Ext.getCmp(this.objWizard).autoload.params.configExtra[1].config.name.setValue('sexo');
        },
        imprimir_reporte: function(){
             var rec = this.sm.getSelected(),
             data = rec.data,
             me = this;
             console.log("llega aqui",data);
           if (data.estado == 'vbpresupuestos') {
             Phx.CP.loadingShow();
             Ext.Ajax.request({
                 url : '../../sis_presupuestos/control/Presupuesto/reporteCertificacionP',
                 params : {
                   'id_proceso_wf' : data.id_proceso_wf,
                 },
                 success : me.generarReporte,
                 failure : me.failureCheck,
                 timeout : me.timeout,
                 scope : me
               });
           }
       },

       generarReporte: function (resp) {
         Phx.CP.loadingHide();
         console.log("llega aqui para mostrar",resp);
         var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
          console.log("llega aqui para mostrar",objRes);
         var objetoDatos = (objRes.ROOT == undefined)?objRes.datos:objRes.ROOT.detalle.archivo_generado;
         console.log("llega aqui para imprimir",objetoDatos);

         //  var objetoDatos = (objRes.ROOT == undefined)?objRes.datos:objRes.ROOT.datos;
         // console.log("llega aqui para imprimir",objetoDatos);

         window.open('../../../lib/lib_control/Intermediario.php?r=' + objetoDatos);
         //wnd.document.write(objetoDatos.html);
          //wnd.document.write(resp.ROOT.detalle.archivo_generado);
       },

        onSaveWizard:function(wizard,resp){
            console.log('wizard',wizard,'resp',resp);
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_adquisiciones/control/Solicitud/siguienteEstadoSolicitudWzd',
                params:{
                    id_proceso_wf_act:  resp.id_proceso_wf_act,
                    id_estado_wf_act:   resp.id_estado_wf_act,
                    id_tipo_estado:     resp.id_tipo_estado,
                    id_funcionario_wf:  resp.id_funcionario_wf,
                    id_depto_wf:        resp.id_depto_wf,
                    obs:                resp.obs,
                    instruc_rpc:		resp.instruc_rpc,
                    lista_comision:     resp.lista_comision,
                    prioridad:          resp.prioridad,
                    json_procesos:      Ext.util.JSON.encode(resp.procesos)
                },
                success: this.successWizard,
                failure: this.failureCheck, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                argument: { wizard:wizard },
                timeout: this.timeout,
                scope: this
            });
        },
        successWizard:function(resp){
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy()
            this.reload();
            this.imprimir_reporte();
        },

        failureCheck:function(resp1,resp2,resp3){

            this.conexionFailure(resp1,resp2,resp3);
            var d= this.sm.getSelected().data;
            if(d.estado == 'vbpresupuestos'){
                this.checkPresupuesto();
            }

        },

        //para retroceder de estado
        antEstado:function(res){
            var rec=this.sm.getSelected(),
                obsValorInicial;

            if(this.nombreVista == 'solicitudvbpresupuestos') {
                obsValorInicial = rec.data.obs_presupuestos;
            }
            Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/AntFormEstadoWf.php',
                'Estado de Wf',
                {
                    modal:true,
                    width:450,
                    height:250

                }, { data:rec.data,
                    estado_destino: res.argument.estado,
                    obsValorInicial: obsValorInicial }, this.idContenedor,'AntFormEstadoWf',
                {
                    config:[{
                        event:'beforesave',
                        delegate: this.onAntEstado,
                    }
                    ],
                    scope:this
                })
        },

        onAntEstado: function(wizard,resp){
            Phx.CP.loadingShow();
            var operacion = 'cambiar';
            operacion=  resp.estado_destino == 'inicio'?'inicio':operacion;
            Ext.Ajax.request({
                url:'../../sis_adquisiciones/control/Solicitud/anteriorEstadoSolicitud',
                params:{
                    id_proceso_wf: resp.id_proceso_wf,
                    id_estado_wf:  resp.id_estado_wf,
                    obs: resp.obs,
                    operacion: operacion,
                    id_solicitud: resp.data.id_solicitud
                },
                argument:{wizard:wizard},
                success: this.successEstadoSinc,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });

        },

        successEstadoSinc:function(resp){
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy()
            this.reload();
        },
        crearFormObs:function(){

            var titulo;
            if(this.nombreVista == 'solicitudvbpoa') {
                titulo = 'Datos POA';
                this.formObs = new Ext.form.FormPanel({
                    baseCls: 'x-plain',
                    autoDestroy: true,
                    border: false,
                    layout: 'form',
                    autoHeight: true,
                    items: [
                        {
                            name: 'codigo_poa',
                            xtype: 'awesomecombo',
                            fieldLabel: 'Código POA',
                            allowBlank: false,
                            emptyText : 'Actividad...',
                            store : new Ext.data.JsonStore({
                                url : '../../sis_presupuestos/control/Objetivo/listarObjetivo',
                                id : 'codigo',
                                root : 'datos',
                                sortInfo : {
                                    field : 'codigo',
                                    direction : 'ASC'
                                },
                                totalProperty : 'total',
                                fields : ['codigo', 'descripcion','sw_transaccional','detalle_descripcion'],
                                remoteSort : true,
                                baseParams : {
                                    par_filtro : 'obj.codigo#obj.descripcion'
                                }
                            }),
                            valueField : 'codigo',
                            displayField : 'detalle_descripcion',
                            forceSelection : true,
                            typeAhead : false,
                            triggerAction : 'all',
                            lazyRender : true,
                            mode : 'remote',
                            pageSize : 10,
                            queryDelay : 1000,
                            gwidth : 150,
                            minChars : 2,
                            enableMultiSelect:true
                        },

                        {
                            name: 'obs_poa',
                            xtype: 'textarea',
                            fieldLabel: 'Obs POA',
                            allowBlank: true,
                            grow: true,
                            growMin : '80%',
                            value:'',
                            anchor: '80%',
                            maxLength:500
                        }]
                });
            }
            else{
                titulo = 'Observaciones Presupuestos';
                this.formObs = new Ext.form.FormPanel({
                    baseCls: 'x-plain',
                    autoDestroy: true,
                    border: false,
                    layout: 'form',
                    autoHeight: true,
                    items: [
                        {
                            name: 'obs',
                            xtype: 'textarea',
                            fieldLabel: 'Obs',
                            allowBlank: true,
                            grow: true,
                            growMin : '80%',
                            value:'',
                            anchor: '80%',
                            maxLength:500
                        }]
                });

            }



            this.wObs = new Ext.Window({
                title: titulo,
                collapsible: true,
                maximizable: true,
                autoDestroy: true,
                width: 380,
                height: 290,
                layout: 'fit',
                plain: true,
                bodyStyle: 'padding:5px;',
                buttonAlign: 'center',
                items: this.formObs,
                modal:true,
                closeAction: 'hide',
                buttons: [{
                    text: 'Guardar',
                    handler: this.submitObs,
                    scope: this

                },
                    {
                        text: 'Cancelar',
                        handler:function(){this.wObs.hide()},
                        scope:this
                    }]
            });
            if(this.nombreVista == 'solicitudvbpoa') {
                this.cmbObsPoa = this.formObs.getForm().findField('obs_poa');
                this.cmbCodigoPoa = this.formObs.getForm().findField('codigo_poa');
            }
            else{
                this.cmbObsPres = this.formObs.getForm().findField('obs');
            }
        },

        initObs:function(){
            var d= this.sm.getSelected().data;
            if(this.nombreVista == 'solicitudvbpoa') {
                this.cmbObsPoa.setValue(d.obs_poa);
                this.cmbCodigoPoa.store.baseParams.id_gestion = d.id_gestion;
                this.cmbCodigoPoa.store.baseParams.sw_transaccional = 'movimiento';

                this.cmbCodigoPoa.setValue(d.codigo_poa);
            }
            else{
                this.cmbObsPres.setValue(d.obs_presupuestos);
            }
            this.wObs.show()
        },

        submitObs:function(){
            Phx.CP.loadingShow();
            var d= this.sm.getSelected().data,
                url, params;
            if(this.nombreVista == 'solicitudvbpoa') {
                url = '../../sis_adquisiciones/control/Solicitud/modificarObsPoa';
                params = {
                    id_solicitud:d.id_solicitud,
                    obs_poa: this.cmbObsPoa.getValue(),
                    codigo_poa: this.cmbCodigoPoa.getValue()
                };
            }
            else{
                url = '../../sis_adquisiciones/control/Solicitud/modificarObsPresupuestos';
                params= { id_solicitud:d.id_solicitud,
                    obs: this.cmbObsPres.getValue()
                };
            }
            Ext.Ajax.request({
                url: url,
                params: params,
                success: function(resp){
                    Phx.CP.loadingHide();
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    if(!reg.ROOT.error){
                        this.reload();
                        this.wObs.hide();
                    }
                },
                failure: function(resp1,resp2,resp3){

                    this.conexionFailure(resp1,resp2,resp3);
                    var d = this.sm.getSelected().data;


                },
                timeout:this.timeout,
                scope:this
            });

        },

        onButtonEdit:function() {

            // this.cmpFechaSoli.disable();
            // this.cmpIdDepto.disable();
            this.Cmp.fecha_soli.disable();
            this.Cmp.id_depto.disable();
            this.Cmp.id_funcionario.disable();
            this.Cmp.id_categoria_compra.disable();
            this.Cmp.precontrato.disable();
            this.Cmp.tipo_concepto.disable();
            this.Cmp.id_moneda.disable();
            this.Cmp.id_proveedor.disable();
            this.Cmp.lugar_entrega.disable();
            this.Cmp.dias_plazo_entrega.disable();
            this.Cmp.fecha_inicio.disable();
            this.Cmp.id_prioridad.disable();
            this.Cmp.nro_po.disable();
            this.Cmp.fecha_po.disable();

            this.ocultarComponente(this.Cmp.estado);

            Phx.vista.SolicitudVb.superclass.onButtonEdit.call(this);

            if (this.Cmp.estado.getValue()== 'vbpresupuestos'){
                this.Cmp.justificacion.enable();
            }else {
                this.Cmp.justificacion.disable();
            }

            //nos permite habilitar para la categoria de compra internacional el campo nro_po, fecha_po
            if(this.Cmp.id_categoria_compra.getValue()==6 || this.Cmp.id_categoria_compra.getValue()==4){
                this.mostrarComponente(this.Cmp.nro_po);
                this.mostrarComponente(this.Cmp.fecha_po);
            }else{
                this.ocultarComponente(this.Cmp.nro_po);
                this.ocultarComponente(this.Cmp.fecha_po);
            }
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
