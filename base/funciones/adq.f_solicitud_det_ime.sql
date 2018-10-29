CREATE OR REPLACE FUNCTION adq.f_solicitud_det_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Adquisiciones
 FUNCION: 		adq.f_solicitud_det_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'adq.tsolicitud_det'
 AUTOR: 		 (admin)
 FECHA:	        05-03-2013 01:28:10
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_solicitud_det	integer;

    v_id_partida integer;
    v_id_cuenta integer;
    v_id_auxiliar integer;
    v_id_moneda integer;
    v_fecha_soli date;
    v_monto_ga_mb numeric;
    v_monto_sg_mb numeric;
    v_precio_unitario_mb numeric;
    v_id_gestion integer;
    v_registros_cig record;
    v_id_orden_trabajo		integer;

    v_orden_trabajo			integer;
    v_desc_orden_trabajo		varchar;
    v_desc_centro_costo		varchar;
    v_des_concepto_ingas		varchar;
    v_id_centro_costo			integer;
    v_id_concepto_ingas		integer;


BEGIN

    v_nombre_funcion = 'adq.f_solicitud_det_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'ADQ_SOLD_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		05-03-2013 01:28:10
	***********************************/

	if(p_transaccion='ADQ_SOLD_INS')then

        begin

           -- obtener parametros de solicitud

            select
             s.id_moneda,
             s.fecha_soli,
             s.id_gestion
            into
              v_id_moneda,
              v_fecha_soli,
              v_id_gestion
            from adq.tsolicitud s
            where  s.id_solicitud = v_parametros.id_solicitud;

           --recupera el nombre del concepto de gasto

            select
            cig.desc_ingas
            into
            v_registros_cig
            from param.tconcepto_ingas cig
            where cig.id_concepto_ingas =  v_parametros.id_concepto_ingas;

             --obtener partida, cuenta auxiliar del concepto de gasto

             v_id_partida = NULL;

            --recueprar la partida de la parametrizacion

            SELECT
              ps_id_partida ,
              ps_id_cuenta,
              ps_id_auxiliar
            into
              v_id_partida,
              v_id_cuenta,
              v_id_auxiliar
           FROM conta.f_get_config_relacion_contable('CUECOMP', v_id_gestion, v_parametros.id_concepto_ingas, v_parametros.id_centro_costo,  'No se encontro relación contable para el conceto de gasto: '||v_registros_cig.desc_ingas||'. <br> Mensaje: ');



        IF  v_id_partida  is NULL  THEN

        	raise exception 'No se encontro partida para el concepto de gasto y el centro de costos solicitados';

        END IF;




            --obetener el precio en la moneda base del sistema



            v_monto_ga_mb= param.f_convertir_moneda(
                          v_id_moneda,
                          NULL,   --por defecto moenda base
                          v_parametros.precio_ga,
                          v_fecha_soli,
                          'O',-- tipo oficial, venta, compra
                           NULL);--defecto dos decimales

             v_monto_sg_mb= param.f_convertir_moneda(
                          v_id_moneda,
                          NULL,   --por defecto moenda base
                          v_parametros.precio_sg,
                          v_fecha_soli,
                          'O',-- tipo oficial, venta, compra
                           NULL);--defecto dos decimales

        v_precio_unitario_mb= param.f_convertir_moneda(
                            v_id_moneda,
                            NULL,   --por defecto moenda base
                            v_parametros.precio_unitario,
                            v_fecha_soli,
                            'O',-- tipo oficial, venta, compra
                             NULL);

            --para que sea obligatorio el orden de trabajo
            IF (v_parametros.id_orden_trabajo is Null ) THEN
            	RAISE EXCEPTION 'Completar el campo de Orden de Trabajo';
            end if;



        	--Sentencia de la insercion
        	insert into adq.tsolicitud_det(
			id_centro_costo,
			descripcion,
			precio_unitario,
			id_solicitud,
			id_partida,
			id_orden_trabajo,

			id_concepto_ingas,
			id_cuenta,
			precio_total,
			cantidad,
			id_auxiliar,
			estado_reg,
		    precio_ga,
            precio_sg,
			id_usuario_reg,
			fecha_reg,
			fecha_mod,
			id_usuario_mod,
            precio_ga_mb,
            precio_sg_mb,
            precio_unitario_mb,

            id_activo_fijo,
            fecha_ini_act,
            fecha_fin_act


          	) values(
			v_parametros.id_centro_costo,
			v_parametros.descripcion,
			v_parametros.precio_unitario,
			v_parametros.id_solicitud,
			v_id_partida,
			v_parametros.id_orden_trabajo,

			v_parametros.id_concepto_ingas,
			v_id_cuenta,
			v_parametros.precio_total,
			v_parametros.cantidad_sol,
			v_id_auxiliar,
			'activo',
		    v_parametros.precio_ga,
            v_parametros.precio_sg,
			p_id_usuario,
			now(),
			null,
			null,
            v_monto_ga_mb,
            v_monto_sg_mb,
            v_precio_unitario_mb,

            v_parametros.id_activo_fijo,
            v_parametros.fecha_ini_act,
            v_parametros.fecha_fin_act

			)RETURNING id_solicitud_det into v_id_solicitud_det;

             --para que sea obligatorio el orden de trabajo
            IF (v_parametros.id_orden_trabajo is Null ) THEN
            	RAISE EXCEPTION 'Completar el campo de Orden de Trabajo';
            end if;

            --control del campo cantidad
            IF (v_parametros.cantidad_sol is Null )THEN
            	raise exception 'Completar el campo de Cantidad';
            end if;

            --control para el campo de activo fijo
              select cin.desc_ingas
             into v_des_concepto_ingas
             from param.tconcepto_ingas cin
             where cin.id_concepto_ingas = v_parametros.id_concepto_ingas;

            if (v_parametros.id_activo_fijo is NULL) THEN
            	raise exception 'Completar el campo de Activo Fijo para el Concepto %', v_des_concepto_ingas;
            end if;

            --control de fechas
            IF (v_parametros.fecha_fin_act < v_parametros.fecha_ini_act) THEN
            	raise exception 'La Fecha Inicio es menor a la Fecha Fin';
             end if;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle almacenado(a) con exito (id_solicitud_det'||v_id_solicitud_det||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_solicitud_det',v_id_solicitud_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_DGSTSOL_INS'
 	#DESCRIPCION:	Insercion detalle gasto solicitud
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		06-06-2017
	***********************************/

	elsif(p_transaccion='ADQ_DGSTSOL_INS')then

        begin

           -- obtener parametros de solicitud

            select
             s.id_moneda,
             s.fecha_soli,
             s.id_gestion
            into
              v_id_moneda,
              v_fecha_soli,
              v_id_gestion
            from adq.tsolicitud s
            where  s.id_solicitud = v_parametros.id_solicitud;

           --recupera el nombre del concepto de gasto

            select
            cig.id_concepto_ingas, cig.desc_ingas
            into
            v_registros_cig
            from param.tconcepto_ingas cig
            where upper(cig.desc_ingas) =  upper(v_parametros.concepto_gasto)
            and 'adquisiciones' = ANY(cig.sw_autorizacion);

            IF v_registros_cig.id_concepto_ingas IS NULL THEN
            	raise exception 'No se encontro parametrizado el concepto de gasto %', v_parametros.concepto_gasto;
            END IF;

             --obtener partida, cuenta auxiliar del concepto de gasto

             v_id_partida = NULL;


            --recueprar la partida de la parametrizacion

            SELECT
              ps_id_partida ,
              ps_id_cuenta,
              ps_id_auxiliar
            into
              v_id_partida,
              v_id_cuenta,
              v_id_auxiliar
           FROM conta.f_get_config_relacion_contable('CUECOMP', v_id_gestion, v_registros_cig.id_concepto_ingas, v_parametros.id_centro_costo,  'No se encontro relación contable para el conceto de gasto: '||v_registros_cig.desc_ingas||'. <br> Mensaje: ');


        IF  v_id_partida  is NULL  THEN

        	raise exception 'No se encontro partida para el concepto de gasto y el centro de costos solicitados';

        END IF;

        	--recuperar la orden de trabajo

            select id_orden_trabajo into v_id_orden_trabajo
            from conta.torden_trabajo
            where upper(motivo_orden)=upper(v_parametros.orden_trabajo)
            or upper(codigo)=upper(v_parametros.orden_trabajo)
            or upper(desc_orden) = upper(v_parametros.orden_trabajo);

            IF v_id_orden_trabajo IS NULL THEN
            	raise exception 'No existe la orden de trabajo %', v_parametros.orden_trabajo;
            END IF;

            --obetener el precio en la moneda base del sistema

            v_monto_ga_mb= param.f_convertir_moneda(
                          v_id_moneda,
                          NULL,   --por defecto moenda base
                          v_parametros.precio_ga,
                          v_fecha_soli,
                          'O',-- tipo oficial, venta, compra
                           NULL);--defecto dos decimales

             v_monto_sg_mb= param.f_convertir_moneda(
                          v_id_moneda,
                          NULL,   --por defecto moenda base
                          v_parametros.precio_sg,
                          v_fecha_soli,
                          'O',-- tipo oficial, venta, compra
                           NULL);--defecto dos decimales

        v_precio_unitario_mb= param.f_convertir_moneda(
                            v_id_moneda,
                            NULL,   --por defecto moenda base
                            v_parametros.precio_unitario,
                            v_fecha_soli,
                            'O',-- tipo oficial, venta, compra
                             NULL);


           ---------------------------------
          select otrab.id_orden_trabajo,tcc.id_tipo_cc,cin.id_concepto_ingas
           into v_orden_trabajo,v_id_centro_costo,v_id_concepto_ingas
           from conta.torden_trabajo otrab
           join conta.ttipo_cc_ot tccot on tccot.id_orden_trabajo = otrab.id_orden_trabajo
           join param.ttipo_cc tcc on tcc.id_tipo_cc = tccot.id_tipo_cc
           join param.tcentro_costo ccos on ccos.id_tipo_cc = tcc.id_tipo_cc
           join adq.tsolicitud_det sd on sd.id_centro_costo = ccos.id_centro_costo
           join param.tconcepto_ingas cin on cin.id_concepto_ingas = sd.id_concepto_ingas
           where ccos.id_centro_costo = v_parametros.id_centro_costo;

           select otrab.desc_orden
           into v_desc_orden_trabajo
           from conta.torden_trabajo otrab
           where otrab.id_orden_trabajo = v_id_orden_trabajo;

           select (tcc.codigo||'-'||tcc.descripcion)
           into  v_desc_centro_costo
           from param.ttipo_cc tcc
           join param.tcentro_costo ccos on ccos.id_tipo_cc = tcc.id_tipo_cc
           where ccos.id_centro_costo = v_parametros.id_centro_costo;

           select cin.desc_ingas
           into v_des_concepto_ingas
           from param.tconcepto_ingas cin
           where cin.id_concepto_ingas = v_registros_cig.id_concepto_ingas;


           if ( v_orden_trabajo <> v_id_orden_trabajo)THEN
            	raise exception '-El Orden Trabajo % no pertece al Concepto % del Centro de Costo %',v_desc_orden_trabajo,v_des_concepto_ingas,v_desc_centro_costo  ;
           end if;
        -----------------------------

        	--Sentencia de la insercion
        	insert into adq.tsolicitud_det(
			id_centro_costo,
			descripcion,
			precio_unitario,
			id_solicitud,
			id_partida,
			id_orden_trabajo,
			id_concepto_ingas,
			id_cuenta,
			precio_total,
			cantidad,
			id_auxiliar,
			estado_reg,
		    precio_ga,
            precio_sg,
			id_usuario_reg,
			fecha_reg,
			fecha_mod,
			id_usuario_mod,
            precio_ga_mb,
            precio_sg_mb,
            precio_unitario_mb

          	) values(
			v_parametros.id_centro_costo,
			v_parametros.descripcion,
			v_parametros.precio_unitario,
			v_parametros.id_solicitud,
			v_id_partida,
			v_id_orden_trabajo,
			v_registros_cig.id_concepto_ingas,
			v_id_cuenta,
			v_parametros.precio_total,
			v_parametros.cantidad_sol,
			v_id_auxiliar,
			'activo',
		    v_parametros.precio_ga,
            v_parametros.precio_sg,
			p_id_usuario,
			now(),
			null,
			null,
            v_monto_ga_mb,
            v_monto_sg_mb,
            v_precio_unitario_mb

			)RETURNING id_solicitud_det into v_id_solicitud_det;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle almacenado(a) con exito (id_solicitud_det'||v_id_solicitud_det||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_solicitud_det',v_id_solicitud_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'ADQ_DGSTSOL_ELI'
 	#DESCRIPCION:	Insercion detalle gasto solicitud
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		07-07-2017
	***********************************/

	elsif(p_transaccion='ADQ_DGSTSOL_ELI')then

        begin

        	DELETE
            FROM adq.tsolicitud_det
            where id_solicitud=v_parametros.id_solicitud;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle de gastos de solicitud eliminados');
            v_resp = pxp.f_agrega_clave(v_resp,'id_solicitud',v_parametros.id_solicitud::varchar);

            --Devuelve la respuesta
            return v_resp;
        end;

	/*********************************
 	#TRANSACCION:  'ADQ_SOLD_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		05-03-2013 01:28:10
	***********************************/

	elsif(p_transaccion='ADQ_SOLD_MOD')then

		begin

            -- obtener parametros de solicitud

            select s.id_moneda, s.fecha_soli,s.id_gestion  into v_id_moneda, v_fecha_soli, v_id_gestion
            from adq.tsolicitud s
            where  s.id_solicitud = v_parametros.id_solicitud;



           --recupera el nombre del concepto de gasto

            select
            cig.desc_ingas
            into
            v_registros_cig
            from param.tconcepto_ingas cig
            where cig.id_concepto_ingas =  v_parametros.id_concepto_ingas;

            --obtener partida, cuenta auxiliar del concepto de gasto
            SELECT
              ps_id_partida ,
              ps_id_cuenta,
              ps_id_auxiliar
            into
              v_id_partida,
              v_id_cuenta,
              v_id_auxiliar
          FROM conta.f_get_config_relacion_contable('CUECOMP', v_id_gestion, v_parametros.id_concepto_ingas, v_parametros.id_centro_costo,  'No se encontro relación contable para el concepto de gasto: '||v_registros_cig.desc_ingas||'. <br> Mensaje: ');



        IF  v_id_partida  is NULL  THEN

        	raise exception 'No se encontro partida para el concepto de gasto y el centro de costos solicitados,%, %, %',v_id_gestion, v_parametros.id_concepto_ingas, v_parametros.id_centro_costo;

        END IF;



            v_monto_ga_mb= param.f_convertir_moneda(
                          v_id_moneda,
                          NULL,   --por defecto moenda base
                          v_parametros.precio_ga,
                          v_fecha_soli,
                          'O',-- tipo oficial, venta, compra
                           NULL);--defecto dos decimales

             v_monto_sg_mb= param.f_convertir_moneda(
                          v_id_moneda,
                          NULL,   --por defecto moenda base
                          v_parametros.precio_sg,
                          v_fecha_soli,
                          'O',-- tipo oficial, venta, compra
                           NULL);--defecto dos decimales


            v_precio_unitario_mb= param.f_convertir_moneda(
                            v_id_moneda,
                            NULL,   --por defecto moenda base
                            v_parametros.precio_unitario,
                            v_fecha_soli,
                            'O',-- tipo oficial, venta, compra
                             NULL);

			--Sentencia de la modificacion
			update adq.tsolicitud_det set
			id_centro_costo = v_parametros.id_centro_costo,
			descripcion = v_parametros.descripcion,
			precio_unitario = v_parametros.precio_unitario,
			id_solicitud = v_parametros.id_solicitud,
			id_partida = v_id_partida,
			id_orden_trabajo = v_parametros.id_orden_trabajo,
			precio_sg = v_parametros.precio_sg,
            precio_ga = v_parametros.precio_ga,
            precio_ga_mb=v_monto_ga_mb,
            precio_sg_mb=v_monto_sg_mb,
			id_concepto_ingas = v_parametros.id_concepto_ingas,
			id_cuenta = v_id_cuenta,
			precio_total = v_parametros.precio_total,
			cantidad = v_parametros.cantidad_sol,
			id_auxiliar = v_id_auxiliar,
            precio_unitario_mb=v_precio_unitario_mb,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
            id_activo_fijo = v_parametros.id_activo_fijo,
            fecha_ini_act = v_parametros.fecha_ini_act,
            fecha_fin_act = v_parametros.fecha_fin_act

			where id_solicitud_det=v_parametros.id_solicitud_det;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_solicitud_det',v_parametros.id_solicitud_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_SOLD_ELI'
 	#DESCRIPCION:	Eliminacion de detalles de la solicitud
 	#AUTOR:		rac (kplian)
 	#FECHA:		05-03-2013 01:28:10
	***********************************/

	elsif(p_transaccion='ADQ_SOLD_ELI')then

		begin
			--Sentencia de la eliminacion

            --delete from adq.tsolicitud_det
            --where id_solicitud_det=v_parametros.id_solicitud_det;

            update adq.tsolicitud_det set
            estado_reg = 'inactivo'
            where id_solicitud_det=v_parametros.id_solicitud_det;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle de solicitud inactivado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_solicitud_det',v_parametros.id_solicitud_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

	end if;

EXCEPTION

	WHEN OTHERS THEN
		v_resp='';
		v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
		v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
		v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
		raise exception '%',v_resp;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;