CREATE OR REPLACE FUNCTION adq.ft_solicitud_modalidad (
  v_id_solicitud integer,
  v_id_usuario integer
)
RETURNS SETOF record AS
$body$
/**************************************************************************
 SISTEMA:		Adquisiciones
 FUNCION: 		adq.ft_solicitud_modalidad
 DESCRIPCION:   verifica solicitud modalidad
 AUTOR: 		maylee.perez
 FECHA:	        14-10-2020
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE
  	v_resp		           	varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
    v_consulta				text;

    v_solicitud_det			record;
    v_id_matriz_modalidad	integer;
    v_modalidades_matriz	record;
    v_id_modalidad_solicitud integer;

    v_desc_ingas			varchar;
    v_total_det				numeric;
    v_codigo_modalidad		varchar;

     --v_id_funcionario_sol	integer;
     v_id_uo  				integer;
     v_solicitud			record;
     va_id_funcionario_gerente  	INTEGER[];
     v_idfun_modalidad		integer;
     v_desc_funcionario		varchar;
     v_nombre_cargo			varchar;
     v_count_concepto_ingas	numeric;
     v_id_matriz_mod		record;
     v_modalidad_solicitud	record;
     v_modalidades_solicitud record;
	 v_modalidad			varchar;
     v_count_modalidad		numeric;
     --v_id_funcionario_aprobador	integer;
     v_solicitud_modalidad	varchar;
     v_solu_modalidades		record;
     v_respuesta_modalidad	varchar;
     v_depto_prioridad		integer;
     v_nom_tipo_contratacion	varchar;
     v_nombre_modalidad		varchar;

     v_funcionario			integer;
     v_id_matriz			record;
     v_id_uo_matriz  		integer;
     v_id_uo_sol			integer;
     v_funcionario_sol		integer;
     v_matriz_id_modalidad	integer;
     v_nom_uo				varchar;

     v_proceso_contratacion	varchar;
     v_count_codigo_modalidad	integer;



BEGIN




  	v_nombre_funcion = 'adq.f_solicitud_modalidad';


      --informacion de la solicitud
      SELECT sol.fecha_soli, sol.id_funcionario, sol.prioridad, sol.id_uo, sol.contratacion_directa
      into v_solicitud
      FROM adq.tsolicitud sol
      WHERE sol.id_solicitud = v_id_solicitud;
     --raise exception 'llega %', v_solicitud.id_uo;

      --prioridad segun el depto del proceso
      SELECT depto.prioridad
      into v_depto_prioridad
      FROM param.tdepto depto
      join adq.tsolicitud sol on sol.id_depto = depto.id_depto
      WHERE sol.id_solicitud = v_id_solicitud;

      --nombre unidad
      SELECT uo.nombre_unidad
      into v_nom_uo
      FROM orga.tuo uo
      WHERE uo.id_uo = v_solicitud.id_uo;


       --RECORRE PARA VERIFICAR EL DETALLLE DE LA SOLICITUD con cada concepto de gasto a que modalidad corresponde o no

      FOR v_solicitud_det in(SELECT sd.id_concepto_ingas
                              FROM adq.tsolicitud_det sd
                              WHERE sd.id_solicitud = v_id_solicitud
                              GROUP BY sd.id_concepto_ingas
                             )LOOP

                            --nombre del concepto de gasto
                            SELECT cin.desc_ingas
                            into v_desc_ingas
                            FROM param.tconcepto_ingas cin
                            WHERE cin.id_concepto_ingas = v_solicitud_det.id_concepto_ingas;

                           --para diferenciar de las regionales y de la central que si pueden elegir una de ellas
                           IF (v_depto_prioridad = 1) THEN


                                    FOR v_id_matriz IN(SELECT mc.id_matriz_modalidad, mm.id_uo
                                                                FROM adq.tmatriz_concepto mc
                                                                left join adq.tmatriz_modalidad mm on mm.id_matriz_modalidad = mc.id_matriz_modalidad
                                                                WHERE mc.id_concepto_ingas = v_solicitud_det.id_concepto_ingas
                                                                and mc.estado_reg = 'activo'
                                                                and mm.id_uo != 10094
                                                         		 )LOOP

                                            --RAISE EXCEPTION 'MATRIZ % - %',v_id_matriz.id_matriz_modalidad, v_id_matriz.id_uo;
                                                SELECT fc.id_funcionario
                                                into v_funcionario
                                                FROM orga.vfuncionario_cargo fc
                                                WHERE (fc.id_uo = v_id_matriz.id_uo
                                                or fc.id_uo =  v_solicitud.id_uo )
                                                and fc.fecha_asignacion  <=  now()
                         						and (fc.fecha_finalizacion is null or fc.fecha_finalizacion >= now() );


                                                SELECT fc.id_funcionario
                                                into v_funcionario_sol
                                                FROM orga.vfuncionario_cargo fc
                                                WHERE fc.id_uo = v_solicitud.id_uo
                                                and fc.fecha_asignacion  <=  now()
                         						and (fc.fecha_finalizacion is null or fc.fecha_finalizacion >= now() );

                                    END LOOP;
                                    --RAISE EXCEPTION 'MATRIZ % - %',v_funcionario, v_funcionario_sol;
                                     -- recupera la uo gerencia del funcionario
                                    v_id_uo_matriz =   orga.f_get_uo_gerencia_area_ope(NULL, v_funcionario, v_solicitud.fecha_soli::Date);

                                    v_id_uo_sol =   orga.f_get_uo_gerencia_area_ope(NULL, v_funcionario_sol, v_solicitud.fecha_soli::Date);




                                    IF (v_id_uo_matriz = v_id_uo_sol ) THEN

                                    --RAISE EXCEPTION 'MATRIZ1 % - %', v_id_uo_matriz,v_solicitud.id_uo;

                                        SELECT mc.id_matriz_modalidad
                                        INTO v_matriz_id_modalidad
                                        FROM adq.tmatriz_concepto mc
                                        left join adq.tmatriz_modalidad mm on mm.id_matriz_modalidad = mc.id_matriz_modalidad
                                        WHERE mc.id_concepto_ingas = v_solicitud_det.id_concepto_ingas
                                        and mc.estado_reg = 'activo'
                                        AND mm.id_uo_gerencia = v_id_uo_sol ;


                                        /*--control para que no tenga mas de un concepto de gasto
                                        SELECT count(mc.id_concepto_ingas)
                                        into v_count_concepto_ingas
                                        FROM adq.tmatriz_concepto mc
                                        left join adq.tmatriz_modalidad mm on mm.id_matriz_modalidad = mc.id_matriz_modalidad
                                        WHERE mc.id_concepto_ingas = v_solicitud_det.id_concepto_ingas
                                        and mc.estado_reg = 'activo'
                                        and mm.id_uo_gerencia != 10094
                                        and  mm.id_uo_gerencia = v_id_uo_sol; --nombre de parametrizacion en la matriz Gerencias Regionales


                                        IF (v_count_concepto_ingas > 1) THEN
                                          RAISE EXCEPTION 'En la Matriz Tipo Contratación(Aprobador) existe mas de un Concepto de Gasto % en un agrupador. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre).', v_desc_ingas;
                                        END IF;*/

                                          --and (mm.id_uo = v_solicitud.id_uo or mm.id_uo = v_id_uo_sol);

                                         --RAISE EXCEPTION 'MATRIZ %',v_matriz_id_modalidad;

                                        v_id_matriz_modalidad =   v_matriz_id_modalidad;


                                    ELSE

                                         RAISE EXCEPTION 'No se encuentra parametrizado el Concepto de Gasto % en la Matriz Tipo Contratación(Aprobador) para la %. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre).', v_desc_ingas, v_nom_uo;

                                    END IF;

                                    ---



                                    IF (v_id_matriz_modalidad is null) THEN
                                        RAISE EXCEPTION 'No se encuentra parametrizado el Concepto de Gasto % en la Matriz Tipo Contratación(Aprobador). Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre).', v_desc_ingas;
                                    END IF;



                                   --verificar si ya existe en agrupador de la matriz(cabecera)en esta tabla  si existe q ya no registre

                                   IF NOT EXISTS( SELECT 1
                                                   FROM adq.tmodalidad_solicitud ms
                                                   WHERE ms.id_matriz_modalidad =  v_id_matriz_modalidad
                                                   and ms.id_solicitud = v_id_solicitud
                                                  ) THEN

                          			--raise exception 'kkea21 % - % - %', v_solicitud_det.id_concepto_ingas,v_id_matriz_modalidad, v_id_solicitud ;
                                         --insertanto tabla
                                         insert into adq.tmodalidad_solicitud (   id_concepto_ingas,
                                                                                  id_solicitud,
                                                                                  id_matriz_modalidad,

                                                                                  id_usuario_reg,
                                                                                  fecha_reg

                                                                               )values(
                                                                                  v_solicitud_det.id_concepto_ingas,
                                                                                  v_id_solicitud,
                                                                                  v_id_matriz_modalidad,

                                                                                  v_id_usuario,
                                                                                  now()

                                                                               );





                                   	END IF;
                                   --

                          ELSE
                                  --prioridad 2 para regionales nacionales

                                  /*--control para que no tenga mas de un concepto de gasto
                                  SELECT count(mc.id_concepto_ingas)
                                  into v_count_concepto_ingas
                                  FROM adq.tmatriz_concepto mc
                                  left join adq.tmatriz_modalidad mm on mm.id_matriz_modalidad = mc.id_matriz_modalidad
                                  WHERE mc.id_concepto_ingas = v_solicitud_det.id_concepto_ingas
                                  and mc.estado_reg = 'activo'
                                  and mm.id_uo in (10094, v_solicitud.id_uo); --nombre de parametrizacion en la matriz Gerencias Regionales

                                  IF (v_count_concepto_ingas > 1) THEN
                                    RAISE EXCEPTION 'En la Matriz Tipo Contratación(Aprobador) existe mas de un Concepto de Gasto % en un agrupador. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre).', v_desc_ingas;
                                  END IF; */

                                  --
                                  SELECT mc.id_matriz_modalidad
                                  into v_id_matriz_modalidad
                                  FROM adq.tmatriz_concepto mc
                                  left join adq.tmatriz_modalidad mm on mm.id_matriz_modalidad = mc.id_matriz_modalidad
                                  WHERE mc.id_concepto_ingas = v_solicitud_det.id_concepto_ingas
                                  and mc.estado_reg = 'activo'
                                  and mm.id_uo in (10094, v_solicitud.id_uo) ; --nombre de parametrizacion en la matriz Gerencias Regionales



                                  IF (v_id_matriz_modalidad is null) THEN
                                      RAISE EXCEPTION 'No se encuentra parametrizado el Concepto de Gasto % para una Regional, en la Matriz Tipo Contratación(Aprobador). Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre).', v_desc_ingas;
                                  END IF;



                                 --verificar si ya existe en agrupador de la matriz(cabecera)en esta tabla  si existe q ya no registre

                                 IF NOT EXISTS( SELECT 1
                                                 FROM adq.tmodalidad_solicitud ms
                                                 WHERE ms.id_matriz_modalidad =  v_id_matriz_modalidad
                                                 and ms.id_solicitud = v_id_solicitud
                                                ) THEN

                        			--raise exception 'kkea21 % - % - %', v_solicitud_det.id_concepto_ingas,v_id_matriz_modalidad, v_id_solicitud ;
                                       --insertanto tabla
                                       insert into adq.tmodalidad_solicitud (   id_concepto_ingas,
                                                                                id_solicitud,
                                                                                id_matriz_modalidad,

                                                                                id_usuario_reg,
                                                                                fecha_reg

                                                                             )values(
                                                                                v_solicitud_det.id_concepto_ingas,
                                                                                v_id_solicitud,
                                                                                v_id_matriz_modalidad,

                                                                                v_id_usuario,
                                                                                now()

                                                                             );





                                 END IF;
                                 --

						  END IF;



      END LOOP;


      --- obtener el precio_total de la solicitud
      SELECT sum(sd.precio_total)
      into v_total_det
      FROM adq.tsolicitud_det sd
      WHERE sd.id_solicitud = v_id_solicitud
      and sd.estado_reg = 'activo' ;





      ----
      FOR v_id_matriz_mod in( SELECT ms.id_matriz_modalidad
                                    FROM adq.tmodalidad_solicitud ms
                                    WHERE ms.id_solicitud = v_id_solicitud
                                    and ms.estado_reg = 'activo'
                                  )LOOP

      		--informacion para sacar de la matriz_modalidad
            SELECT mm.modalidad_menor,
            	   mm.modalidad_anpe,
                   mm.modalidad_directa,
                   mm.flujo_mod_directa,
                   mm.modalidad_licitacion,
                   mm.modalidad_desastres,
                   mm.modalidad_excepcion,
                   mm.id_uo,
                   mm.id_cargo,
                   mm.resp_proc_contratacion_menor,
                   mm.resp_proc_contratacion_anpe,
                   mm.resp_proc_contratacion_directa,
                   mm.resp_proc_contratacion_licitacion,
                   mm.resp_proc_contratacion_desastres,
                   mm.resp_proc_contratacion_excepcion

            into v_modalidades_matriz
            FROM  adq.tmatriz_modalidad mm
            WHERE mm.id_matriz_modalidad = v_id_matriz_mod.id_matriz_modalidad
            and mm.estado_reg = 'activo';

			--
            IF (v_modalidades_matriz.modalidad_directa = 'si' and v_modalidades_matriz.flujo_mod_directa is null) THEN
            	raise exception 'No existe un Flujo parametrizado para la Contratación Directa';
            END IF;

            ---
            --informacion para sacar el funcionario aprobador

            --PARA LOS TIPO DE UNIDADES DE GERENCIAS REGIONALES
             IF ( v_modalidades_matriz.id_uo =  10094) THEN

                -- PARA VER QUE SI SON GERENCIAS REGIONALES SI EL APROBADOR VA SER GERENTE GENERAL O EL GERENTE DE LA REGIONAL
             	IF (v_modalidades_matriz.id_cargo = 18594  or v_modalidades_matriz.id_cargo IS NULL) THEN

                        -- recupera la uo gerencia del funcionario
                        v_id_uo =   orga.f_get_uo_gerencia_area_ope(NULL, v_solicitud.id_funcionario, v_solicitud.fecha_soli::Date);

                        IF exists(select 1 from orga.tuo_funcionario uof
                                   inner join orga.tuo uo on uo.id_uo = uof.id_uo and uo.estado_reg = 'activo'
                                   inner join orga.tnivel_organizacional no on no.id_nivel_organizacional = uo.id_nivel_organizacional and no.numero_nivel in (1,2)
                                   where  uof.estado_reg = 'activo' and  uof.id_funcionario = v_solicitud.id_funcionario ) THEN

                              va_id_funcionario_gerente[1] = v_solicitud.id_funcionario;

                        ELSE
                            --si tiene funcionario identificar el gerente correspondientes
                            IF v_solicitud.id_funcionario is not NULL THEN

                                SELECT
                                   pxp.aggarray(id_funcionario)
                                 into
                                   va_id_funcionario_gerente
                                 FROM orga.f_get_aprobadores_x_funcionario(v_solicitud.fecha_soli,v_solicitud.id_funcionario , 'todos', 'si', 'todos', 'ninguno') AS (id_funcionario integer);

                             END IF;
                        END IF;

                          v_idfun_modalidad = va_id_funcionario_gerente[1] ;

                 ELSE


                		 IF (v_modalidades_matriz.id_cargo is null) THEN
                            RAISE EXCEPTION 'No se encuentra parametrizado el Responsable de Nivel Aprobación  en la Matriz Tipo Contratación - Aprobador. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre).';
                         END IF;

                         --CONDICION PARA RESCATAR DE LA MATRIZ EL FUNCIONARIO RESPONSABLE
                         SELECT vc.id_funcionario
                         INTO v_idfun_modalidad
                         FROM orga.vfuncionario_cargo vc
                         WHERE vc.id_cargo = v_modalidades_matriz.id_cargo
                         and vc.fecha_asignacion  <=  now()
                         and (vc.fecha_finalizacion is null or vc.fecha_finalizacion >= now() );

                END IF;

             ELSE

                   IF (v_modalidades_matriz.id_cargo is null) THEN
                      RAISE EXCEPTION 'No se encuentra parametrizado el Responsable de Nivel Aprobación  en la Matriz Tipo Contratación - Aprobador. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre).';
                   END IF;

                   --CONDICION PARA RESCATAR DE LA MATRIZ EL FUNCIONARIO RESPONSABLE
                   SELECT vc.id_funcionario
                   INTO v_idfun_modalidad
                   FROM orga.vfuncionario_cargo vc
                   WHERE vc.id_cargo = v_modalidades_matriz.id_cargo
                   and vc.fecha_asignacion  <=  now()
                   and (vc.fecha_finalizacion is null or vc.fecha_finalizacion >= now() );

             END IF;

             SELECT vf.desc_funcionario1
             INTO	v_desc_funcionario
             FROM orga.vfuncionario vf
             WHERE vf.id_funcionario = v_idfun_modalidad;

             SELECT car.nombre
             INTO	v_nombre_cargo
             FROM orga.tcargo car
             WHERE car.id_cargo = v_modalidades_matriz.id_cargo;

             IF (v_idfun_modalidad is null) THEN
                  RAISE EXCEPTION 'No se encuentra el Funcionario Responsable % del cargo % en la Matriz Tipo Contratación - Aprobado.  Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre). ',v_desc_funcionario, v_nombre_cargo ;
             END IF;

            ---


            UPDATE adq.tmodalidad_solicitud SET
            modalidad_menor = v_modalidades_matriz.modalidad_menor,
            modalidad_anpe = v_modalidades_matriz.modalidad_anpe,
            modalidad_directa = v_modalidades_matriz.modalidad_directa,
            modalidad_licitacion = v_modalidades_matriz.modalidad_licitacion,
            modalidad_excepcion = v_modalidades_matriz.modalidad_excepcion,
            modalidad_desastres = v_modalidades_matriz.modalidad_desastres,

            id_funcionario_aprobador = v_idfun_modalidad,
            flujo_mod_directa = v_modalidades_matriz.flujo_mod_directa,

            resp_proc_contratacion_menor = v_modalidades_matriz.resp_proc_contratacion_menor,
            resp_proc_contratacion_anpe = v_modalidades_matriz.resp_proc_contratacion_anpe,
            resp_proc_contratacion_directa = v_modalidades_matriz.resp_proc_contratacion_directa,
            resp_proc_contratacion_licitacion = v_modalidades_matriz.resp_proc_contratacion_licitacion,
            resp_proc_contratacion_desastres = v_modalidades_matriz.resp_proc_contratacion_desastres,
            resp_proc_contratacion_excepcion = v_modalidades_matriz.resp_proc_contratacion_excepcion

            WHERE id_matriz_modalidad = v_id_matriz_mod.id_matriz_modalidad;


      END LOOP  ;


      FOR v_modalidad_solicitud in(SELECT ms.id_modalidad_solicitud
                                    FROM adq.tmodalidad_solicitud ms
                                    WHERE ms.id_solicitud =  v_id_solicitud
                                    and ms.estado_reg = 'activo'
                                      )LOOP

                    SELECT  ms.modalidad_menor,
                    	    ms.modalidad_anpe,
                            ms.modalidad_directa,
                            ms.modalidad_licitacion,
                            ms.modalidad_desastres,
                            ms.modalidad_excepcion,
                            ms.id_concepto_ingas,
                            ms.id_matriz_modalidad,
                            ms.flujo_mod_directa,

                            ms.resp_proc_contratacion_menor,
                            ms.resp_proc_contratacion_anpe,
                            ms.resp_proc_contratacion_directa,
                            ms.resp_proc_contratacion_licitacion,
                            ms.resp_proc_contratacion_desastres,
                            ms.resp_proc_contratacion_excepcion

                    INTO v_modalidades_solicitud
                    FROM adq.tmodalidad_solicitud ms
                    WHERE ms.id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud
                    and ms.estado_reg = 'activo';

                    --------
                    --VERIFICACION DE MODALIDADES
                    --par los que entran en la condicion con menores y directas
                    IF v_modalidades_solicitud.modalidad_directa = 'si' THEN

                          --COMPARACION CON LA TABLA DE MODALIDAD que ingrese los que se parametricen con conceptos de gasto
                          SELECT mod.codigo
                          into v_codigo_modalidad
                          FROM adq.tmodalidades mod
                          WHERE mod.condicion_menor <= v_total_det
                          and mod.condicion_mayor >= v_total_det
                          and mod.con_concepto = 'si';

                    ELSE

                          --COMPARACION CON LA TABLA DE MODALIDAD que no ingrese los que se parametricen con concepto de gasto
                          SELECT mod.codigo
                          into v_codigo_modalidad
                          FROM adq.tmodalidades mod
                          WHERE mod.condicion_menor <= v_total_det
                          and mod.condicion_mayor >= v_total_det
                          and mod.con_concepto = 'no';

                    END IF;

                    ------------

                    --nombre del concepto de gasto
                    SELECT cin.desc_ingas
                    into v_desc_ingas
                    FROM param.tconcepto_ingas cin
                    WHERE cin.id_concepto_ingas = v_modalidades_solicitud.id_concepto_ingas;

                    --nombre de la matriz modalidad
                    SELECT mm.referencia ||'-'||mm.tipo_contratacion
                    into v_nom_tipo_contratacion
                    FROM adq.tmatriz_modalidad mm
                    WHERE mm.id_matriz_modalidad = v_modalidades_solicitud.id_matriz_modalidad;

                    --nombre de la matriz modalidad
                    SELECT mod.nombre_modalidad
                    into v_nombre_modalidad
                    FROM adq.tmodalidades mod
                    WHERE mod.codigo = v_codigo_modalidad;

                    	IF (v_codigo_modalidad ='mod_menor') THEN
                        	v_modalidad = v_modalidades_solicitud.modalidad_menor;

                            IF v_modalidad = 'si' THEN
                                UPDATE adq.tmodalidad_solicitud set
                                calificacion = 'SI'
                                WHERE id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud;
                            ELSE
                            	UPDATE adq.tmodalidad_solicitud set
                                calificacion = 'NO'
                                WHERE id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud;

                            	raise exception 'Este proceso pertenece al Tipo Contratación: %, y no esta habilitado para la %  en la Matriz Tipo Contratación-Aprobador. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre). ',v_nom_tipo_contratacion, upper(v_nombre_modalidad);

                            END IF;

                        ELSIF (v_codigo_modalidad ='mod_anpe') THEN
                        	v_modalidad = v_modalidades_solicitud.modalidad_anpe;

                            IF v_modalidad = 'si' THEN
                                UPDATE adq.tmodalidad_solicitud set
                                calificacion = 'SI'
                                WHERE id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud;
                            ELSE
                            	UPDATE adq.tmodalidad_solicitud set
                                calificacion = 'NO'
                                WHERE id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud;

                            	raise exception 'Este proceso pertenece al Tipo Contratación: %, y no esta habilitado para la %  en la Matriz Tipo Contratación-Aprobador. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre). ',v_nom_tipo_contratacion, upper(v_nombre_modalidad);

                            END IF;

                        ELSIF (v_codigo_modalidad ='mod_licitacion') THEN
                        	v_modalidad = v_modalidades_solicitud.modalidad_directa;

                            IF v_modalidad = 'si' THEN
                                UPDATE adq.tmodalidad_solicitud set
                                calificacion = 'SI'
                                WHERE id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud;
                            ELSE
                            	UPDATE adq.tmodalidad_solicitud set
                                calificacion = 'NO'
                                WHERE id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud;

                            	raise exception 'Este proceso pertenece al Tipo Contratación: %, y no esta habilitado para la %  en la Matriz Tipo Contratación-Aprobador. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre). ',v_nom_tipo_contratacion, upper(v_nombre_modalidad);

                            END IF;

                        ELSIF (v_codigo_modalidad ='mod_directa') THEN
                        	v_modalidad = v_modalidades_solicitud.modalidad_directa;

                            IF v_modalidad = 'si' THEN
                                UPDATE adq.tmodalidad_solicitud set
                                calificacion = 'SI'
                                WHERE id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud;
                            ELSE
                            	UPDATE adq.tmodalidad_solicitud set
                                calificacion = 'NO'
                                WHERE id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud;

                            	raise exception 'Este proceso pertenece al Tipo Contratación: %, y no esta habilitado para la %  en la Matriz Tipo Contratación-Aprobador. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre). ',v_nom_tipo_contratacion, upper(v_nombre_modalidad);

                            END IF;

                        /*
                        ELSIF (v_modalidades_solicitud.modalidad_desastres ='si') THEN
                        	v_modalidad = 'mod_';

                        ELSIF (v_modalidades_solicitud.modalidad_excepcion ='si') THEN
                        	v_modalidad = 'mod_';*/

                        END IF;


                    SELECT count(ms.id_modalidad_solicitud)
                    INTO v_count_modalidad
                    FROM adq.tmodalidad_solicitud ms
                    WHERE ms.id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud
                    and ms.id_solicitud = v_id_solicitud
                    and ms.calificacion = 'SI';

                    IF (v_count_modalidad >=2) THEN

                    	raise EXCEPTION 'son mas de dos calificados como si';

                    ELSE

                    	SELECT ms.id_funcionario_aprobador,
                        		ms.modalidad_menor,
                                ms.modalidad_anpe,
                                ms.modalidad_directa,
                                ms.modalidad_licitacion,
                                ms.modalidad_desastres,
                                ms.modalidad_excepcion,

                                ms.resp_proc_contratacion_menor,
                                ms.resp_proc_contratacion_anpe,
                                ms.resp_proc_contratacion_directa,
                                ms.resp_proc_contratacion_licitacion,
                                ms.resp_proc_contratacion_desastres,
                                ms.resp_proc_contratacion_excepcion

                        INTO v_solu_modalidades
                        FROM adq.tmodalidad_solicitud ms
                        WHERE ms.id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud
                        and ms.id_solicitud = v_id_solicitud
                        and ms.calificacion = 'SI';

                        IF (v_solu_modalidades.modalidad_menor = 'si' and v_codigo_modalidad = 'mod_menor') THEN
                        	v_respuesta_modalidad = 'mod_menor';
                            v_proceso_contratacion = 'RPA';

                        ELSIF (v_solu_modalidades.modalidad_anpe = 'si' and v_codigo_modalidad = 'mod_anpe') THEN
                        	v_respuesta_modalidad = 'mod_anpe';
                            v_proceso_contratacion = 'RPA';

                        ELSIF (v_solu_modalidades.modalidad_licitacion = 'si' and v_codigo_modalidad = 'mod_licitacion') THEN
                        	v_respuesta_modalidad = 'mod_licitacion';
                            v_proceso_contratacion = v_solu_modalidades.resp_proc_contratacion_licitacion;

                        ELSIF (v_solu_modalidades.modalidad_directa = 'si' and v_codigo_modalidad = 'mod_directa') THEN
                        	v_respuesta_modalidad = v_modalidades_solicitud.flujo_mod_directa;
                            v_proceso_contratacion = v_solu_modalidades.resp_proc_contratacion_directa;

                        END IF;

                    	UPDATE adq.tsolicitud SET
                        id_funcionario_supervisor = v_solu_modalidades.id_funcionario_aprobador,
                        tipo_modalidad = v_respuesta_modalidad,
                        proceso_contratacion = v_proceso_contratacion
                        WHERE id_solicitud = v_id_solicitud;

                    END IF;




      END LOOP;


      --raise exception 'llegan %',v_resp;

          --Devuelve la respuesta
          return ;

        raise notice '%',v_resp;

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
COST 100 ROWS 1000;
