<?
/*
 *
 * Autor: F. Javier Campos
 *
 *
 * Descripci?n: Todas las operaciones con Base de Datos (BDA) se hacen desde este archivo. Esto
 * permite cambiar la tecnolog?a de la BDA con mayor facilidad, sobretodo en equipos sin ODBC
 */

require_once("config_bda.php");

function conectar_bd(){
  /* devuelve identificador conexi?n, o FALSE si error */
  
  $db=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
        if($db==false){
                echo("Error al conectar con la base de datos (error: 1). Int&eacute;ntalo de nuevo m&aacute;s tarde.");
                return false;
        }
        if(!mysql_select_db(DB_NAME)){
                echo "Error al conectar con la base de datos (error: 2). Int&eacute;ntalo de nuevo m&aacute;s tarde.";
                return false;
        }
        
        return $db;
}

function consulta_bd($query){
// devuelve FALSE o TRUE, segun error y exito, respectivamente

  return mysql_query($query);
}

function numero_filas_resultado_bd($results){
  return mysql_num_rows($results);
}

function obtener_fila_assoc_bd($results){
  return mysql_fetch_assoc($results);
}

function obtener_fila_row_bd($results){
  return mysql_fetch_row($results);
}

function liberar_resultado_bd($results){
  mysql_free_result($results);
}

function desconectar_bd($id){ // NO es necesaria si usamos "liberar_resultado_bd()"
  mysql_close($id);
}

function insert_id_bd(){
  return mysql_insert_id();
}

/*******************************************************************************/

function comillas_inteligentes($valor,$conexion){
/* Escapa los caracteres especiales de 'valor', segun las caracteristicas 
 * de la BDA a la que se conecta 'conexion'.
 *
 * Devuelve: la cadena escapada y entre comillas simples. 
 */

    $valor = trim($valor);
    $valor = htmlentities($valor,ENT_QUOTES,"UTF-8");
    
    // Retirar las barras
    if (get_magic_quotes_gpc()) {
        $valor = stripslashes($valor);
    }
    
    $valor=strtolower($valor); // paso a minusculas

    // Colocar comillas si no es entero
    if (!is_numeric($valor)) {
        $valor = "'" . mysql_real_escape_string($valor,$conexion) . "'";
    }
    return $valor;

}
/*******************************************************************************/

function existe_partida($titulo, $user){
	
	global $ERROR;
  
    $db=conectar_bd();
    
    if($db==false){
            $ERROR="Hay problemas para acceder a la Base de Datos.";
            return false;
    }
    
	// partidas creadas por mi
	$sql = "SELECT 1 FROM partida p WHERE p.id_creador=".comillas_inteligentes($user,$db). " AND p.titulo_partida=".comillas_inteligentes($titulo,$db) . " LIMIT 1";
	$result=consulta_bd($sql);
    
    if($result==false){
      $ERROR="Se produjo un error durante la consulta.";
      return false;
    }
    else{
		$num=numero_filas_resultado_bd($result);
		liberar_resultado_bd($result);
    
	    if($num!=0){ // si ya existe...
	        $ERROR= "Ya existe una partida creada por ti y con el mismo nombre. Revisa tus partidas.";
	        return false;
	    }
	    
		return true;
	}

}

function alta_partida($val){
/* a�ade la partida recibida en la BDA. Los campos de '$val' son trim(cadenas)
 * 
 * Devuelve false si se produce un error.
 *   
 */
    global $ERROR;
  
    $db=conectar_bd();
    
    if($db==false){
            $ERROR="Hay problemas para acceder a la Base de Datos.";
            return false;
    }
    
    $trozo_fecha = explode("-",$val["fecha"]);
    
    $sql= "INSERT INTO partida ".
    "(id_creador,fecha_creacion, titulo_partida, descripcion_partida, estado_partida) ".
    "VALUES (".comillas_inteligentes($val["id_creador"],$db).",".
			  "NOW(),".
              comillas_inteligentes($val["titulo_partida"],$db).",".
              comillas_inteligentes($val["descripcion_partida"],$db).",". 
              "1)";

    $result=consulta_bd($sql);
    
    if($result==false){
      $ERROR="Se produjo un error durante la insercion de la nueva partida a la Base de Datos.";
      return false;
    }
    else{
		$id_partida = insert_id_bd(); // devuelve el identificador.
	
		// a�adir participantes
		$sql_cab = "INSERT INTO partida_jugador ".
					"(id_partida, id_jugador, estado_jugador, fecha_cambio) ".
					"VALUES (";
		for($i=0;$i<count($val["participantes"]);$i++){
			$sql = $sql_cab.	comillas_inteligentes($id_partida,$db).",".
								comillas_inteligentes($val["participantes"][$i],$db).",".
								"1,NULL)";
			$result=consulta_bd($sql);
		}
		
		return $id_partida;
	}
}

function incluir_participantes_en_partida($id_partida, $participantes){
	
		global $ERROR;
  
	    $db=conectar_bd();
	    
	    if($db==false){
	            $ERROR="Hay problemas para acceder a la Base de Datos.";
	            return false;
	    }
		// a�adir participantes
		$sql_cab = "INSERT INTO partida_jugador ".
					"(id_partida, id_jugador, estado_jugador, fecha_cambio) ".
					"VALUES (";
		
		for($i=0;$i<count($participantes);$i++){
			$sql = $sql_cab.	comillas_inteligentes($id_partida,$db).",".
								comillas_inteligentes($participantes[$i],$db).",".
								"1,NULL)";
			$result=consulta_bd($sql);
		}
}



function obtener_partidas_creadas($id_usuario){
	
	global $ERROR;
  
    $db=conectar_bd();
    
    if($db==false){
            $ERROR="Hay problemas para acceder a la Base de Datos.";
            return false;
    }
    
    $trozo_fecha = explode("-",$val["fecha"]);
    
	// partidas creadas por mi
	$sql = "SELECT * FROM partida p, estados_partida ep WHERE p.id_creador=".comillas_inteligentes($id_usuario,$db). " AND ep.id_estado_partida=p.estado_partida";
	$result=consulta_bd($sql);
    
    if($result==false){
      $ERROR="Se produjo un error durante la consulta.";
      return false;
    }
    else{
		$num=numero_filas_resultado_bd($result);
    
	    if($num==0){ // si no hay partidas
	        $ERROR= "";
	        return false;
	    }
	    
	    for($i=0; $i<$num; $i++){
	        $partida[$i]=obtener_fila_assoc_bd($result);
	    }
	    
	    liberar_resultado_bd($result);
		
		return $partida;
	}

}

function obtener_usuarios_partida($id_partida){
	
	global $ERROR;
  
    $db=conectar_bd();
    
    if($db==false){
            $ERROR="Hay problemas para acceder a la Base de Datos.";
            return false;
    }
    
    $trozo_fecha = explode("-",$val["fecha"]);
    
	// partidas creadas por mi
	$sql = "SELECT * FROM partida_jugador pj WHERE pj.id_partida=".comillas_inteligentes($id_partida,$db);
	$result=consulta_bd($sql);
    
    if($result==false){
      $ERROR="Se produjo un error durante la consulta.";
      return false;
    }
    else{
		$num=numero_filas_resultado_bd($result);
    
	    if($num==0){ // si no hay usuarios
	        $ERROR= "";
	        return false;
	    }
	    
	    for($i=0; $i<$num; $i++){
	        $usuario[$i]=obtener_fila_assoc_bd($result);
	    }
	    
	    liberar_resultado_bd($result);
		
		return $usuario;
	}

}


/************************************/

function obtener_partidas_participo($id_usuario){
	
	global $ERROR;
  
    $db=conectar_bd();
    
    if($db==false){
            $ERROR="Hay problemas para acceder a la Base de Datos.";
            return false;
    }
    
    $trozo_fecha = explode("-",$val["fecha"]);
    
	// partidas creadas por mi
	$sql = "SELECT * FROM partida p, estados_partida ep, partida_jugador pj WHERE p.id_partida=pj.id_partida AND  pj.id_jugador=".comillas_inteligentes($id_usuario,$db) . " AND ep.id_estado_partida=p.estado_partida";;
    $result=consulta_bd($sql);
    
    if($result==false){
      $ERROR="Se produjo un error durante la consulta.";
      return false;
    }
    else{
		$num=numero_filas_resultado_bd($result);
    
	    if($num==0){ // si no hay partidas
	        $ERROR= "";
	        return false;
	    }
	    
	    for($i=0; $i<$num; $i++){
	        $partida[$i]=obtener_fila_assoc_bd($result);
	    }
	    
	    liberar_resultado_bd($result);
		
		return $partida;
	}

}


/* actualizar_asistencia_partida()
 *
 * id_partida: identificador de la partida
 * $user: id del usuario
 * $accion: 2 (s� va) � 3 (no va) */
function actualizar_asistencia_partida($id_partida,$user,$accion){
	
	global $ERROR;
  
    $db=conectar_bd();
    
    if($db==false){
            $ERROR="Hay problemas para acceder a la Base de Datos.";
            return false;
    }
    
    
    $sql = "UPDATE partida p, partida_jugador pj 
			SET pj.fecha_cambio = NOW(), pj.estado_jugador=".comillas_inteligentes($accion,$db).
			" WHERE (p.id_partida = pj.id_partida AND 
			p.estado_partida = 1 AND 
			pj.id_jugador = ".comillas_inteligentes($user,$db)." AND
			p.id_partida = ".comillas_inteligentes($id_partida,$db).")";
    
	$result=consulta_bd($sql);
}

/*-----------------------------------*/

function es_creador_partida($id_partida,$user){
	// devuelve la PARTIDA si esa partida la cre� ese usuario
	// FALSE si no la cre�.
	
	global $ERROR;
  
    $db=conectar_bd();
    
    if($db==false){
            $ERROR="Hay problemas para acceder a la Base de Datos.";
            return false;
    }
    
	// partidas creadas por mi
	$sql = "SELECT * FROM partida p WHERE p.id_creador=".comillas_inteligentes($user,$db). " AND p.id_partida=".comillas_inteligentes($id_partida,$db) . " LIMIT 1";
	$result=consulta_bd($sql);
    
    if($result==false){
      $ERROR="Se produjo un error durante la consulta.";
      return false;
    }
    else{
		$num=numero_filas_resultado_bd($result);
     	if($num == 0){ // si no existe... FALSE      
	        return true;
	    }
	    
		$fila = obtener_fila_assoc_bd($result);
		liberar_resultado_bd($result);
    
	    return $fila;
	}

}

function actualizar_estado_partida($id_partida,$estado_partida){
	global $ERROR;
  
    $db=conectar_bd();
    
    if($db==false){
            $ERROR="Hay problemas para acceder a la Base de Datos.";
            return false;
    }

    
	$sql = "UPDATE partida p 
				SET p.estado_partida=".comillas_inteligentes($estado_partida,$db).
				" WHERE (p.id_partida = ".comillas_inteligentes($id_partida,$db).") LIMIT 1";
	    
	//echo "$sql";
	    
	$result=consulta_bd($sql);
	
	return true;
}


function almacenar_sorteo($id_partida, $resultado_sorteo){
	// actualiza los usuarios (el campo 'regala_a') y el campo partida.estado
	
	global $ERROR;
  
    $db=conectar_bd();
    
    if($db==false){
            $ERROR="Hay problemas para acceder a la Base de Datos.";
            return false;
    }
    
    foreach ($resultado_sorteo as $key => $value){ // key: el que regala; $value: al que regala
    
	    $sql = "UPDATE partida_jugador pj 
				SET pj.regala_a=".comillas_inteligentes($value,$db).
				" WHERE (pj.id_jugador = ".comillas_inteligentes($key,$db)." AND
				pj.id_partida = ".comillas_inteligentes($id_partida,$db).") LIMIT 1";
	    
	    //echo "$sql";
	    
		$result=consulta_bd($sql);
    }
    
    // actualizar estado partida
    return actualizar_estado_partida($id_partida,2);
	
	
}

function obtener_partida($id_partida){
	// devuelve la PARTIDA
	// FALSE si error o no existe
	
	global $ERROR;
  
    $db=conectar_bd();
    
    if($db==false){
            $ERROR="Hay problemas para acceder a la Base de Datos.";
            return false;
    }
    
	// partidas creadas por mi
	$sql = "SELECT * FROM partida p WHERE p.id_partida=".comillas_inteligentes($id_partida,$db) . " LIMIT 1";
	$result=consulta_bd($sql);
    
    if($result==false){
      $ERROR="Se produjo un error durante la consulta.";
      return false;
    }
    else{
		$num=numero_filas_resultado_bd($result);
     	if($num == 0){ // si no existe... FALSE      
	        return false;
	    }
	    
		$fila = obtener_fila_assoc_bd($result);
		liberar_resultado_bd($result);
    
	    return $fila;
	}

}
?>