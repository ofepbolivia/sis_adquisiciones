CREATE OR REPLACE FUNCTION adq.f_reporte_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Adquisiciones
 FUNCION: 		adq.f_reporte_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'adq.tproceso_compra'
 AUTOR: 		 (f.e.a)
 FECHA:	        19-02-2018 12:55:30
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

    v_filadd 			varchar;

    va_id_depto 		integer[];
    v_filtro			varchar;
    v_id_gestion 		integer;
    v_nom_fun_resp		varchar;
    v_rec_fun			record;
    v_rec_func			record;
    v_id_usuario		integer;
    v_condicion			varchar;
    v_id_rol			integer;

    v_contador			integer;

BEGIN

	v_nombre_funcion = 'adq.f_proceso_compra_sel';
    v_parametros = pxp.f_get_record(p_tabla);


    /*********************************
 	#TRANSACCION:  'ADQ_FORM_400_SEL'
 	#DESCRIPCION:	Obtiene detalle del formulario 400 de todos los proceso y se aplican diferentes filtros
 	#AUTOR:		FRANKLIN ESPINOZA A.
 	#FECHA:		18-08-2017
	***********************************/
    IF (p_transaccion='ADQ_FORM_400_SEL')THEN
    	BEGIN

            --raise exception  'id_usuario: %', v_parametros.id_usuario;
            SELECT g.id_gestion
            INTO v_id_gestion
            FROM param.tgestion g
            WHERE g.gestion = EXTRACT(YEAR FROM current_date);


            SELECT vfcl.desc_funcionario1--, tur.id_rol
            INTO v_nom_fun_resp--, v_id_rol
            FROM segu.tusuario tu
            INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
            INNER JOIN orga.vfuncionario vfcl on vfcl.id_funcionario = tf.id_funcionario
            --INNER JOIN segu.tusuario_rol tur on tur.id_usuario = p_id_usuario
            WHERE tu.id_usuario = v_parametros.id_usuario ;

            select count(tur.id_rol)
            into v_contador
            from segu.tusuario_rol tur
            where tur.id_usuario = v_parametros.id_usuario and tur.id_rol = 1;

            if (p_administrador = 1 and v_contador > 0) then
            	v_condicion = '';
            else
            	v_condicion = ' and tpc.id_usuario_auxiliar = '||v_parametros.id_usuario;
            end if;
			--raise exception 'v_condicion: %', v_parametros.chequeado;
    		v_consulta = '
            			with formularios as
                        (
            			  SELECT
            				tc.id_cotizacion,
                            tc.id_proceso_wf,
                            tc.id_estado_wf,
                            tc.estado,
                            tc.num_tramite,
                            vf.desc_funcionario1::varchar AS fun_solicitante,
                            '''||v_nom_fun_resp||'''::varchar AS fun_resp,
                            CASE WHEN tdw.chequeado = ''si'' THEN ''TIENE FORM 400''::varchar ELSE ''NO TIENE EL FORM 400''::varchar END AS tieneform400,
                            case when tc.fecha_adju is null then -1 when  adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tc.fecha_adju,15),tc.id_cotizacion, '||p_id_usuario||',400, tdw.chequeado) between 0 and 15 then adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tc.fecha_adju,15), tc.id_cotizacion, '||p_id_usuario||',400, tdw.chequeado)
                            else -2 end as dias_form_400,
                            ts.fecha_inicio,
                            ''ORDEN''::varchar as tipo_doc,
                            tc.fecha_adju as fecha_aprob
            			  FROM adq.tcotizacion tc
                            INNER JOIN wf.tdocumento_wf tdw ON tdw.id_proceso_wf = tc.id_proceso_wf
                            INNER JOIN wf.ttipo_documento ttd ON ttd.id_tipo_documento = tdw.id_tipo_documento
                            INNER JOIN adq.tsolicitud ts ON ts.num_tramite = tc.num_tramite
                            INNER JOIN adq.tproceso_compra tpc ON tpc.num_tramite = tc.num_tramite
                            INNER JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario
                          WHERE ttd.codigo = ''FORM400'' AND tpc.estado = ''proceso'' AND tc.estado != ''anulado'' AND tc.requiere_contrato = ''no'' and
                          ts.id_gestion = '||v_id_gestion||'  and tdw.chequeado = '''||v_parametros.chequeado||''' and tc.fecha_adju is not null '||v_condicion||'

                          union all

                          SELECT
            				tc.id_cotizacion,
                            tc.id_proceso_wf,
                            tc.id_estado_wf,
                            tc.estado,
                            tc.num_tramite,
                            vf.desc_funcionario1::varchar AS fun_solicitante,
                            '''||v_nom_fun_resp||'''::varchar AS fun_resp,
                            CASE WHEN tdw.chequeado = ''si'' THEN ''TIENE FORM 400''::varchar ELSE ''NO TIENE EL FORM 400''::varchar END AS tieneform400,
                            case when tleg.fecha_elaboracion is null then -1 when  adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tleg.fecha_elaboracion,15),tc.id_cotizacion, '||p_id_usuario||',400, tdw.chequeado) between 0 and 15 then adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tleg.fecha_elaboracion,15), tc.id_cotizacion, '||p_id_usuario||',400, tdw.chequeado)
                            else -2 end as dias_form_400,
                            ts.fecha_inicio,
                            ''CONTRATO''::varchar as tipo_doc,
                            tleg.fecha_elaboracion as fecha_aprob
            			  FROM adq.tcotizacion tc
                            INNER JOIN wf.tdocumento_wf tdw ON tdw.id_proceso_wf = tc.id_proceso_wf
                            INNER JOIN wf.ttipo_documento ttd ON ttd.id_tipo_documento = tdw.id_tipo_documento
                            INNER JOIN adq.tsolicitud ts ON ts.num_tramite = tc.num_tramite
                            INNER JOIN adq.tproceso_compra tpc ON tpc.num_tramite = tc.num_tramite
                            INNER JOIN leg.tcontrato tleg on tleg.id_cotizacion = tc.id_cotizacion
                            INNER JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario
                          WHERE ttd.codigo = ''FORM400'' AND tc.requiere_contrato = ''si'' and tleg.fecha_elaboracion is not null and tpc.estado = ''proceso'' AND
                          tc.estado != ''anulado'' AND ts.id_gestion = '||v_id_gestion||'  and tdw.chequeado = '''||v_parametros.chequeado||''''||v_condicion||'
                        )

                        select
                          id_cotizacion,
                          id_proceso_wf,
                          id_estado_wf,
                          estado,
                          num_tramite,
                          fun_solicitante,
                          fun_resp,
                          tieneform400,
                          dias_form_400,
                          fecha_inicio,
                          tipo_doc,
                          fecha_aprob
                        from formularios
                        where ';
                v_consulta=v_consulta||v_parametros.filtro||' order by dias_form_400 asc';
                raise notice 'consulta: %',v_consulta;

			  --Devuelve la respuesta
			  return v_consulta;
      END;
    /*********************************
 	#TRANSACCION:  'ADQ_FORM_400_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		FRANKLIN ESPINOZA A.
 	#FECHA:		18-08-2017
	***********************************/

	elsif(p_transaccion='ADQ_FORM_400_CONT')then

		BEGIN
        	SELECT g.id_gestion
            INTO v_id_gestion
            FROM param.tgestion g
            WHERE g.gestion = EXTRACT(YEAR FROM current_date);


            SELECT vfcl.desc_funcionario1
            INTO v_nom_fun_resp
            FROM segu.tusuario tu
            INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
            INNER JOIN orga.vfuncionario vfcl on vfcl.id_funcionario = tf.id_funcionario
            WHERE tu.id_usuario = p_id_usuario;

            select count(tur.id_rol)
            into v_contador
            from segu.tusuario_rol tur
            where tur.id_usuario = v_parametros.id_usuario and tur.id_rol = 1;

            if (p_administrador = 1 and v_contador > 0) then
            	v_condicion = '';
            else
            	v_condicion = ' and tpc.id_usuario_auxiliar = '||v_parametros.id_usuario;
            end if;

            --Sentencia de la consulta de conteo de registros
    		v_consulta = '
            			with formularios as
                        (
            			  SELECT
            				tc.id_cotizacion,
                            tc.id_proceso_wf,
                            tc.id_estado_wf,
                            tc.estado,
                            tc.num_tramite,
                            vf.desc_funcionario1::varchar AS fun_solicitante,
                            '''||v_nom_fun_resp||'''::varchar AS fun_resp,
                            CASE WHEN tdw.chequeado = ''si'' THEN ''TIENE FORM 400''::varchar ELSE ''NO TIENE EL FORM 400''::varchar END AS tieneform400,
                            case when tc.fecha_adju is null then -1 when  adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tc.fecha_adju,15),tc.id_cotizacion, '||p_id_usuario||',400, tdw.chequeado) between 0 and 15 then adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tc.fecha_adju,15), tc.id_cotizacion, '||p_id_usuario||',400, tdw.chequeado)
                            else -2 end as dias_form_400,
                            ts.fecha_inicio,
                            ''ORDEN''::varchar as tipo_doc
            			  FROM adq.tcotizacion tc
                            INNER JOIN wf.tdocumento_wf tdw ON tdw.id_proceso_wf = tc.id_proceso_wf
                            INNER JOIN wf.ttipo_documento ttd ON ttd.id_tipo_documento = tdw.id_tipo_documento
                            INNER JOIN adq.tsolicitud ts ON ts.num_tramite = tc.num_tramite
                            INNER JOIN adq.tproceso_compra tpc ON tpc.num_tramite = tc.num_tramite
                            INNER JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario
                          WHERE ttd.codigo = ''FORM400'' AND tpc.estado = ''proceso'' AND tc.estado != ''anulado'' AND tc.requiere_contrato = ''no'' and
                          ts.id_gestion = '||v_id_gestion||'  and tdw.chequeado = '''||v_parametros.chequeado||''' and tc.fecha_adju is not null '||v_condicion||'

                          union all

                          SELECT
            				tc.id_cotizacion,
                            tc.id_proceso_wf,
                            tc.id_estado_wf,
                            tc.estado,
                            tc.num_tramite,
                            vf.desc_funcionario1::varchar AS fun_solicitante,
                            '''||v_nom_fun_resp||'''::varchar AS fun_resp,
                            CASE WHEN tdw.chequeado = ''si'' THEN ''TIENE FORM 400''::varchar ELSE ''NO TIENE EL FORM 400''::varchar END AS tieneform400,
                            case when tleg.fecha_elaboracion is null then -1 when  adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tleg.fecha_elaboracion,15),tc.id_cotizacion, '||p_id_usuario||',400, tdw.chequeado) between 0 and 15 then adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tleg.fecha_elaboracion,15), tc.id_cotizacion, '||p_id_usuario||',400, tdw.chequeado)
                            else -2 end as dias_form_400,
                            ts.fecha_inicio,
                            ''CONTRATO''::varchar as tipo_doc
            			  FROM adq.tcotizacion tc
                            INNER JOIN wf.tdocumento_wf tdw ON tdw.id_proceso_wf = tc.id_proceso_wf
                            INNER JOIN wf.ttipo_documento ttd ON ttd.id_tipo_documento = tdw.id_tipo_documento
                            INNER JOIN adq.tsolicitud ts ON ts.num_tramite = tc.num_tramite
                            INNER JOIN adq.tproceso_compra tpc ON tpc.num_tramite = tc.num_tramite
                            INNER JOIN leg.tcontrato tleg on tleg.id_cotizacion = tc.id_cotizacion
                            INNER JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario
                          WHERE ttd.codigo = ''FORM400'' AND tc.requiere_contrato = ''si'' and tleg.fecha_elaboracion is not null and tpc.estado = ''proceso'' AND
                          tc.estado != ''anulado'' AND ts.id_gestion = '||v_id_gestion||'  and tdw.chequeado = '''||v_parametros.chequeado||''''||v_condicion||'
                        )

                        select count(id_cotizacion)
                        from formularios
                        where ';
                v_consulta=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		END;
    /*********************************
 	#TRANSACCION:  'ADQ_FORM_500_SEL'
 	#DESCRIPCION:	Obtiene detalle del formulario 500 de todos los proceso y se aplican diferentes filtros
 	#AUTOR:		FRANKLIN ESPINOZA A.
 	#FECHA:		18-08-2017
	***********************************/
    ELSIF (p_transaccion='ADQ_FORM_500_SEL')THEN
    	BEGIN
            --raise exception 'yusuario: %',v_parametros.id_usuario;
        	SELECT g.id_gestion
            INTO v_id_gestion
            FROM param.tgestion g
            WHERE g.gestion = EXTRACT(YEAR FROM current_date);

            SELECT vfcl.desc_funcionario1
            INTO v_nom_fun_resp
            FROM segu.tusuario tu
            INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
            INNER JOIN orga.vfuncionario vfcl on vfcl.id_funcionario = tf.id_funcionario
            WHERE tu.id_usuario = p_id_usuario;

            select count(tur.id_rol)
            into v_contador
            from segu.tusuario_rol tur
            where tur.id_usuario = v_parametros.id_usuario and tur.id_rol = 1;

            if (p_administrador = 1 and v_contador > 0) then
            	v_condicion = '';
            else
            	v_condicion = ' and tpc.id_usuario_auxiliar = '||v_parametros.id_usuario;
            end if;

            v_consulta = '
            			with formularios as
                        (
            			  SELECT
            			  	tc.id_cotizacion,
                            tc.id_proceso_wf,
                            tc.id_estado_wf,
                            tc.estado,
                            tc.num_tramite,
                            vf.desc_funcionario1::varchar AS fun_solicitante,
                            '''||v_nom_fun_resp||'''::varchar AS fun_resp,
                            CASE WHEN tdw.chequeado = ''si'' THEN ''TIENE FORM 500''::varchar ELSE ''NO TIENE EL FORM 500''::varchar END AS tieneform500,
                            case when tc.fecha_adju is null then -1
                            when  adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tpp.fecha_conformidad,15),tc.id_cotizacion, '||p_id_usuario||',500, tdw.chequeado) between 0 and 15 then adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tpp.fecha_conformidad,15), tc.id_cotizacion, '||p_id_usuario||',500, tdw.chequeado)
                            else -2 end as dias_form_500,
                            ts.fecha_inicio,
                            ''ORDEN''::varchar as tipo_doc,
                            CASE WHEN tpp.conformidad != '''' THEN ''TIENE CONFORMIDAD''::varchar ELSE ''NO TIENE CONFORMIDAD''::varchar END AS conformidad,
                            tpp.fecha_conformidad,
                            tpp.nro_cuota
                          FROM adq.tcotizacion tc
                          INNER JOIN adq.tproceso_compra tpc ON tpc.id_proceso_compra = tc.id_proceso_compra
                          INNER JOIN wf.tdocumento_wf tdw ON tdw.id_proceso_wf = tc.id_proceso_wf
                          INNER JOIN wf.ttipo_documento ttd ON ttd.id_tipo_documento = tdw.id_tipo_documento
                          INNER JOIN tes.tobligacion_pago top ON top.id_obligacion_pago = tc.id_obligacion_pago
                          INNER JOIN tes.tplan_pago tpp ON tpp.id_obligacion_pago = top.id_obligacion_pago and tpp.es_ultima_cuota = true
                          INNER JOIN adq.tsolicitud ts ON ts.id_solicitud = tpc.id_solicitud
                          INNER JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario
                          WHERE ttd.codigo = ''FORM500'' and tc.estado!=''anulado''  and tpp.estado_reg = ''activo'' AND tc.requiere_contrato = ''no'' and
                          tpp.es_ultima_cuota and ts.id_gestion = '||v_id_gestion||' and tdw.chequeado = '''||v_parametros.chequeado||''''||v_condicion||'


                          UNION ALL

                          SELECT
            				tc.id_cotizacion,
                            tc.id_proceso_wf,
                            tc.id_estado_wf,
                            tc.estado,
                            tc.num_tramite,
                            vf.desc_funcionario1::varchar AS fun_solicitante,
                            '''||v_nom_fun_resp||'''::varchar AS fun_resp,
                            CASE WHEN tdw.chequeado = ''si'' THEN ''TIENE FORM 500''::varchar ELSE ''NO TIENE EL FORM 500''::varchar END AS tieneform500,
                            (case when tc.fecha_adju is null then -1
                            when  adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tpp.fecha_conformidad,15),tc.id_cotizacion, '||p_id_usuario||',500, tdw.chequeado) between 0 and 15 then adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tpp.fecha_conformidad,15), tc.id_cotizacion, '||p_id_usuario||',500, tdw.chequeado)
                            else -2 end)::integer as dias_form_500,
                            ts.fecha_inicio,
                            ''CONTRATO''::varchar as tipo_doc,
                            CASE WHEN tpp.conformidad != '''' THEN ''TIENE CONFORMIDAD''::varchar ELSE ''NO TIENE CONFORMIDAD''::varchar END AS conformidad,
                            tpp.fecha_conformidad,
                            tpp.nro_cuota
            			  FROM adq.tcotizacion tc
                          INNER JOIN adq.tproceso_compra tpc ON tpc.id_proceso_compra = tc.id_proceso_compra
                          INNER JOIN wf.tdocumento_wf tdw ON tdw.id_proceso_wf = tc.id_proceso_wf
                          INNER JOIN wf.ttipo_documento ttd ON ttd.id_tipo_documento = tdw.id_tipo_documento
                          INNER JOIN tes.tobligacion_pago top ON top.id_obligacion_pago = tc.id_obligacion_pago
                          INNER JOIN tes.tplan_pago tpp ON tpp.id_obligacion_pago = top.id_obligacion_pago and tpp.es_ultima_cuota = true
                          INNER JOIN adq.tsolicitud ts ON ts.id_solicitud = tpc.id_solicitud
                          INNER JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario
                          INNER JOIN leg.tcontrato tleg on tleg.id_cotizacion = tc.id_cotizacion
                          WHERE ttd.codigo = ''FORM500'' AND tc.requiere_contrato = ''si'' and tleg.fecha_elaboracion is not null and
                          tpc.estado = ''proceso'' AND tc.estado <> ''anulado'' AND
                          tpp.es_ultima_cuota and ts.id_gestion = '||v_id_gestion||' and tdw.chequeado = '''||v_parametros.chequeado||''''||v_condicion||'
                        )



                        select
                          id_cotizacion,
                          id_proceso_wf,
                          id_estado_wf,
                          estado,
                          num_tramite,
                          fun_solicitante,
                          fun_resp,
                          tieneform500,
                          conformidad,
                          nro_cuota,
                          dias_form_500,
                          fecha_inicio,
                          fecha_conformidad,
                          tipo_doc
                        from formularios
                        where ';
            v_consulta=v_consulta||v_parametros.filtro||' order by dias_form_500 asc';
            raise notice 'consulta: %',v_consulta;

			--Devuelve la respuesta
			return v_consulta;
      END;
    /*********************************
 	#TRANSACCION:  'ADQ_FORM_500_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		FRANKLIN ESPINOZA A.
 	#FECHA:		18-08-2017
	***********************************/

	elsif(p_transaccion='ADQ_FORM_500_CONT')then

		BEGIN
        	SELECT g.id_gestion
            INTO v_id_gestion
            FROM param.tgestion g
            WHERE g.gestion = EXTRACT(YEAR FROM current_date);

            SELECT vfcl.desc_funcionario1
            INTO v_nom_fun_resp
            FROM segu.tusuario tu
            INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
            INNER JOIN orga.vfuncionario vfcl on vfcl.id_funcionario = tf.id_funcionario
            WHERE tu.id_usuario = p_id_usuario;

            select count(tur.id_rol)
            into v_contador
            from segu.tusuario_rol tur
            where tur.id_usuario = v_parametros.id_usuario and tur.id_rol = 1;

            if (p_administrador = 1 and v_contador > 0) then
            	v_condicion = '';
            else
            	v_condicion = ' and tpc.id_usuario_auxiliar = '||v_parametros.id_usuario;
            end if;

           --Sentencia de la consulta de conteo de registros
			v_consulta = '
            			with formularios as
                        (
            			  SELECT
            			  	tc.id_cotizacion,
                            tc.id_proceso_wf,
                            tc.id_estado_wf,
                            tc.estado,
                            tc.num_tramite,
                            vf.desc_funcionario1::varchar AS fun_solicitante,
                            '''||v_nom_fun_resp||'''::varchar AS fun_resp,
                            CASE WHEN tdw.chequeado = ''si'' THEN ''TIENE FORM 500''::varchar ELSE ''NO TIENE EL FORM 500''::varchar END AS tieneform500,
                            case when tc.fecha_adju is null then -1
                            when  adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tpp.fecha_conformidad,15),tc.id_cotizacion, '||p_id_usuario||',500, tdw.chequeado) between 0 and 15 then adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tpp.fecha_conformidad,15), tc.id_cotizacion, '||p_id_usuario||',500, tdw.chequeado)
                            else -2 end as dias_form_500,
                            ts.fecha_inicio,
                            ''ORDEN''::varchar as tipo_doc,
                            CASE WHEN tpp.conformidad != '''' THEN ''TIENE CONFORMIDAD''::varchar ELSE ''NO TIENE CONFORMIDAD''::varchar END AS conformidad,
                            tpp.fecha_conformidad,
                            tpp.nro_cuota
                          FROM adq.tcotizacion tc
                          INNER JOIN adq.tproceso_compra tpc ON tpc.id_proceso_compra = tc.id_proceso_compra
                          INNER JOIN wf.tdocumento_wf tdw ON tdw.id_proceso_wf = tc.id_proceso_wf
                          INNER JOIN wf.ttipo_documento ttd ON ttd.id_tipo_documento = tdw.id_tipo_documento
                          INNER JOIN tes.tobligacion_pago top ON top.id_obligacion_pago = tc.id_obligacion_pago
                          INNER JOIN tes.tplan_pago tpp ON tpp.id_obligacion_pago = top.id_obligacion_pago
                          INNER JOIN adq.tsolicitud ts ON ts.id_solicitud = tpc.id_solicitud
                          INNER JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario
                          WHERE ttd.codigo = ''FORM500'' and tc.estado!=''anulado''  and tpp.estado_reg = ''activo'' AND tc.requiere_contrato = ''no'' AND
                          tpp.es_ultima_cuota and ts.id_gestion = '||v_id_gestion||' and tdw.chequeado = '''||v_parametros.chequeado||''''||v_condicion||'


                          UNION ALL

                          SELECT
            				tc.id_cotizacion,
                            tc.id_proceso_wf,
                            tc.id_estado_wf,
                            tc.estado,
                            tc.num_tramite,
                            vf.desc_funcionario1::varchar AS fun_solicitante,
                            '''||v_nom_fun_resp||'''::varchar AS fun_resp,
                            CASE WHEN tdw.chequeado = ''si'' THEN ''TIENE FORM 500''::varchar ELSE ''NO TIENE EL FORM 500''::varchar END AS tieneform500,
                            (case when tc.fecha_adju is null then -1
                            when  adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tpp.fecha_conformidad,15),tc.id_cotizacion, '||p_id_usuario||',500, tdw.chequeado) between 0 and 15 then adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tpp.fecha_conformidad,15), tc.id_cotizacion, '||p_id_usuario||',500, tdw.chequeado)
                            else -2 end)::integer as dias_form_500,
                            ts.fecha_inicio,
                            ''CONTRATO''::varchar as tipo_doc,
                            CASE WHEN tpp.conformidad != '''' THEN ''TIENE CONFORMIDAD''::varchar ELSE ''NO TIENE CONFORMIDAD''::varchar END AS conformidad,
                            tpp.fecha_conformidad,
                            tpp.nro_cuota
            			  FROM adq.tcotizacion tc
                          INNER JOIN adq.tproceso_compra tpc ON tpc.id_proceso_compra = tc.id_proceso_compra
                          INNER JOIN wf.tdocumento_wf tdw ON tdw.id_proceso_wf = tc.id_proceso_wf
                          INNER JOIN wf.ttipo_documento ttd ON ttd.id_tipo_documento = tdw.id_tipo_documento
                          INNER JOIN tes.tobligacion_pago top ON top.id_obligacion_pago = tc.id_obligacion_pago
                          INNER JOIN tes.tplan_pago tpp ON tpp.id_obligacion_pago = top.id_obligacion_pago
                          INNER JOIN adq.tsolicitud ts ON ts.id_solicitud = tpc.id_solicitud
                          INNER JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario
                          INNER JOIN leg.tcontrato tleg on tleg.id_cotizacion = tc.id_cotizacion
                          WHERE ttd.codigo = ''FORM500'' AND tc.requiere_contrato = ''si'' and tleg.fecha_elaboracion is not null and
                          tpc.estado = ''proceso'' AND tc.estado <> ''anulado'' AND
                          tpp.es_ultima_cuota and ts.id_gestion = '||v_id_gestion||' and tdw.chequeado = '''||v_parametros.chequeado||''''||v_condicion||'
                        )

                        select count(id_cotizacion)
                        from formularios
            			where ';
            v_consulta=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;
		END;

    /*********************************
 	#TRANSACCION:   'ADQ_PEN_FORM400_REP'
 	#DESCRIPCION:	 Verifica los procesos que no tienen form 400 que estan dentro del plazo de vencimiento que son 15 dias.
 	#AUTOR:		Franklin Espinoza A.
 	#FECHA:		21-08-2017
	***********************************/

    elsif(p_transaccion='ADQ_PEN_FORM400_REP')then

      begin
        v_id_usuario = coalesce(v_parametros.id_usuario, p_id_usuario);

		SELECT tf.id_funcionario, vfcl.desc_funcionario1
        INTO v_rec_func
        FROM segu.tusuario tu
        INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
        INNER JOIN orga.vfuncionario vfcl on vfcl.id_funcionario = tf.id_funcionario
        WHERE tu.id_usuario = v_id_usuario;
        --Obtenemos la gestion
        SELECT g.id_gestion
        into v_id_gestion
        from param.tgestion g
        where g.gestion = EXTRACT(YEAR FROM current_date);

        /*v_resp = 'SELECT
            				tpc.id_usuario_auxiliar AS id_usuario,
                            tc.estado,
                            tc.num_tramite,
                            vf.desc_funcionario1::varchar AS fun_solicitante,
                            CASE WHEN tdw.chequeado = ''si'' THEN ''TIENE FORM 400''::varchar ELSE ''NO TIENE EL FORM 400''::varchar END AS tieneform400,
                            (ts.fecha_fin-now()::date)::integer AS dias_form_400,
                            ts.fecha_inicio,
                            ts.fecha_fin,
                            (''(''||td.nombre_corto ||'') - ''|| td.nombre)::varchar AS desc_depto,
                            '''||v_rec_func.desc_funcionario1||'''::varchar AS fun_responsable,
                            '''||pxp.f_get_variable_global('dias_form_400')||'''::varchar AS plazo_dias
            			  FROM adq.tcotizacion tc
                            INNER JOIN wf.tdocumento_wf tdw ON tdw.id_proceso_wf = tc.id_proceso_wf
                            INNER JOIN wf.ttipo_documento ttd ON ttd.id_tipo_documento = tdw.id_tipo_documento
                            INNER JOIN adq.tsolicitud ts ON ts.num_tramite = tc.num_tramite
                            INNER JOIN adq.tproceso_compra tpc ON tpc.num_tramite = tc.num_tramite
                            INNER JOIN param.tdepto td ON td.id_depto = tpc.id_depto
                            INNER JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario
                          WHERE tpc.id_usuario_auxiliar = '||v_id_usuario||' AND tdw.momento= ''exigir'' AND tdw.chequeado = ''no'' AND ttd.codigo = ''FORM400'' AND ts.id_gestion = '||v_id_gestion||' AND tc.estado<>''anulado'' ORDER BY dias_form_400 ASC ';*/

                         -- = '''||v_parametros.chequeado||'''
        v_resp = '
            			with formularios as
                        (
            			  SELECT
            				tc.id_cotizacion,
                            tc.id_proceso_wf,
                            tc.id_estado_wf,
                            tc.estado,
                            tc.num_tramite,
                            vf.desc_funcionario1::varchar AS fun_solicitante,
                            '''||v_nom_fun_resp||'''::varchar AS fun_resp,
                            CASE WHEN tdw.chequeado = ''si'' THEN ''TIENE FORM 400''::varchar ELSE ''NO TIENE EL FORM 400''::varchar END AS tieneform400,
                            case when tc.fecha_adju is null then -1 when  adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tc.fecha_adju,15),tc.id_cotizacion, '||p_id_usuario||',400) between 0 and 15 then adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tc.fecha_adju,15), tc.id_cotizacion, '||p_id_usuario||',400)
                            else -2 end as dias_form_400,
                            ts.fecha_inicio,
                            ''ORDEN''::varchar as tipo_doc,
                            tpc.id_usuario_auxiliar,
                            tpc.id_depto
            			  FROM adq.tcotizacion tc
                            INNER JOIN wf.tdocumento_wf tdw ON tdw.id_proceso_wf = tc.id_proceso_wf
                            INNER JOIN wf.ttipo_documento ttd ON ttd.id_tipo_documento = tdw.id_tipo_documento
                            INNER JOIN adq.tsolicitud ts ON ts.num_tramite = tc.num_tramite
                            INNER JOIN adq.tproceso_compra tpc ON tpc.num_tramite = tc.num_tramite
                            INNER JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario
                          WHERE ttd.codigo = ''FORM400'' AND
                          tpc.estado = ''proceso'' AND tc.estado <> ''anulado'' AND
                          ts.id_gestion = '||v_id_gestion||' AND tpc.id_usuario_auxiliar = '||v_parametros.id_usuario||' and '||
                          'tdw.chequeado in (''si'',''no'') and tc.fecha_adju is not null

                          union all

                          SELECT
            				tc.id_cotizacion,
                            tc.id_proceso_wf,
                            tc.id_estado_wf,
                            tc.estado,
                            tc.num_tramite,
                            vf.desc_funcionario1::varchar AS fun_solicitante,
                            '''||v_nom_fun_resp||'''::varchar AS fun_resp,
                            CASE WHEN tdw.chequeado = ''si'' THEN ''TIENE FORM 400''::varchar ELSE ''NO TIENE EL FORM 400''::varchar END AS tieneform400,
                            case when tleg.fecha_elaboracion is null then -1 when  adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tleg.fecha_elaboracion,15),tc.id_cotizacion, '||p_id_usuario||',400) between 0 and 15 then adq.f_verificar_dias_form45(CURRENT_DATE, param.f_sumar_dias_habiles(tleg.fecha_elaboracion,15), tc.id_cotizacion, '||p_id_usuario||',400)
                            else -2 end as dias_form_400,
                            ts.fecha_inicio,
                            ''CONTRATO''::varchar as tipo_doc,
                            tpc.id_usuario_auxiliar,
                            tpc.id_depto
            			  FROM adq.tcotizacion tc
                            INNER JOIN wf.tdocumento_wf tdw ON tdw.id_proceso_wf = tc.id_proceso_wf
                            INNER JOIN wf.ttipo_documento ttd ON ttd.id_tipo_documento = tdw.id_tipo_documento
                            INNER JOIN adq.tsolicitud ts ON ts.num_tramite = tc.num_tramite
                            INNER JOIN adq.tproceso_compra tpc ON tpc.num_tramite = tc.num_tramite
                            INNER JOIN leg.tcontrato tleg on tleg.id_cotizacion = tc.id_cotizacion
                            INNER JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario
                          WHERE ttd.codigo = ''FORM400'' AND tc.requiere_contrato = ''si'' and tleg.fecha_elaboracion is not null and
                          tpc.estado = ''proceso'' AND tc.estado <> ''anulado'' AND
                          ts.id_gestion = '||v_id_gestion||' AND tpc.id_usuario_auxiliar = '||v_parametros.id_usuario||' and '||
                          'tdw.chequeado in (''si'',''no'')
                        )


                        select
                          id_usuario_auxiliar as id_usuario,
                          estado,
                          num_tramite,
                          fun_solicitante,
                          tieneform400,
                          dias_form_400,
                          fecha_inicio,
                          (''(''||td.nombre_corto ||'') - ''|| td.nombre)::varchar AS desc_depto,
                          fun_responsable,
                          '''||pxp.f_get_variable_global('dias_form_400')||'''::varchar AS plazo_dias,
                          tipo_doc
                        from formularios
                        inner join  param.tdepto td ON td.id_depto = tpc.id_depto
                        where id_usuario_auxiliar is not null
                        order by dias_form_400
                          ';
		raise notice 'v_resp: %',v_resp;
        --Devuelve la respuesta
        return v_resp;

      end;

    /*********************************
 	#TRANSACCION:   'ADQ_PEN_FORM500_REP'
 	#DESCRIPCION:	 Verifica los procesos que no tienen form 500 que estan dentro del plazo de vencimiento que son 15 dias.
 	#AUTOR:		Franklin Espinoza A.
 	#FECHA:		21-08-2017
	***********************************/

    elsif(p_transaccion='ADQ_PEN_FORM500_REP')then

      begin

        v_id_usuario = coalesce(v_parametros.id_usuario, p_id_usuario);

        SELECT tf.id_funcionario, vfcl.desc_funcionario1
        INTO v_rec_func
        FROM segu.tusuario tu
        INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
        INNER JOIN orga.vfuncionario vfcl on vfcl.id_funcionario = tf.id_funcionario
        WHERE tu.id_usuario = p_id_usuario ;

        --Obtenemos la gestion
        SELECT g.id_gestion
        into v_id_gestion
        from param.tgestion g
        where g.gestion = EXTRACT(YEAR FROM current_date);

        v_resp = 'SELECT
            				tpc.id_usuario_auxiliar AS id_usuario,
                            tc.estado,
                            tc.num_tramite,
                            vf.desc_funcionario1::varchar AS fun_solicitante,
                            '''||v_rec_func.desc_funcionario1||'''::varchar AS fun_responsable,
                            (''(''||td.nombre_corto ||'') - ''|| td.nombre)::varchar AS desc_depto,
                            CASE WHEN tdw.chequeado = ''si'' THEN ''TIENE FORM 500''::varchar ELSE ''NO TIENE EL FORM 500''::varchar END AS tieneform500,
                            CASE WHEN tpp.conformidad<>'''' THEN ''TIENE CONFORMIDAD''::varchar ELSE ''NO TIENE CONFORMIDAD''::varchar END AS conformidad,
                            (ts.fecha_fin-now()::date)::integer AS dias_form_500,
                            ts.fecha_inicio,
                            ts.fecha_fin,
                            '''||pxp.f_get_variable_global('dias_form_500')||'''::varchar AS plazo_dias
            			  FROM adq.tcotizacion tc
                            INNER JOIN wf.tdocumento_wf tdw ON tdw.id_proceso_wf = tc.id_proceso_wf
                            INNER JOIN wf.ttipo_documento ttd ON ttd.id_tipo_documento = tdw.id_tipo_documento
                            INNER JOIN tes.tobligacion_pago top ON top.num_tramite = tc.num_tramite
                            INNER JOIN tes.tplan_pago tpp ON tpp.id_obligacion_pago = top.id_obligacion_pago
                            INNER JOIN adq.tsolicitud ts ON ts.num_tramite = tc.num_tramite
                            INNER JOIN adq.tproceso_compra tpc ON tpc.num_tramite = tc.num_tramite
                            INNER JOIN param.tdepto td ON td.id_depto = tpc.id_depto
                            INNER JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario
                          WHERE tpc.id_usuario_auxiliar = '||p_id_usuario||' AND tdw.momento= ''exigir'' AND tdw.chequeado = ''no'' AND ttd.codigo = ''FORM500'' AND ts.id_gestion = '||v_id_gestion||' AND tc.estado<>''anulado'' AND tpp.es_ultima_cuota ORDER BY dias_form_500 ASC ';

		raise notice 'v_resp: %',v_resp;
        --Devuelve la respuesta
        return v_resp;

      end;
    /*********************************
 	#TRANSACCION:   'ADQ_ALERT_FORM_4_5'
 	#DESCRIPCION:	 Verifica los procesos que tienen form 400 que estan dentro del plazo de vencimiento que es variable global form_dias_400.
 	#AUTOR:		Franklin Espinoza A.
 	#FECHA:		21-08-2017
	***********************************/

    elsif(p_transaccion='ADQ_ALERT_FORM_4_5')then

      begin

        SELECT tf.id_funcionario, vfcl.desc_funcionario1
        INTO v_rec_func
        FROM segu.tusuario tu
        INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
        INNER JOIN orga.vfuncionario vfcl on vfcl.id_funcionario = tf.id_funcionario
        WHERE tu.id_usuario = p_id_usuario ;

        --Obtenemos la gestion
        SELECT g.id_gestion
        into v_id_gestion
        from param.tgestion g
        where g.gestion = EXTRACT(YEAR FROM current_date);

        v_resp = 'SELECT
            				tpc.id_usuario_auxiliar AS id_usuario,
                            tc.estado,
                            tc.num_tramite,
                            vf.desc_funcionario1::varchar AS fun_solicitante,
                            CASE WHEN tdw.chequeado = ''si'' THEN ''TIENE FORM 400''::varchar ELSE ''NO TIENE EL FORM 400''::varchar END AS tieneform400,
                            (ts.fecha_fin-now()::date)::integer AS dias_form_400,
                            ts.fecha_inicio,
                            ts.fecha_fin,
                            (''(''||td.nombre_corto ||'') - ''|| td.nombre)::varchar AS desc_depto,
                            '''||v_rec_func.desc_funcionario1||'''::varchar AS fun_responsable ,
                            '''||pxp.f_get_variable_global('dias_form_400')||'''::varchar AS plazo_dias
            			  FROM adq.tcotizacion tc
                            INNER JOIN wf.tdocumento_wf tdw ON tdw.id_proceso_wf = tc.id_proceso_wf
                            INNER JOIN wf.ttipo_documento ttd ON ttd.id_tipo_documento = tdw.id_tipo_documento
                            INNER JOIN adq.tsolicitud ts ON ts.num_tramite = tc.num_tramite
                            INNER JOIN adq.tproceso_compra tpc ON tpc.num_tramite = tc.num_tramite
                            INNER JOIN param.tdepto td ON td.id_depto = tpc.id_depto
                            INNER JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario
                          WHERE tpc.id_usuario_auxiliar = '||p_id_usuario||' AND tdw.momento= ''exigir'' AND tdw.chequeado = ''no'' AND ttd.codigo = ''FORM400'' AND ts.id_gestion = '||v_id_gestion||' ORDER BY dias_form_400 ASC ';--select 75 AS id_usuario


        --Devuelve la respuesta
        raise notice 'v_resp: %', v_resp;
        return v_resp;

      end;
    /*********************************
 	#TRANSACCION:   'ADQ_ALERT_FORM_5'
 	#DESCRIPCION:	 Verifica los procesos que no tienen form 500 que estan dentro del plazo de vencimiento que es variable global form_dias_500.
 	#AUTOR:		Franklin Espinoza A.
 	#FECHA:		21-08-2017
	***********************************/

    elsif(p_transaccion='ADQ_ALERT_FORM_5')then

      begin

        SELECT tf.id_funcionario, vfcl.desc_funcionario1
        INTO v_rec_func
        FROM segu.tusuario tu
        INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
        INNER JOIN orga.vfuncionario vfcl on vfcl.id_funcionario = tf.id_funcionario
        WHERE tu.id_usuario = p_id_usuario ;

        --Obtenemos la gestion
        SELECT g.id_gestion
        into v_id_gestion
        from param.tgestion g
        where g.gestion = EXTRACT(YEAR FROM current_date);

        v_resp = 'SELECT
            				tpc.id_usuario_auxiliar AS id_usuario,
                            tc.estado,
                            tc.num_tramite,
                            vf.desc_funcionario1::varchar AS fun_solicitante,
                            '''||v_rec_func.desc_funcionario1||'''::varchar AS fun_responsable,
                            (''(''||td.nombre_corto ||'') - ''|| td.nombre)::varchar AS desc_depto,
                            CASE WHEN tdw.chequeado = ''si'' THEN ''TIENE FORM 500''::varchar ELSE ''NO TIENE EL FORM 500''::varchar END AS tieneform500,
                            CASE WHEN tpp.conformidad<>'''' THEN ''TIENE CONFORMIDAD''::varchar ELSE ''NO TIENE CONFORMIDAD''::varchar END AS conformidad,
                            (ts.fecha_fin-now()::date)::integer AS dias_form_500,
                            ts.fecha_inicio,
                            ts.fecha_fin,
                            '''||pxp.f_get_variable_global('dias_form_500')||'''::varchar AS plazo_dias
            			  FROM adq.tcotizacion tc
                            INNER JOIN wf.tdocumento_wf tdw ON tdw.id_proceso_wf = tc.id_proceso_wf
                            INNER JOIN wf.ttipo_documento ttd ON ttd.id_tipo_documento = tdw.id_tipo_documento
                            INNER JOIN tes.tobligacion_pago top ON top.num_tramite = tc.num_tramite
                            INNER JOIN tes.tplan_pago tpp ON tpp.id_obligacion_pago = top.id_obligacion_pago
                            INNER JOIN adq.tsolicitud ts ON ts.num_tramite = tc.num_tramite
                            INNER JOIN adq.tproceso_compra tpc ON tpc.num_tramite = tc.num_tramite
                            INNER JOIN param.tdepto td ON td.id_depto = tpc.id_depto
                            INNER JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario
                          WHERE tpc.id_usuario_auxiliar = '||p_id_usuario||' AND tdw.momento= ''exigir'' AND tdw.chequeado = ''no'' AND ttd.codigo = ''FORM500'' AND ts.id_gestion = '||v_id_gestion||' AND tc.estado<>''anulado'' AND tpp.es_ultima_cuota ORDER BY dias_form_500 ASC ';

		raise notice 'v_resp: %',v_resp;
        --Devuelve la respuesta
        return v_resp;

      end;

	else

		raise exception 'Transaccion inexistente';

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