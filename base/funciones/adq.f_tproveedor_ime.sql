CREATE OR REPLACE FUNCTION adq.f_tproveedor_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Adquisiciones
 FUNCION: 		adq.f_tproveedor_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'adq.tdocumento_sol'
 AUTOR: 		Gonzalo Sarmiento Sejas
 FECHA:	        01-03-2013 19:01:00
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
	v_id_proveedor			integer;
    cadena_con				varchar;
    v_res_cone				varchar;
    v_consulta				varchar;

    resp text;
    v_id_persona 			int4;
    v_id_institucion		int4;
    v_registro				record;
    v_num_seq					integer;
	v_codigo_gen				varchar;

BEGIN

    v_nombre_funcion = 'adq.ft_proveedor_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'ADQ_PROVEE_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		01-03-2013 19:01:00
	***********************************/

	if(p_transaccion='ADQ_PROVEE_INS')then

        begin
        	/*if (pxp.f_get_variable_global('sincronizar') = 'true') then
            	v_consulta = 'SELECT *
                FROM compro.f_tad_nuevo_proveedor_pxp_iud(
                		''insert'',NULL, '|| coalesce (v_parametros.id_persona::text, 'NULL') || ',''' ||  v_parametros.codigo || ''',''' ||
                        v_parametros.numero_sigma || ''',''' ||  CASE WHEN v_parametros.tipo_prov <>'' THEN v_parametros.tipo_prov ELSE v_parametros.tipo END || ''',' ||  coalesce (v_parametros.id_institucion::text, 'NULL') || ',' ||
                        coalesce (v_parametros.doc_id::text, 'NULL') || ',''' ||  v_parametros.nombre_institucion || ''',''' ||  v_parametros.direccion_institucion || ''',' ||
                         coalesce (v_parametros.casilla::text, 'NULL') || ',' ||   coalesce (v_parametros.telefono1_institucion::text, 'NULL') || ',' ||   coalesce (v_parametros.telefono2_institucion::text, 'NULL') || ',' ||
                         coalesce (v_parametros.celular1_institucion::text, 'NULL') || ',' ||   coalesce (v_parametros.celular2_institucion::text, 'NULL') || ',' ||   coalesce (v_parametros.fax::text, 'NULL') || ',''' ||
                        v_parametros.email1_institucion || ''',''' ||  v_parametros.email2_institucion || ''',''' ||  v_parametros.pag_web || ''',''' ||
                        v_parametros.observaciones || ''',''' ||  v_parametros.codigo_banco || ''',''' ||  v_parametros.codigo_institucion || ''',
                        ''' ||  coalesce (v_parametros.nit::text, 'NULL') ||''',' ||  coalesce (v_parametros.id_lugar::text, 'NULL')|| ',''' ||  v_parametros.register || ''',''' ||
                        v_parametros.nombre || ''',''' ||  v_parametros.apellido_paterno || ''',''' ||  v_parametros.apellido_materno || ''','||
                        coalesce (v_parametros.ci::text, 'NULL') || ',''' ||  v_parametros.correo || ''',''' ||  coalesce (v_parametros.celular1::text, 'NULL') || ''',''' ||  coalesce (v_parametros.celular2::text, 'NULL') || ''',''' ||
                        coalesce (v_parametros.telefono1::text, 'NULL') || ''',''' || coalesce (v_parametros.telefono2::text, 'NULL') || ''',''' ||  v_parametros.genero || ''',' ||  coalesce ('''' || v_parametros.fecha_nacimiento::text || '''', 'NULL') || ',''' ||
                        v_parametros.direccion || ''',''' || v_parametros.rotulo_comercial || ''',''' || v_parametros.contacto || ''')';

                select * FROM dblink(migra.f_obtener_cadena_conexion(),
                v_consulta,TRUE)AS t1(resp varchar)
            			into v_resp;


                 select * FROM dblink(migra.f_obtener_cadena_conexion(),
                 'SELECT * FROM migracion.f_sincronizacion()',FALSE)AS t1(resp varchar)
            	 into v_resp;

            else*/

                     v_num_seq =  nextval('param.tproveedor_id_proveedor_seq');
                     v_codigo_gen = 'P'||pxp.f_llenar_ceros(v_num_seq, 7);

                if v_parametros.register = 'before_registered' then
					--RAISE EXCEPTION 'salida: %',v_codigo_gen;
                    insert into param.tproveedor
                    (id_usuario_reg,
                    fecha_reg,
                    estado_reg,
                    id_institucion,
                    id_persona,
                    tipo,
                    numero_sigma,
                    codigo,
                    nit,
                    id_lugar,
                    rotulo_comercial,
                    contacto,

                    condicion,
                    actividad,
                    num_proveedor

                    )
                    values
                    (
                    p_id_usuario,
                    now(),
                    'activo',
                    v_parametros.id_institucion,
                    v_parametros.id_persona,
                    case when v_parametros.tipo_prov <>'' THEN v_parametros.tipo_prov
                											when v_parametros.id_persona is NULL THEN

                                                                    'institucion'
                                                             else
                                                                    'persona'
                                                            end,
                    v_parametros.numero_sigma,
                    v_codigo_gen,
                    v_parametros.nit,
                    v_parametros.id_lugar,
                    v_parametros.rotulo_comercial,
                    v_parametros.contacto,

                    v_parametros.condicion,
                    v_parametros.actividad,
                    v_parametros.num_proveedor

                    )RETURNING id_proveedor into v_id_proveedor;
                else
                    if (v_parametros.tipo = 'persona')then
                        insert into segu.tpersona (
                                   nombre,
                                   apellido_paterno,
                                   apellido_materno,
                                   ci,
                                   correo,
                                   celular1,
                                   telefono1,
                                   telefono2,
                                   celular2,
                                   --foto,
                                   --extension,
                                   genero,
                                   fecha_nacimiento,
                                   direccion,
                                   codigo_telf)
                         values(
                                --v_parametros.nombre,
                                v_parametros.nombre_persona,
                                v_parametros.apellido_paterno,
                                v_parametros.apellido_materno,
                                v_parametros.ci,
                                v_parametros.correo,
                                v_parametros.celular1,
                                v_parametros.telefono1,
                                v_parametros.telefono2,
                                v_parametros.celular2,
                                --v_parametros.foto,
                                --v_parametros.extension,
                                v_parametros.genero,
                                v_parametros.fecha_nacimiento,
                                v_parametros.direccion,
                                v_parametros.codigo_telf)

                        RETURNING id_persona INTO v_id_persona;
                    else
                        --Sentencia de la insercion
                        insert into param.tinstitucion(
                        fax,
                        estado_reg,
                        casilla,
                        direccion,
                        doc_id,
                        telefono2,
                        email2,
                        celular1,
                        email1,
                        nombre,
                        observaciones,
                        telefono1,
                        celular2,
                        codigo_banco,
                        pag_web,
                        id_usuario_reg,
                        fecha_reg,
                        id_usuario_mod,
                        fecha_mod,
                        codigo,
                        codigo_telf_institucion

                        ) values(

                        v_parametros.fax,
                        'activo',
                        v_parametros.casilla,
                        v_parametros.direccion_institucion,
                        v_parametros.doc_id,
                        v_parametros.telefono2_institucion,
                        v_parametros.email2_institucion,
                        v_parametros.celular1_institucion,
                        v_parametros.email1_institucion,
                        v_parametros.nombre_institucion,
                        v_parametros.observaciones,
                        v_parametros.telefono1_institucion,
                        v_parametros.celular2_institucion,
                        v_parametros.codigo_banco,
                        v_parametros.pag_web,
                        p_id_usuario,
                        now(),
                        null,
                        null,
                        v_parametros.codigo,
                        v_parametros.codigo_telf_institucion

                        )RETURNING id_institucion into v_id_institucion;
                    end if;

                        insert into param.tproveedor
                        (id_usuario_reg,
                        fecha_reg,
                        estado_reg,
                        id_institucion,
                        id_persona,
                        tipo,
                        numero_sigma,
                        codigo,
                        nit,
                        id_lugar,
                        rotulo_comercial,
                        contacto,
                        num_proveedor
                        )
                        values
                        (
                        p_id_usuario,
                        now(),
                        'activo',
                        v_id_institucion,
                        v_id_persona,
                        case when v_parametros.tipo_prov <>'' THEN
                                  v_parametros.tipo_prov
                             when v_id_persona is NULL THEN
                                  'institucion'
                             else
                                  'persona'
                        end,
                        v_parametros.numero_sigma,
                        v_codigo_gen,
                        v_parametros.nit,
                        v_parametros.id_lugar,
                        v_parametros.rotulo_comercial,
                        v_parametros.contacto,
                        v_parametros.num_proveedor

                        )RETURNING id_proveedor into v_id_proveedor;
                --end if;
            end if;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Proveedor almacenado(a) con exito');
            v_resp = pxp.f_agrega_clave(v_resp,'id_proveedor',v_id_proveedor::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_PROVEE_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		01-03-2013 19:01:00
	***********************************/

	elsif(p_transaccion='ADQ_PROVEE_MOD')then

		begin

			/*if (pxp.f_get_variable_global('sincronizar') = 'true') then

               v_consulta = 'SELECT *
                FROM compro.f_tad_nuevo_proveedor_pxp_iud(
                		''update'',' || v_parametros.id_proveedor || ', '|| coalesce (v_parametros.id_persona::text, 'NULL') || ',''' ||  v_parametros.codigo || ''',''' ||
                        v_parametros.numero_sigma || ''',''' || CASE WHEN v_parametros.tipo_prov <>'' THEN v_parametros.tipo_prov ELSE v_parametros.tipo END || ''',' ||  coalesce (v_parametros.id_institucion::text, 'NULL') || ',' ||
                        coalesce (v_parametros.doc_id::text, 'NULL') || ',''' ||  v_parametros.nombre_institucion || ''',''' ||  v_parametros.direccion_institucion || ''',' ||
                         coalesce (v_parametros.casilla::text, 'NULL') || ',' ||   coalesce (v_parametros.telefono1_institucion::text, 'NULL') || ',' ||   coalesce (v_parametros.telefono2_institucion::text, 'NULL') || ',' ||
                         coalesce (v_parametros.celular1_institucion::text, 'NULL') || ',' ||   coalesce (v_parametros.celular2_institucion::text, 'NULL') || ',' ||   coalesce (v_parametros.fax::text, 'NULL') || ',''' ||
                        v_parametros.email1_institucion || ''',''' ||  v_parametros.email2_institucion || ''',''' ||  v_parametros.pag_web || ''',''' ||
                        v_parametros.observaciones || ''',''' ||  v_parametros.codigo_banco || ''',''' ||  v_parametros.codigo_institucion || ''',
                        ''' ||  coalesce (v_parametros.nit::text, 'NULL') ||''',' ||  coalesce (v_parametros.id_lugar::text, 'NULL')|| ',''' ||  v_parametros.register || ''',''' ||
                        v_parametros.nombre || ''',''' ||  v_parametros.apellido_paterno || ''',''' ||  v_parametros.apellido_materno || ''','||
                        coalesce (v_parametros.ci::text, 'NULL') || ',''' ||  v_parametros.correo || ''',''' ||  coalesce (v_parametros.celular1::text, 'NULL') || ''',''' ||  coalesce (v_parametros.celular2::text, 'NULL') || ''',''' ||
                        coalesce (v_parametros.telefono1::text, 'NULL') || ''',''' || coalesce (v_parametros.telefono2::text, 'NULL') || ''',''' ||  v_parametros.genero || ''',' ||  coalesce ('''' || v_parametros.fecha_nacimiento::text || '''', 'NULL') || ',''' ||
                        v_parametros.direccion || ''',''' || v_parametros.rotulo_comercial || ''',''' || v_parametros.contacto || ''')';

                select * FROM dblink(migra.f_obtener_cadena_conexion(),
                v_consulta,TRUE)AS t1(resp varchar)
            			into v_resp;

                 select * FROM dblink(migra.f_obtener_cadena_conexion(),
                 'SELECT * FROM migracion.f_sincronizacion()',FALSE)AS t1(resp varchar)
            	 into v_resp;

            end if;*/

            update param.tproveedor set
            numero_sigma = v_parametros.numero_sigma,
            id_institucion = v_parametros.id_institucion,
            id_persona = v_parametros.id_persona,
            id_lugar = v_parametros.id_lugar,
            nit = v_parametros.nit,
            id_usuario_mod = p_id_usuario,
            fecha_mod = now(),
            rotulo_comercial = v_parametros.rotulo_comercial,
            contacto = v_parametros.contacto,
            tipo = v_parametros.tipo_prov,

            condicion = v_parametros.condicion,
            actividad = v_parametros.actividad,
            num_proveedor = v_parametros.num_proveedor

            where id_proveedor=v_parametros.id_proveedor;

            update param.tinstitucion set
            direccion = v_parametros.direccion_institucion,
            email2=v_parametros.email2_institucion,
            email1=v_parametros.email1_institucion,
            pag_web=v_parametros.pag_web,
            telefono1=v_parametros.telefono1_institucion,
            telefono2=v_parametros.telefono2_institucion,
            celular1=v_parametros.celular1_institucion,
            celular2=v_parametros.celular2_institucion,
            codigo_telf_institucion = v_parametros.codigo_telf_institucion

            where id_institucion=v_parametros.id_institucion;

            update segu.tpersona SET
            ci = v_parametros.ci,
            correo=v_parametros.correo,
            celular1=v_parametros.celular1,
            telefono1=v_parametros.telefono1,
            telefono2=v_parametros.telefono2,
            celular2=v_parametros.celular2,
            direccion = v_parametros.direccion,
            codigo_telf = v_parametros.codigo_telf

            where id_persona=v_parametros.id_persona;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Proveedor modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_proveedor',v_parametros.id_proveedor::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_PROVEE_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		08-02-2013 19:01:00
	***********************************/

	elsif(p_transaccion='ADQ_PROVEE_ELI')then

		begin
			--Sentencia de la eliminacion
           /* if (pxp.f_get_variable_global('sincronizar') = 'true') then

                select * FROM dblink(migra.f_obtener_cadena_conexion(),
                'SELECT *
                  FROM compro.f_tad_nuevo_proveedor_pxp_iud(''delete'', '|| v_parametros.id_proveedor || ',NULL
                  , NULL, NULL, NULL,NULL, NULL,
                   NULL, NULL, NULL,
                    NULL, NULL, NULL,
                    NULL, NULL, NULL, NULL,
                    NULL, NULL, NULL, NULL, NULL,
                    NULL, NULL, NULL, NULL, NULL,
                    NULL, NULL, NULL, NULL, NULL, NULL, NULL
                    ,NULL, NULL,NULL,NULL)',TRUE)AS t1(resp varchar)
            	into v_resp;


                 select * FROM dblink(migra.f_obtener_cadena_conexion(),
                 'SELECT * FROM migracion.f_sincronizacion()',FALSE)AS t1(resp varchar)
            	 into v_resp;

            else*/

              	delete from param.tproveedor
              	where id_proveedor=v_parametros.id_proveedor;
            --end if;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Proveedor eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_proveedor',v_parametros.id_proveedor::varchar);

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
