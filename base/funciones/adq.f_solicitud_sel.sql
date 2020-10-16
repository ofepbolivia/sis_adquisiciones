CREATE OR REPLACE FUNCTION adq.f_solicitud_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Adquisiciones
 FUNCION: 		adq.f_solicitud_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'adq.tsolicitud'
 AUTOR: 		 (admin)
 FECHA:	        19-02-2013 12:12:51
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
    v_filtro varchar;
    v_id_estados		record;
    v_resultado 		record;
	v_cotizaciones		record;
    v_obligaciones		record;


    v_historico        varchar;
    v_inner            varchar;
    v_strg_sol         varchar;
    v_strg_obs         varchar;
    v_exception_detail   varchar;
    v_exception_context    varchar;

    --variables para reporte MEMORANDUM RCP
    v_comision 			varchar[];
    v_tam				INTEGER;
    v_valores 			varchar = '';
    v_ids_comision		varchar;
    cont				integer;
    v_fecha_po			date;
    v_valor				varchar;
    v_nro_memo			varchar;
    v_correlativo		varchar;

    --select revertir presupuesto
    v_solicitud_partida record;
    v_verificar	record;
    v_auxiliar_id		integer;
    v_fecha_sol			date;
    v_proces_wf			integer;
    v_cont				integer;
    v_cont_1			integer;
	v_fecha				date;
    v_nro_tramite		varchar;
    v_fecha_sol_material date;
    v_id_estado_wf		integer;

   v_id_uo				integer;
   /*Aumentando Varibales para el reporte Solicitud de compra (Ismael Valdivia 13/10/2020)*/
   v_nombre_funcionario_aprobador varchar;
   v_cargo_aprobador	varchar;
   v_estado_tramite		varchar;
   v_fecha_aprobador	varchar;
   v_nombre_funcionario_rpc		varchar;
   v_cargo_rpc			varchar;
   v_fecha_rpc			varchar;

   v_nombre_funcionario_jefatura_adq varchar;
   v_cargo_jefatura_adq				 varchar;
   v_fecha_jefatura_adq				 varchar;
   v_codigo_rpc			varchar;
   /***************************************************************************************/
BEGIN

	v_nombre_funcion = 'adq.f_solicitud_sel';
    v_parametros = pxp.f_get_record(p_tabla);



	/*********************************
 	#TRANSACCION:  'ADQ_SOL_SEL'
 	#DESCRIPCION:	Consulta de datos
    #DESCRIPCION_TEC:	 TIENE QUE DEVOLVER LAS MISMAS COLUMNAS QUE ADQ_HISSOL_SEL
 	#AUTOR:		Rensi Arteaga Copari
 	#FECHA:		19-02-2013 12:12:51
    ***********************************/

	if(p_transaccion='ADQ_SOL_SEL')then

    	begin
          --si es administrador



            v_filtro='';
            if (v_parametros.id_funcionario_usu is null) then
              	v_parametros.id_funcionario_usu = -1;
            end if;

            IF  pxp.f_existe_parametro(p_tabla,'historico') THEN

             v_historico =  v_parametros.historico;

            ELSE

            v_historico = 'no';

            END IF;

            IF p_administrador !=1  and lower(v_parametros.tipo_interfaz) = 'solicitudreq' THEN

              v_filtro = '(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||'  or sol.id_usuario_reg='||p_id_usuario||' or sol.id_funcionario = '||v_parametros.id_funcionario_usu::varchar||') and ';


            END IF;

          IF  lower(v_parametros.tipo_interfaz) in ('solicitudvb','solicitudvbwzd','solicitudvbpoa','solicitudvbpresupuestos') THEN

                IF v_historico =  'no' THEN
                    IF p_administrador !=1 THEN
                      v_filtro = ' (ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' ) and  (lower(sol.estado)!=''borrador'') and (lower(sol.estado)!=''proceso'' ) and ';
                    ELSE
                        v_filtro = ' (lower(sol.estado)!=''borrador''  and lower(sol.estado)!=''proceso'' and lower(sol.estado)!=''finalizado'') and ';
                    END IF;
                ELSE
                    IF p_administrador !=1 THEN
                      if lower(v_parametros.tipo_interfaz) = 'solicitudvbpoa' then
                          v_auxiliar_id = 75;
                          v_filtro = ' (ew.id_funcionario='||v_auxiliar_id||') and  (lower(sol.estado)!=''borrador'') and ';
                      else
                          v_filtro = ' (ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||') and  (lower(sol.estado)!=''borrador'') and ';
                      end if;
                      --v_filtro = ' (ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' ) and  (lower(sol.estado)!=''borrador'') and ';
                    ELSE
                        v_filtro = ' (lower(sol.estado) != ''borrador'') and ';
                    END IF;
               END IF;


         END IF;

         --la interface de vbpresupuestos mustra todas las solcitudes no importa el funcionario asignado
         IF  lower(v_parametros.tipo_interfaz) = 'solicitudvbpresupuestos' and v_historico =  'no' THEN
             v_filtro = v_filtro||' (lower(sol.estado)=''vbpresupuestos'' ) and ';
         END IF;

         --la interface de vbpresupuestos mustra todas las solcitudes no importa el funcionario asignado
         IF  lower(v_parametros.tipo_interfaz) = 'solicitudvbpoa' and v_historico =  'no'  THEN
             v_filtro = ' (lower(sol.estado)=''vbpoa'' ) and ';
         END IF;



            IF  lower(v_parametros.tipo_interfaz) = 'solicitudvbasistente' THEN


                v_filtro = ' (ew.id_funcionario  IN (select * FROM orga.f_get_funcionarios_x_usuario_asistente(now()::date,'||p_id_usuario||') AS (id_funcionario INTEGER))) and ';
                IF v_historico =  'no' THEN
                	v_filtro = v_filtro || ' lower(sol.estado)=''vbgerencia'' and ';
                END IF;
            END IF;




            IF v_historico =  'si' THEN

               v_inner =  'inner join wf.testado_wf ew on ew.id_proceso_wf = sol.id_proceso_wf';
               v_strg_sol = 'DISTINCT(sol.id_solicitud)';
               v_strg_obs = '''---''::text';

               IF p_administrador = 1 THEN
					v_filtro = ' (lower(sol.estado)!=''borrador'' ) and ';
			   END IF;

            ELSE

               v_inner =  'inner join wf.testado_wf ew on ew.id_estado_wf = sol.id_estado_wf';
               v_strg_sol = 'sol.id_solicitud';
               v_strg_obs = 'ew.obs';

             END IF;


			IF  lower(v_parametros.tipo_interfaz) in ('solicitudhistorico') THEN
 			    v_filtro = ' ';
            END IF;



            --(31-01-2020) MAY nuevo filtro consulta solicitudes para cada gerencia
            IF  (lower(v_parametros.tipo_interfaz) in ('solicitudhistoricoxgerencia')) THEN

 			    --busca id_uo del usuario
                  SELECT uo.id_uo
                  INTO v_id_uo
                  FROM orga.tfuncionario f
                  inner join segu.tusuario usu on usu.id_persona = f.id_persona
                  inner join orga.tuo_funcionario func on func.id_funcionario = f.id_funcionario and func.estado_reg = 'activo'
                  inner join orga.tuo uo on uo.estado_reg='activo' and uo.id_uo = tes.f_get_uo_gerencia_proceso(func.id_uo,null::integer,null::date)
                  WHERE usu.id_usuario= p_id_usuario;

                  IF v_historico =  'si' THEN
                     v_strg_sol = 'DISTINCT(sol.id_solicitud)';
                     v_strg_obs = '''---''::text';
                  ELSE
                     v_strg_sol = 'sol.id_solicitud';
                     v_strg_obs = 'ew.obs';
                  END IF;

            --raise exception  'llega %', v_id_uo;
             v_inner = '   inner join wf.testado_wf ew on ew.id_proceso_wf = sol.id_proceso_wf
                           inner JOIN orga.tuo_funcionario uof on uof.id_funcionario = sol.id_funcionario and uof.tipo = ''oficial'' and uof.estado_reg = ''activo'' and (current_date <= uof.fecha_finalizacion or  uof.fecha_finalizacion is null)
			               inner JOIN orga.tuo tuo on tuo.id_uo = tes.f_get_uo_gerencia_proceso(uof.id_uo,null::integer,null::date)  ';

             v_filtro = ' tuo.id_uo = '||v_id_uo||'  and  ';



            END IF;


    		--Sentencia de la consulta
			v_consulta:='select
						'||v_strg_sol||',
						sol.estado_reg,
						sol.id_solicitud_ext,
						sol.presu_revertido,
						sol.fecha_apro,
						sol.estado,
						sol.id_funcionario_aprobador,
						sol.id_moneda,
						sol.id_gestion,
						sol.tipo,
						sol.num_tramite,
						sol.justificacion,
						sol.id_depto,
						sol.lugar_entrega,
						sol.extendida,
					    sol.posibles_proveedores,
						sol.id_proceso_wf,
						sol.comite_calificacion,
						sol.id_categoria_compra,
						sol.id_funcionario,
						sol.id_estado_wf,
						sol.fecha_soli,
						sol.fecha_reg,
						sol.id_usuario_reg,
						sol.fecha_mod,
						sol.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        sol.id_uo,
						fun.desc_funcionario1 as desc_funcionario,
                        funa.desc_funcionario1 as desc_funcionario_apro,
                        uo.codigo||''-''||uo.nombre_unidad as desc_uo,
                        ges.gestion as desc_gestion,
                        mon.codigo as desc_moneda,
						dep.codigo as desc_depto,
                        pm.nombre as desc_proceso_macro,
                        cat.nombre as desc_categoria_compra,
                        sol.id_proceso_macro,
                        sol.numero,
                        funrpc.desc_funcionario1 as desc_funcionario_rpc,
                        '||v_strg_obs||',
                        sol.instruc_rpc,
                        pro.desc_proveedor,
                        sol.id_proveedor,
                        sol.id_funcionario_supervisor,
                        funs.desc_funcionario1 as desc_funcionario_supervisor,
                        sol.ai_habilitado,
                        sol.id_cargo_rpc,
                        sol.id_cargo_rpc_ai,
                        sol.tipo_concepto,
                        sol.revisado_asistente,
                        sol.fecha_inicio,
                        sol.dias_plazo_entrega,
                        sol.obs_presupuestos,
                        sol.precontrato,
                        sol.update_enable,
                        sol.codigo_poa,
                        sol.obs_poa,
                        (select count(*)
                             from unnest(id_tipo_estado_wfs) elemento
                             where elemento = ew.id_tipo_estado) as contador_estados,
						            sol.nro_po,
                        sol.fecha_po,
                        sum(tsd.precio_total) as importe_total,
                        tcat.codigo as prioridad,
                        tcat.id_catalogo as id_prioridad,
                        sol.list_proceso,
                        sol.cuce,
                        sol.fecha_conclusion,
                        sol.presupuesto_aprobado,
                        tcat2.descripcion as tipo_modalidad

						from adq.tsolicitud sol
						inner join segu.tusuario usu1 on usu1.id_usuario = sol.id_usuario_reg
                        inner join wf.tproceso_wf pwf on pwf.id_proceso_wf = sol.id_proceso_wf
                        inner join orga.vfuncionario fun on fun.id_funcionario = sol.id_funcionario
                        inner join orga.tuo uo on uo.id_uo = sol.id_uo
                        inner join param.tmoneda mon on mon.id_moneda = sol.id_moneda
                        inner join param.tgestion ges on ges.id_gestion = sol.id_gestion
                        inner join param.tdepto dep on dep.id_depto = sol.id_depto
                        inner join wf.tproceso_macro pm on pm.id_proceso_macro = sol.id_proceso_macro
                        inner join adq.tcategoria_compra cat on cat.id_categoria_compra = sol.id_categoria_compra

                        left join orga.vfuncionario funrpc on funrpc.id_funcionario = sol.id_funcionario_rpc
                        inner join orga.vfuncionario funa on funa.id_funcionario = sol.id_funcionario_aprobador
                        left join orga.vfuncionario funs on funs.id_funcionario = sol.id_funcionario_supervisor

						left join segu.tusuario usu2 on usu2.id_usuario = sol.id_usuario_mod
                        left join param.vproveedor pro on pro.id_proveedor = sol.id_proveedor

                        left join adq.tsolicitud_det tsd on tsd.id_solicitud = sol.id_solicitud and tsd.estado_reg = ''activo''

                        left join param.tcatalogo tcat on tcat.id_catalogo = sol.prioridad
                         left join param.tcatalogo tcat2 on tcat2.codigo = sol.tipo_modalidad
                        '||v_inner||'
                        where  sol.estado_reg = ''activo'' and '||v_filtro;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' group by sol.id_solicitud, usu1.cuenta, usu2.cuenta,
             fun.desc_funcionario1, funa.desc_funcionario1, uo.codigo, uo.nombre_unidad,
             ges.gestion, mon.codigo, dep.codigo, pm.nombre, cat.nombre, funrpc.desc_funcionario1,
             ew.obs, pro.desc_proveedor, funs.desc_funcionario1, ew.id_tipo_estado,
             pwf.id_tipo_estado_wfs, tcat.codigo, tcat.id_catalogo, tcat2.descripcion order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;



   /*********************************
 	#TRANSACCION:  'ADQ_SOL_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		Rensi Arteaga Copari
 	#FECHA:		19-02-2013 12:12:51
	***********************************/

	elsif(p_transaccion='ADQ_SOL_CONT')then

		begin

            v_filtro='';
            if (v_parametros.id_funcionario_usu is null) then
              	v_parametros.id_funcionario_usu = -1;
            end if;

            IF  pxp.f_existe_parametro(p_tabla,'historico') THEN

             v_historico =  v_parametros.historico;

            ELSE

            v_historico = 'no';

            END IF;

            IF p_administrador !=1  and lower(v_parametros.tipo_interfaz) = 'solicitudreq' THEN

              v_filtro = '(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||'  or sol.id_usuario_reg='||p_id_usuario||' or sol.id_funcionario = '||v_parametros.id_funcionario_usu::varchar||') and ';


            END IF;

          IF  lower(v_parametros.tipo_interfaz) in ('solicitudvb','solicitudvbwzd','solicitudvbpoa','solicitudvbpresupuestos') THEN

                IF v_historico =  'no' THEN
                    IF p_administrador !=1 THEN
                      v_filtro = ' (ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' ) and  (lower(sol.estado)!=''borrador'') and (lower(sol.estado)!=''proceso'' ) and ';
                    ELSE
                        v_filtro = ' (lower(sol.estado)!=''borrador''  and lower(sol.estado)!=''proceso'' and lower(sol.estado)!=''finalizado'') and ';
                    END IF;
                ELSE
                    IF p_administrador !=1 THEN
                      if lower(v_parametros.tipo_interfaz) = 'solicitudvbpoa' then
                          v_auxiliar_id = 75;
                          v_filtro = ' (ew.id_funcionario='||v_auxiliar_id||') and  (lower(sol.estado)!=''borrador'') and ';
                      else
                          v_filtro = ' (ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||') and  (lower(sol.estado)!=''borrador'') and ';
                      end if;
                      --v_filtro = ' (ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' ) and  (lower(sol.estado)!=''borrador'') and ';
                    ELSE
                        v_filtro = ' (lower(sol.estado) != ''borrador'') and ';
                    END IF;
               END IF;


         END IF;

         --la interface de vbpresupuestos mustra todas las solcitudes no importa el funcionario asignado
         IF  lower(v_parametros.tipo_interfaz) = 'solicitudvbpresupuestos' and v_historico =  'no' THEN
             v_filtro = v_filtro||' (lower(sol.estado)=''vbpresupuestos'' ) and ';
         END IF;

         --la interface de vbpresupuestos mustra todas las solcitudes no importa el funcionario asignado
         IF  lower(v_parametros.tipo_interfaz) = 'solicitudvbpoa' and v_historico =  'no'  THEN
             v_filtro = ' (lower(sol.estado)=''vbpoa'' ) and ';
         END IF;



            IF  lower(v_parametros.tipo_interfaz) = 'solicitudvbasistente' THEN


                v_filtro = ' (ew.id_funcionario  IN (select * FROM orga.f_get_funcionarios_x_usuario_asistente(now()::date,'||p_id_usuario||') AS (id_funcionario INTEGER))) and ';
                IF v_historico =  'no' THEN
                	v_filtro = v_filtro || ' lower(sol.estado)=''vbgerencia'' and ';
                END IF;
            END IF;




            IF v_historico =  'si' THEN

               v_inner =  'inner join wf.testado_wf ew on ew.id_proceso_wf = sol.id_proceso_wf';
               v_strg_sol = 'DISTINCT(sol.id_solicitud)';
               v_strg_obs = '''---''::text';

               IF p_administrador = 1 THEN
					v_filtro = ' (lower(sol.estado)!=''borrador'' ) and ';
			   END IF;

            ELSE

               v_inner =  'inner join wf.testado_wf ew on ew.id_estado_wf = sol.id_estado_wf';
               v_strg_sol = 'sol.id_solicitud';
               v_strg_obs = 'ew.obs';

             END IF;


			IF  lower(v_parametros.tipo_interfaz) in ('solicitudhistorico') THEN
 			    v_filtro = ' ';
            END IF;


             --(31-01-2020) MAY nuevo filtro consulta solicitudes para cada gerencia
            IF  (lower(v_parametros.tipo_interfaz) in ('solicitudhistoricoxgerencia')) THEN

 			    --busca id_uo del usuario
                  SELECT uo.id_uo
                  INTO v_id_uo
                  FROM orga.tfuncionario f
                  inner join segu.tusuario usu on usu.id_persona = f.id_persona
                  inner join orga.tuo_funcionario func on func.id_funcionario = f.id_funcionario and func.estado_reg = 'activo'
                  inner join orga.tuo uo on uo.estado_reg='activo' and uo.id_uo = tes.f_get_uo_gerencia_proceso(func.id_uo,null::integer,null::date)
                  WHERE usu.id_usuario= p_id_usuario;

                  IF v_historico =  'si' THEN
                     v_strg_sol = 'DISTINCT(sol.id_solicitud)';
                     v_strg_obs = '''---''::text';
                  ELSE
                     v_strg_sol = 'sol.id_solicitud';
                     v_strg_obs = 'ew.obs';
                  END IF;

            --raise exception  'llega %', v_id_uo;
             v_inner = '   inner join wf.testado_wf ew on ew.id_proceso_wf = sol.id_proceso_wf
                           inner JOIN orga.tuo_funcionario uof on uof.id_funcionario = sol.id_funcionario and uof.tipo = ''oficial'' and uof.estado_reg = ''activo'' and (current_date <= uof.fecha_finalizacion or  uof.fecha_finalizacion is null)
			               inner JOIN orga.tuo tuo on tuo.id_uo = tes.f_get_uo_gerencia_proceso(uof.id_uo,null::integer,null::date)  ';

             v_filtro = ' tuo.id_uo = '||v_id_uo||'  and  ';



            END IF;
            --


			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count('||v_strg_sol||')
			            from adq.tsolicitud sol
						inner join segu.tusuario usu1 on usu1.id_usuario = sol.id_usuario_reg
                        inner join wf.tproceso_wf pwf on pwf.id_proceso_wf = sol.id_proceso_wf
                        inner join orga.vfuncionario fun on fun.id_funcionario = sol.id_funcionario
                        inner join orga.tuo uo on uo.id_uo = sol.id_uo
                        inner join param.tmoneda mon on mon.id_moneda = sol.id_moneda
                        inner join param.tgestion ges on ges.id_gestion = sol.id_gestion
                        inner join param.tdepto dep on dep.id_depto = sol.id_depto
                        inner join wf.tproceso_macro pm on pm.id_proceso_macro = sol.id_proceso_macro
                        inner join adq.tcategoria_compra cat on cat.id_categoria_compra = sol.id_categoria_compra

                        left join orga.vfuncionario funrpc on funrpc.id_funcionario = sol.id_funcionario_rpc
                        inner join orga.vfuncionario funa on funa.id_funcionario = sol.id_funcionario_aprobador
                        left join orga.vfuncionario funs on funs.id_funcionario = sol.id_funcionario_supervisor

						left join segu.tusuario usu2 on usu2.id_usuario = sol.id_usuario_mod
                        left join param.vproveedor pro on pro.id_proveedor = sol.id_proveedor

                        left join adq.tsolicitud_det tsd on tsd.id_solicitud = sol.id_solicitud and tsd.estado_reg = ''activo''

                        left join param.tcatalogo tcat on tcat.id_catalogo = sol.prioridad
                         left join param.tcatalogo tcat2 on tcat2.codigo = sol.tipo_modalidad
                        '||v_inner||'
                        where  sol.estado_reg = ''activo'' and '||v_filtro;


			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;


			--Devuelve la respuesta
			return v_consulta;

		end;


     /*********************************
 	#TRANSACCION:  'ADQ_SOLREP_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		13-03-2013 12:12:51
	***********************************/

	elsif(p_transaccion='ADQ_SOLREP_SEL')then

    begin

            IF  pxp.f_existe_parametro(p_tabla,'id_solicitud') THEN

                  v_filtro = 'sol.id_solicitud='||v_parametros.id_solicitud||' and ';

                    select
                    sol.id_proceso_wf
                    into v_proces_wf
                    from adq.tsolicitud sol
                    where sol.id_solicitud = v_parametros.id_solicitud;

               		select sol.num_tramite,
                          /*Recuperando el estado para la condicion de la firma*/
                     	   sol.estado
                          /****************************************************/
                     into v_nro_tramite,
                          v_estado_tramite
                    from adq.tsolicitud sol
                    where sol.id_solicitud = v_parametros.id_solicitud;

            ELSE
                  v_filtro = 'sol.id_proceso_wf='||v_parametros.id_proceso_wf||' and ';

                  v_proces_wf = v_parametros.id_proceso_wf;

               		select sol.num_tramite,
                          /*Recuperando el estado para la condicion de la firma*/
                     	   sol.estado
                          /****************************************************/
                     into v_nro_tramite,
                          v_estado_tramite
                    from adq.tsolicitud sol
                    where sol.id_proceso_wf = v_parametros.id_proceso_wf;

            END IF;

			if (substr(v_nro_tramite, 1, 2) in ('GM', 'GO', 'GA', 'GC')) then

            	select sol.fecha_solicitud
                	into v_fecha_sol_material
                from mat.tsolicitud sol
                where sol.nro_tramite = v_nro_tramite;

            else

                select es.id_estado_wf
                	into v_id_estado_wf
                from wf.testado_wf es
                where es.fecha_reg = (
                select
                     max(ewf.fecha_reg)
                   FROM  wf.testado_wf ewf
                   INNER JOIN  wf.ttipo_estado te on ewf.id_tipo_estado = te.id_tipo_estado
                   LEFT JOIN   segu.tusuario usu on usu.id_usuario = ewf.id_usuario_reg
                   LEFT JOIN  orga.vfuncionario fun on fun.id_funcionario = ewf.id_funcionario
                   LEFT JOIN  param.tdepto depto on depto.id_depto = ewf.id_depto
                   WHERE
                    ewf.id_proceso_wf = v_proces_wf
                    and te.codigo = 'borrador'
                    and te.etapa = 'Solicitante');

              select
                     ew.fecha_reg::date
                     into v_fecha_sol
                   FROM  wf.testado_wf ew
                   where ew.id_estado_anterior = v_id_estado_wf;

          	end if;

            /*Aqui Recuperamos la firma del aprobador cuando pase su estado (Ismael Valdivia 13/10/2020)*/
            if (v_estado_tramite != 'vbaprobador' and v_estado_tramite != 'vbgerencia' and v_estado_tramite != 'borrador' and v_estado_tramite != 'vbuti') then
            		SELECT 	vf.desc_funcionario1,
                    		vf.nombre_cargo,
                            max(twf.fecha_reg::date)::varchar
                    INTO
                            v_nombre_funcionario_aprobador,
                            v_cargo_aprobador,
                            v_fecha_aprobador
                    FROM wf.testado_wf twf
                        INNER JOIN wf.ttipo_estado te ON te.id_tipo_estado = twf.id_tipo_estado
                        INNER JOIN wf.tproceso_wf pro ON twf.id_proceso_wf = pro.id_proceso_wf
                        INNER JOIN orga.vfuncionario_cargo vf ON vf.id_funcionario = twf.id_funcionario
                        WHERE twf.id_proceso_wf = v_proces_wf AND (te.codigo = 'vbaprobador' or te.codigo = 'vbgerencia') and ( vf.fecha_finalizacion is null or vf.fecha_finalizacion >= now())
                        GROUP BY twf.id_funcionario, vf.desc_funcionario1,te.codigo,vf.nombre_cargo,pro.nro_tramite;
    		end if;

            if (v_nombre_funcionario_aprobador is null) then
            	v_nombre_funcionario_aprobador = '';
            end if;

            if (v_cargo_aprobador is null) then
            	v_cargo_aprobador = '';
            end if;

            if (v_fecha_aprobador is null) then
            	v_fecha_aprobador = '';
            end if;
            /********************************************************************************************/


            /*Aqui Recuperamos la firma del RPC cuando pase su estado (Ismael Valdivia 13/10/2020)*/
            if (v_estado_tramite != 'vbrpc' and v_estado_tramite != 'vbrpa' and v_estado_tramite != 'vbpresupuestos' and v_estado_tramite != 'suppresu'
            	and v_estado_tramite != 'vbpoa' and v_estado_tramite != 'vbaprobador' and v_estado_tramite != 'vbgerencia'
                and v_estado_tramite != 'vbuti' and v_estado_tramite != 'borrador' and v_estado_tramite != 'vbactif'
                and v_estado_tramite != 'pendiente') then
            		SELECT 	vf.desc_funcionario1,
                    		vf.nombre_cargo,
                            max(twf.fecha_reg::date),
                            te.codigo
                    INTO
                            v_nombre_funcionario_rpc,
                            v_cargo_rpc,
                            v_fecha_rpc,
                            v_codigo_rpc
                    FROM wf.testado_wf twf
                        INNER JOIN wf.ttipo_estado te ON te.id_tipo_estado = twf.id_tipo_estado
                        INNER JOIN wf.tproceso_wf pro ON twf.id_proceso_wf = pro.id_proceso_wf
                        INNER JOIN orga.vfuncionario_cargo vf ON vf.id_funcionario = twf.id_funcionario
                        WHERE twf.id_proceso_wf = v_proces_wf AND (te.codigo = 'vbrpa' or te.codigo = 'vbrpc') and ( vf.fecha_finalizacion is null or vf.fecha_finalizacion >= now())
                        GROUP BY twf.id_funcionario, vf.desc_funcionario1,te.codigo,vf.nombre_cargo,pro.nro_tramite;
    		end if;

            if (v_nombre_funcionario_rpc is null) then
            	v_nombre_funcionario_rpc = '';
            end if;

            if (v_cargo_rpc is null) then
            	v_cargo_rpc = '';
            end if;

            if (v_codigo_rpc is null) then
            	v_codigo_rpc = '';
            end if;
            /********************************************************************************************/

            /*Aqui Recuperamos la firma del RPC cuando pase su estado (Ismael Valdivia 13/10/2020)*/
            if (v_estado_tramite != 'aprobado' and v_estado_tramite != 'vbrpc' and v_estado_tramite != 'vbrpa' and v_estado_tramite != 'vbpresupuestos' and v_estado_tramite != 'suppresu'
            	and v_estado_tramite != 'vbpoa' and v_estado_tramite != 'vbaprobador' and v_estado_tramite != 'vbgerencia'
                and v_estado_tramite != 'vbuti' and v_estado_tramite != 'borrador' and v_estado_tramite != 'vbactif'
                and v_estado_tramite != 'pendiente') then
                    SELECT 	vf.desc_funcionario1,
                            vf.nombre_cargo,
                            max(twf.fecha_reg::date)
                    INTO
                            v_nombre_funcionario_jefatura_adq,
                            v_cargo_jefatura_adq,
                            v_fecha_jefatura_adq
                    FROM wf.testado_wf twf
                        INNER JOIN wf.ttipo_estado te ON te.id_tipo_estado = twf.id_tipo_estado
                        INNER JOIN wf.tproceso_wf pro ON twf.id_proceso_wf = pro.id_proceso_wf
                        INNER JOIN orga.vfuncionario_cargo vf ON vf.id_funcionario = twf.id_funcionario
                    WHERE twf.id_proceso_wf = v_proces_wf AND te.codigo = 'aprobado' and ( vf.fecha_finalizacion is null or vf.fecha_finalizacion >= now())
                    GROUP BY twf.id_funcionario, vf.desc_funcionario1,te.codigo,vf.nombre_cargo,pro.nro_tramite,te.id_tipo_estado;
			end if;

            if (v_nombre_funcionario_jefatura_adq is null) then
            	v_nombre_funcionario_jefatura_adq = '';
            end if;

            if (v_cargo_jefatura_adq is null) then
            	v_cargo_jefatura_adq = '';
            end if;
            /********************************************************************************************/

            --Sentencia de la consulta
			v_consulta:='select
						sol.id_solicitud,
						sol.estado_reg,
						sol.id_solicitud_ext,
						sol.presu_revertido,
						sol.fecha_apro,
						sol.estado,
						sol.id_funcionario_aprobador,
						sol.id_moneda,
						sol.id_gestion,
						sol.tipo,
						sol.num_tramite,
						sol.justificacion,
						sol.id_depto,
						sol.lugar_entrega,
						sol.extendida,

						sol.posibles_proveedores,
						sol.id_proceso_wf,
						sol.comite_calificacion,
						sol.id_categoria_compra,
						sol.id_funcionario,
						sol.id_estado_wf,
                        sol.fecha_soli,
						'''||coalesce(v_fecha_sol,now())||'''::date as fecha_soli_gant,
                        '''||coalesce(v_fecha_sol_material,now())||'''::date as fecha_soli_material,
						sol.fecha_reg,
						sol.id_usuario_reg,
						sol.fecha_mod,
						sol.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        sol.id_uo,
						fun.desc_funcionario1 as desc_funcionario,

                        funa.desc_funcionario1 as desc_funcionario_apro,
                        uo.codigo||''-''||uo.nombre_unidad as desc_uo,
                        ges.gestion as desc_gestion,
                        mon.codigo as desc_moneda,
						dep.codigo as desc_depto,
                        pm.nombre as desc_proceso_macro,
                        cat.nombre as desc_categoria_compra,
                        sol.id_proceso_macro,
                        sol.numero,
                        funrpc.desc_funcionario1 as desc_funcionario_rpc,
                        COALESCE(sol.usuario_ai,'''')::varchar as nombre_usuario_ai,
                        uo.codigo as codigo_uo,
						fca.descripcion_cargo::varchar as cargo_desc_funcionario,
                        fcap.descripcion_cargo::varchar as cargo_desc_funcionario_apro,
                        sol.prioridad,
                        frpc.descripcion_cargo::varchar as cargo_desc_funcionario_rpc,
                        dep.prioridad as dep_prioridad,


                        /*Aumentando Para la firma del aprobador (Ismael Valdivia 13/10/2020)*/
                        '''||v_nombre_funcionario_aprobador||'''::varchar as funcionario_aprobador,
                        '''||v_cargo_aprobador||'''::varchar as cargo_aprobador,
                        '''||v_fecha_aprobador||'''::varchar as fecha_aprobador,

                        '''||v_nombre_funcionario_rpc||'''::varchar as funcionario_rpc,
                        '''||v_cargo_rpc||'''::varchar as cargo_rpc,
                        '''||v_codigo_rpc||'''::varchar as codigo_rpc,

                        '''||v_nombre_funcionario_jefatura_adq||'''::varchar as funcionario_jefatura_adq,
                        '''||v_cargo_jefatura_adq||'''::varchar as cargo_jefatura_adq,
                        cat.codigo::varchar as codigo_adquisicion,

                        (SELECT count(det.id_solicitud)
                        from adq.tsolicitud_det det
                        where det.id_solicitud = sol.id_solicitud
                        group by det.id_solicitud)::varchar as total_detalle,
                        /****************************************/

                        /*Aumentando el tipo de la modalidad (Ismael Valdivia 13/10/2020)*/
                        (select cata.descripcion
                        from param.tcatalogo cata
                        inner join param.tcatalogo_tipo catip on catip.id_catalogo_tipo = cata.id_catalogo_tipo
                        where catip.nombre = ''tmatriz_modalidad'' and cata.codigo = sol.tipo_modalidad)::varchar as tipo_modalidad,
                        /*****************************************************************/

                        /*Aumnetando el tipo de solicitud (Ismael Valdivia 14/10/2020)*/
                        (select cata.descripcion
                        from param.tcatalogo cata
                        inner join param.tcatalogo_tipo catip on catip.id_catalogo_tipo = cata.id_catalogo_tipo
                        where catip.nombre = ''codigo_tipo_concepto'' and cata.codigo = sol.tipo_concepto)::varchar as tipo_solicitud,

                        sol.tipo_concepto
                        /**************************************************************/

						from adq.tsolicitud sol
						inner join segu.tusuario usu1 on usu1.id_usuario = sol.id_usuario_reg

                        inner join orga.vfuncionario fun on fun.id_funcionario = sol.id_funcionario
                        inner join orga.tuo uo on uo.id_uo = sol.id_uo
                        inner join param.tmoneda mon on mon.id_moneda = sol.id_moneda
                        inner join param.tgestion ges on ges.id_gestion = sol.id_gestion
                        inner join param.tdepto dep on dep.id_depto = sol.id_depto
                        inner join wf.tproceso_macro pm on pm.id_proceso_macro = sol.id_proceso_macro
                        inner join adq.tcategoria_compra cat on cat.id_categoria_compra = sol.id_categoria_compra

                        left join orga.vfuncionario funrpc on funrpc.id_funcionario = sol.id_funcionario_rpc
                        inner join orga.vfuncionario funa on funa.id_funcionario = sol.id_funcionario_aprobador

						left join segu.tusuario usu2 on usu2.id_usuario = sol.id_usuario_mod

                        inner join wf.testado_wf ew on ew.id_estado_wf = sol.id_estado_wf
                    	inner join orga.vfuncionario_ultimo_cargo fca on fca.id_funcionario = fun.id_funcionario
					    inner join orga.vfuncionario_ultimo_cargo fcap on fcap.id_funcionario = sol.id_funcionario_aprobador
                        left join orga.vfuncionario_ultimo_cargo frpc on frpc.id_funcionario = sol.id_funcionario_rpc

				        where '||v_filtro;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            raise notice '%', v_consulta;

			--Devuelve la respuesta
			return v_consulta;
    end;


   	/*********************************
 	#TRANSACCION:  'ADQ_ESTSOL_SEL'
 	#DESCRIPCION:	Consulta estado de solicitud
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		02-06-2013
	***********************************/

	elsif(p_transaccion='ADQ_ESTSOL_SEL')then
    begin
		select sol.id_estado_wf as ult_est_sol into v_id_estados
        from adq.tsolicitud sol
        left join adq.tproceso_compra pc on pc.id_solicitud=sol.id_solicitud
        where sol.id_solicitud=v_parametros.id_solicitud;

        create temp table flujo_solicitud(
        	funcionario text,
            nombre text,
            nombre_estado varchar,
            fecha_reg date,
            id_tipo_estado int4,
            id_estado_wf int4,
            id_estado_anterior int4
        ) on commit drop;

		--recupera el flujo de control de las solicitudes y del proceso de compra
		INSERT INTO flujo_solicitud(
        WITH RECURSIVE estados_solicitud_proceso(id_funcionario, id_proceso_wf, id_tipo_estado,id_estado_wf, id_estado_anterior, fecha_reg)AS(
     		SELECT et.id_funcionario, et.id_proceso_wf, et.id_tipo_estado, et.id_estado_wf, et.id_estado_anterior, et.fecha_reg
    		FROM wf.testado_wf et
     		WHERE et.id_estado_wf  in (v_id_estados.ult_est_sol)
     	UNION ALL
      		SELECT et.id_funcionario, et.id_proceso_wf, et.id_tipo_estado, et.id_estado_wf, et.id_estado_anterior, et.fecha_reg
        	FROM wf.testado_wf et, estados_solicitud_proceso
      		WHERE et.id_estado_wf=estados_solicitud_proceso.id_estado_anterior
     	)SELECT perf.nombre_completo1 as funcionario, tp.nombre,te.nombre_estado, es.fecha_reg,es.id_tipo_estado, es.id_estado_wf, COALESCE(es.id_estado_anterior,NULL) as id_estado_anterior
         FROM estados_solicitud_proceso es
         INNER JOIN wf.tproceso_wf pwf on pwf.id_proceso_wf=es.id_proceso_wf
         INNER JOIN wf.ttipo_proceso tp on tp.id_tipo_proceso=pwf.id_tipo_proceso
         INNER JOIN wf.ttipo_estado te on te.id_tipo_estado=es.id_tipo_estado
         INNER JOIN orga.tfuncionario fun on fun.id_funcionario=es.id_funcionario
         INNER JOIN segu.vpersona perf on perf.id_persona=fun.id_persona
         order by id_estado_wf);

        --Definicion de la respuesta
        v_consulta:='select * from flujo_solicitud';

        --Devuelve la respuesta
        return v_consulta;

	end;

    /*********************************
 	#TRANSACCION:  'ADQ_SOLOC_REP'
 	#DESCRIPCION:	Reporte Pre Orden Compra
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		22-09-2014
	***********************************/
	elsif(p_transaccion='ADQ_SOLOC_REP')then
    	begin
		v_consulta:='select pv.desc_proveedor,
                     per.id_persona,
                     per.direccion as dir_persona,
                     per.telefono1 as telf1_persona,
                     per.telefono2 as telf2_persona,
                     per.celular1 as cel_persona,
                     per.correo as correo_persona,
                     ins.id_institucion,
                     ins.direccion as dir_institucion,
                     ins.telefono1 as telf1_institucion,
                     ins.telefono2 as telf2_institucion,
                     ins.celular1 as cel_institucion,
                     ins.email1 as email_institucion,
                     ins.fax as fax_institucion,
                     sol.lugar_entrega,
                     sol.tipo,
                     mon.moneda,
                     mon.codigo as codigo_moneda,
                     sol.num_tramite
                	 from adq.tsolicitud sol
                   	 inner join param.vproveedor pv on pv.id_proveedor = sol.id_proveedor
                   	 left join segu.tpersona per on per.id_persona = pv.id_persona
                   	 left join param.tinstitucion ins on ins.id_institucion = pv.id_institucion
                   	 inner join param.tmoneda mon on mon.id_moneda = sol.id_moneda
                	 where sol.id_solicitud ='||v_parametros.id_solicitud||' and ';

          --Definicion de la respuesta
          v_consulta:=v_consulta||v_parametros.filtro;
          v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

          --Devuelve la respuesta
          return v_consulta;
        end;

	/*********************************
 	#TRANSACCION:  'ADQ_ESTPROC_SEL'
 	#DESCRIPCION:	Consulta estado de procesos
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		31-05-2013
	***********************************/

	elsif(p_transaccion='ADQ_ESTPROC_SEL')then
    begin

        create temp table flujo_proceso(
        	funcionario text,
            nombre text,
            nombre_estado varchar,
            fecha_reg date,
            id_tipo_estado int4,
            id_estado_wf int4,
            id_estado_anterior int4
        ) on commit drop;

		--recupera el flujo de control de las solicitudes y del proceso de compra
		INSERT INTO flujo_proceso(
        WITH RECURSIVE estados_solicitud_proceso(id_funcionario, id_proceso_wf, id_tipo_estado,id_estado_wf, id_estado_anterior, fecha_reg)AS(
     		SELECT et.id_funcionario, et.id_proceso_wf, et.id_tipo_estado, et.id_estado_wf, et.id_estado_anterior, et.fecha_reg
    		FROM wf.testado_wf et
     		WHERE et.id_estado_wf  in (
            			select pc.id_estado_wf as ult_est_prc
				        from adq.tsolicitud sol
        				left join adq.tproceso_compra pc on pc.id_solicitud=sol.id_solicitud
        				where pc.id_proceso_compra=v_parametros.id_proceso_compra)
     	UNION ALL
      		SELECT et.id_funcionario, et.id_proceso_wf, et.id_tipo_estado, et.id_estado_wf, et.id_estado_anterior, et.fecha_reg
        	FROM wf.testado_wf et, estados_solicitud_proceso
      		WHERE et.id_estado_wf=estados_solicitud_proceso.id_estado_anterior
     	)SELECT perf.nombre_completo1 as funcionario, tp.nombre,te.nombre_estado, es.fecha_reg,es.id_tipo_estado, es.id_estado_wf, COALESCE(es.id_estado_anterior,NULL) as id_estado_anterior
         FROM estados_solicitud_proceso es
         INNER JOIN wf.tproceso_wf pwf on pwf.id_proceso_wf=es.id_proceso_wf
         INNER JOIN wf.ttipo_proceso tp on tp.id_tipo_proceso=pwf.id_tipo_proceso
         INNER JOIN wf.ttipo_estado te on te.id_tipo_estado=es.id_tipo_estado
         LEFT JOIN orga.tfuncionario fun on fun.id_funcionario=es.id_funcionario
         LEFT JOIN segu.vpersona perf on perf.id_persona=fun.id_persona
         order by id_estado_wf);

        --Definicion de la respuesta
        v_consulta:='select * from flujo_proceso';

        --Devuelve la respuesta
        return v_consulta;

	end;
    /*********************************
 	#TRANSACCION:  'ADQ_CERT_SEL'
 	#DESCRIPCION:	Cetificado Poa
 	#AUTOR:		MAM
 	#FECHA:		02-06-2013
	***********************************/
    elsif(p_transaccion='ADQ_CERT_SEL')then
    	begin
        v_consulta:='select
                            ad.id_solicitud,
                            ad.num_tramite,
                            to_char(ad.fecha_soli,''DD/MM/YYYY'')as fecha_soli,
                            ad.justificacion,
                            ad.codigo_poa,
                            (SELECT  pxp.list(ob.codigo|| '' ''||ob.descripcion||'' '')
                                from pre.tobjetivo ob
                                where ob.codigo = ANY (string_to_array(ad.codigo_poa,'',''))) as codigo_descripcion,
                            ge.gestion
                            from adq.tsolicitud ad
                            inner join param.tgestion ge on ge.id_gestion = ad.id_gestion
                            where ad.codigo_poa IS NOT NULL and ad.id_proceso_wf='||v_parametros.id_proceso_wf;
        --Devuelve la respuesta
          return v_consulta;
        end;
    /*********************************
 	#TRANSACCION:  'ADQ_RMEMOCOMDCR_SEL'
 	#DESCRIPCION:	Obtenemos datos para Reporte Memorandum RPC
 	#AUTOR:		FEA
 	#FECHA:		17-04-2017
	***********************************/
    ELSIF (p_transaccion='ADQ_RMEMOCOMDCR_SEL')THEN
    	BEGIN

            SELECT COALESCE(ts.comite_calificacion::text,''::text), COALESCE(ts.fecha_po, now()::date), ts.nro_cite_rpc
            INTO v_ids_comision, v_fecha_po, v_nro_memo
            FROM adq.tsolicitud ts
            WHERE ts.id_proceso_wf = v_parametros.id_proceso_wf;

       	    v_comision = string_to_array(v_ids_comision,',')::varchar[];
            v_tam = array_length(v_comision,1);

            if (v_tam>0)then
            	for cont in 1..v_tam loop
                    select vf.desc_funcionario1::varchar
                    into  v_valor
                    from adq.tcomision  com
                    inner join orga.vfuncionario vf on vf.id_funcionario = com.id_funcionario
                    where com.id_integrante = v_comision[cont]::integer;
                    if (cont < v_tam) then
                    	v_valores = v_valores || v_valor || ',';
                    else
                    	v_valores = v_valores || v_valor;
                    end if;
                end loop;
			      end if;

            if(v_nro_memo is not null)then
           		v_correlativo = v_nro_memo;
            else
           		SELECT param.f_obtener_correlativo(
                                              'MEM',
                                               ts.id_gestion,
                                               NULL,
                                               ts.id_depto,
                                               ts.id_usuario_reg,
                                               'ADQ',
                                               NULL)
           		INTO v_correlativo
        		  FROM  adq.tsolicitud ts
            	WHERE  ts.id_proceso_wf = v_parametros.id_proceso_wf;

              UPDATE adq.tsolicitud SET
              nro_cite_rpc = v_correlativo
              WHERE id_proceso_wf = v_parametros.id_proceso_wf;
            end if;


        	--Sentencia de la consulta
            v_consulta:='SELECT
            vf.desc_funcionario1 as funcionario,
            p.desc_proveedor as proveedor,'''||
            v_correlativo||'''::varchar as tramite,'''||
        	v_valores||'''::varchar as nombres,'''||
            to_char(v_fecha_po, 'DD-MM-YYYY')||'''::varchar as fecha_po
            FROM  adq.tsolicitud ts
            LEFT JOIN orga.vfuncionario vf ON vf.id_funcionario = ts.id_funcionario_rpc
            LEFT JOIN param.vproveedor p ON p.id_proveedor = ts.id_proveedor
            WHERE  ts.id_proceso_wf = '||v_parametros.id_proceso_wf;


			--Devuelve la respuesta
          RAISE NOTICE 'CONSULTA: %',v_consulta;
			    return v_consulta;
      END;
    /*********************************
 	#TRANSACCION:  'ADQ_COMEJEPAG_SEL'
 	#DESCRIPCION:	Listado Solicitudes + Comprometido Ejecutado Pagado
 	#AUTOR:		Franklin Espinoza Alvarez
 	#FECHA:		27-10-2017 16:01:32
	***********************************/

	elsif(p_transaccion='ADQ_COMEJEPAG_SEL')then
        begin
    		--Sentencia de la consulta
            create temp table obligaciones(
                    id_solicitud_det 	integer,
                    id_partida			integer,
                    nombre_partida		text,
                    id_concepto_ingas	integer,
                    nombre_ingas			text,
                    id_solicitud	integer,
                    id_centro_costo		integer,
                    codigo_cc			text,
                    id_partida_ejecucion	integer,
                    descripcion			text,
                    comprometido		numeric DEFAULT 0.00,
                    ejecutado			numeric DEFAULT 0.00,
                    pagado				numeric DEFAULT 0.00,
                    revertible			numeric DEFAULT 0.00,
                    revertir			numeric
            ) on commit drop;

            insert into obligaciones (id_solicitud_det,
                                      id_partida,
                                      nombre_partida,
            						  id_concepto_ingas,
                                      nombre_ingas,
                                      id_solicitud,
                                      id_centro_costo,
                                      codigo_cc,
                                      id_partida_ejecucion,
                                      descripcion)
            select
                sold.id_solicitud_det,
                sold.id_partida,
                par.nombre_partida||'-('||par.codigo||')' as nombre_partida,
                sold.id_concepto_ingas,
                cig.desc_ingas||'-('||cig.movimiento||')' as nombre_ingas,
                sold.id_solicitud,
                sold.id_centro_costo,
                cc.codigo_cc,
                sold.id_partida_ejecucion,
                sold.descripcion
           from adq.tsolicitud_det sold
                inner join param.vcentro_costo cc on cc.id_centro_costo=sold.id_centro_costo
                inner join segu.tusuario usu1 on usu1.id_usuario = sold.id_usuario_reg
                inner join pre.tpartida par on par.id_partida=sold.id_partida
                inner join param.tconcepto_ingas cig on cig.id_concepto_ingas=sold.id_concepto_ingas
            where sold.id_solicitud=v_parametros.id_solicitud;

            select  obli.id_solicitud_det,
                obli.id_partida,
                obli.nombre_partida,
                obli.id_concepto_ingas,
                obli.nombre_ingas,
                obli.id_solicitud,
                obli.id_centro_costo,
                obli.codigo_cc,
                obli.id_partida_ejecucion,
                obli.descripcion
            into v_verificar
            from obligaciones obli
            where obli.id_solicitud=v_parametros.id_solicitud;

			FOR v_solicitud_partida in (select * from obligaciones)LOOP
            	v_verificar = pre.f_verificar_com_eje_pag(v_solicitud_partida.id_partida_ejecucion,v_parametros.id_moneda);

            	update obligaciones set
                comprometido = COALESCE(v_verificar.ps_comprometido,0.00::numeric),
                ejecutado = COALESCE(v_verificar.ps_ejecutado,0.00::numeric),
                pagado = COALESCE(v_verificar.ps_pagado,0.00::numeric),
                revertible =  COALESCE(v_verificar.ps_comprometido,0.00::numeric) - COALESCE(v_verificar.ps_ejecutado,0.00::numeric)
                where obligaciones.id_solicitud_det=v_solicitud_partida.id_solicitud_det;

        	END LOOP;

              v_consulta:='select * from obligaciones where  ';

              --Definicion de la respuesta
              v_consulta:=v_consulta||v_parametros.filtro;
              v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion;


			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'ADQ_GET_DAT_SOL_SEL'
 	#DESCRIPCION:	Datos de Solicitud de Compra
 	#AUTOR:		F.E.A
 	#FECHA:		02-06-2018
	***********************************/
    elsif(p_transaccion='ADQ_GET_DAT_SOL_SEL')then
    	begin
        v_consulta:='select
                            ts.id_solicitud,
                            ts.num_tramite,
                            ts.id_depto,
                            ts.instruc_rpc
                            from adq.tsolicitud ts
                            where '||v_parametros.filtro;
        --Devuelve la respuesta
          return v_consulta;
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

ALTER FUNCTION adq.f_solicitud_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
