<?php
/**
 * @package pXP
 * @file gen-SistemaDist.php
 * @author  Maylee Perez Pastor
 * @date 31-01-2020 10:22:05
 * @description Archivo con la interfaz permite consultar tramites dentro su gerencia
 *
 *
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.SolicitudHistoricoXGerencia = {
        bedit: false,
        bnew: false,
        bsave: false,
        bdel: false,
        require: '../../../sis_adquisiciones/vista/solicitud/Solicitud.php',
        requireclase: 'Phx.vista.Solicitud',
        title: 'Solicitud',
        nombreVista: 'SolicitudHistoricoXGerencia',

        constructor: function (config) {

            this.maestro = config.maestro;

            this.Atributos[this.getIndAtributo('id_funcionario')].form = false;
            this.Atributos[this.getIndAtributo('id_funcionario_aprobador')].form = false;
            this.Atributos[this.getIndAtributo('id_moneda')].form = false;
            //this.Atributos[this.getIndAtributo('id_proceso_macro')].form=false;
            this.Atributos[this.getIndAtributo('fecha_soli')].form = false;
            this.Atributos[this.getIndAtributo('id_categoria_compra')].form = false;
            this.Atributos[this.getIndAtributo('id_uo')].form = false;
            this.Atributos[this.getIndAtributo('id_depto')].form = false;
            this.Atributos[this.getIndAtributo('revisado_asistente')].grid = true;
            // 16/05/2019 campo CUCE para aumentar datos a la tabla solicitud
            this.Atributos[this.getIndAtributo('cuce')].grid = true;
            this.Atributos[this.getIndAtributo('fecha_conclusion')].grid = true;

            this.crearFormCuce();
            //funcionalidad para listado de historicos
            this.historico = 'no';
            this.tbarItems = ['-', {
                text: 'Histórico',
                enableToggle: true,
                pressed: false,
                toggleHandler: function (btn, pressed) {

                    if (pressed) {
                        this.historico = 'si';

                    }
                    else {
                        this.historico = 'no'
                    }

                    this.store.baseParams.historico = this.historico;
                    this.reload();
                },
                scope: this
            }];

            Phx.vista.SolicitudHistoricoXGerencia.superclass.constructor.call(this, config);

            //28-05-2019 se comenta porque se hizo de otra forma el registro.
            // this.addButton('verificar_presupuesto', {
            //     text: 'Revertir Presup.',
            //     grupo: [0, 1, 2],
            //     iconCls: 'bassign',
            //     disabled: false,
            //     handler: this.onVerificarPresu,
            //     tooltip: '<b>Revertir  presupuestos</b><br>Permite ver la evolución presupuestaria y revertir parcialmente.</b>'
            // });
            // 16/05/2019 campo CUCE para aumentar datos a la tabla solicitud
            this.addButton('bmodCuce', {
                text: 'CUCE',
                iconCls: 'bengine',
                disabled: true,
                handler: this.modCuce,
                tooltip: '<b>Modificar CUCE</b><br/>Permite modificar el CUCE de un trámite'
            });


            this.store.baseParams = {tipo_interfaz: this.nombreVista};
            //coloca filtros para acceso directo si existen
            if (config.filtro_directo) {
                this.store.baseParams.filtro_valor = config.filtro_directo.valor;
                this.store.baseParams.filtro_campo = config.filtro_directo.campo;
            }
            //carga inicial de la pagina
            this.load({params: {start: 0, limit: this.tam_pag}});


        },

        preparaMenu: function (n) {
            var data = this.getSelectedData();
            var tb = this.tbar;

            this.getBoton('btnChequeoDocumentosWf').setDisabled(false);
            Phx.vista.Solicitud.superclass.preparaMenu.call(this, n);
            this.getBoton('diagrama_gantt').enable();
            this.getBoton('btnObs').enable();
            this.getBoton('btnDetalleGasto').enable();
            // this.getBoton('verificar_presupuesto').enable();
            this.getBoton('bmodCuce').enable();


            return tb
        },
        liberaMenu: function () {
            var tb = Phx.vista.Solicitud.superclass.liberaMenu.call(this);
            if (tb) {

                this.getBoton('btnChequeoDocumentosWf').setDisabled(true);
                this.getBoton('diagrama_gantt').disable();
                this.getBoton('btnObs').disable();
                this.getBoton('btnDetalleGasto').disable();
                // this.getBoton('verificar_presupuesto').disable();
                this.getBoton('bmodCuce').disable();

            }
            return tb
        },

        onVerificarPresu: function () {
            var rec = this.sm.getSelected();
            var moneda = rec.data.desc_moneda == '$us' ? 'Dolares Americanos' : 'Bolivianos';
            //Se define el nombre de la columna de la llave primaria
            Phx.CP.loadWindows('../../../sis_adquisiciones/vista/solicitud/VerificarPresupuesto.php', 'Evolución presupuestaria (' + moneda + ')', {
                modal: true,
                width: '98%',
                height: '70%'
            }, rec.data, this.idContenedor, 'VerificarPresupuesto');
        },

        // 16/05/2019 campo CUCE para aumentar datos a la tabla solicitud
        crearFormCuce: function () {
            var me = this;
            me.formAjustes = new Ext.form.FormPanel({
                //id: me.idContenedor + '_AJUSTES',
                margins: ' 10 10 10 10',
                items: [
                    {
                        name: 'cuce',
                        xtype: 'field',
                        width: 150,
                        fieldLabel: 'CUCE'
                    },
                    {
                        name: 'fecha_conclusion',
                        xtype: 'datefield',
                        width: 150,
                        fieldLabel: 'Fecha conclusión'

                    },
                    {
                        xtype: 'field',
                        name: 'id_solicitud',
                        labelSeparator: '',
                        inputType: 'hidden'
                    }
                ],
                autoScroll: false,
                autoDestroy: true
            });

            // Definicion de la ventana que contiene al formulario
            me.windowAjustes = new Ext.Window({
                // id:this.idContenedor+'_W',
                title: 'Registrar CUCE',
                margins: ' 10 10 10 10',
                modal: true,
                width: 400,
                height: 150,
                bodyStyle: 'padding:5px;',
                buttonAlign: 'center',
                layout: 'fit',
                plain: true,
                hidden: true,
                autoScroll: false,
                maximizable: true,
                buttons: [{
                    text: 'Guardar',
                    arrowAlign: 'bottom',
                    handler: me.saveAjustes,
                    argument: {
                        'news': false
                    },
                    scope: me

                },
                    {
                        text: 'Declinar',
                        handler: me.onDeclinarAjustes,
                        scope: me
                    }],
                items: me.formAjustes,
                autoDestroy: true,
                closeAction: 'hide'
            });


        },
        saveAjustes: function () {
            var me = this,
                d = me.sm.getSelected().data;
            console.log('llega1',d)
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_adquisiciones/control/Solicitud/insertarCuce',
                success: me.successAjustes,
                failure: me.failureAjustes,
                params: {
                    'id_solicitud': d.id_solicitud,
                    'cuce': me.formAjustes.getForm().findField('cuce').getValue(),
                    'fecha_conclusion': me.formAjustes.getForm().findField('fecha_conclusion').getValue()

                },
                timeout: me.timeout,
                scope: me
            });


        },
        successAjustes: function (resp) {
            Phx.CP.loadingHide();
            this.windowAjustes.hide();
            this.reload();

        },

        failureAjustes: function (resp) {
            Phx.CP.loadingHide();
            Phx.vista.SolicitudHistoricoXGerencia.superclass.conexionFailure.call(this, resp);

        },
        onDeclinarAjustes: function () {
            this.windowAjustes.hide();

        },
        modCuce: function () {
            this.windowAjustes.show();
            this.formAjustes.getForm().reset();
            var d = this.sm.getSelected().data;
            this.formAjustes.getForm().findField('cuce').show();
            this.formAjustes.getForm().findField('cuce').setValue(d.cuce);
            this.formAjustes.getForm().findField('fecha_conclusion').show();
            this.formAjustes.getForm().findField('fecha_conclusion').setValue(d.fecha_conclusion);


        },
//
        south:
            {
                url: '../../../sis_adquisiciones/vista/solicitud_det/SolicitudVbDet.php',
                title: 'Detalle',
                height: '50%',
                cls: 'SolicitudVbDet'
            }
    };
</script>
