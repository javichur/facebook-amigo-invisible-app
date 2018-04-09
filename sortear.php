<?

function realizar_sorteo($jugadores){
	// devuelve un vector donde cada elemento es de la forma:
	// vector[id_jugador_X] = id_jugador_al_que_regala_X

	$num_jugadores = count($jugadores);
	
	$num_colisiones = 0;
	

	do{ // hasta que se encuentre una soluci�n...
		for($i=0;$i<$num_jugadores;$i++){
			$regala_a[$jugadores[$i]] = rand(0,1000);
		}
		
		asort($regala_a); // ordenamos seg�n los n�s aleatorios
		
		$colision = false;
		
		$i = 0;
		foreach ($regala_a as $key => $value){
			if($key == $jugadores[$i]){
				$colision = true;
				break;
			}	
			$i++;
		}

		$num_colisiones++;
	}while($colision == true);
	
	$i = 0;
	foreach ($regala_a as $key => $value){
			$solucion[$key] = $jugadores[$i];
			$i++;
	}
	
	//echo "$num_colisiones intentos hasta encontrar solucion.";

	return $solucion;
}

/* ------------------------------------------------------ */


if(!isset($_REQUEST["id_partida"])){
	?><fb:redirect url="error.php" /><?
	return;
}
if(!is_numeric($_REQUEST["id_partida"])){
	?><fb:redirect url="error.php" /><?
	return;
}

// si la URL es correcta...

include_once 'constants.php';
include_once LIB_PATH.'moods.php';
include_once LIB_PATH.'display.php';

$fb = get_fb();
$user = $fb->require_login();

echo render_header("otros");

if(isset($_REQUEST["sortear"])){ // si se ha confirmado que se quiere realizar el sorteo...
include_once LIB_PATH.'acceso_a_red.php';
	$ret = es_creador_partida($_REQUEST["id_partida"],$user);
	if($ret != false && $ret["estado_partida"] == 1){ // si es el creador de dicha partida, y la partida est� en estado 1
		// (acabamos de comprobar que la partida est� en estado 1)
		
		// filtrar usuarios confirmados
		$todos_usuarios = obtener_usuarios_partida($_REQUEST["id_partida"]);
		$j=0;
		for($i=0;$i<count($todos_usuarios);$i++){ // obtener usuarios confirmados
			if($todos_usuarios[$i]["estado_jugador"] == 2){
				$id_usuarios_confirmados[$j] = $todos_usuarios[$i]["id_jugador"];
				$j++;
			}			
		}
		
		if(count($id_usuarios_confirmados) < 3){
			?><fb:error><fb:message>Error</fb:message>Hay menos de 3 usuarios que han confirmado su participaci&oacute;n. No tiene sentido realizar el sorteo :(</fb:error><?	
			return;
		}
		
		// SORTEAR
		$resultado_sorteo = realizar_sorteo($id_usuarios_confirmados);
		
		$ret = almacenar_sorteo($_REQUEST["id_partida"], $resultado_sorteo);
		if($ret == true){
			// Mandar notificaci�n a los participantes
			require_once(LIB_PATH."lib_notificaciones.php");
			enviar_notificaciones_sorteo_realizado($id_usuarios_confirmados,$fb);
			
			?><fb:success> <fb:message>Sorteo realizado.</fb:message> 
			Se ha realizado el sorteo entre los participantes que confirmaron su asistencia. 
			Se les ha enviado una notificaci&oacute;n tambi&eacute;n.</fb:success>
			
			<br/><br/><a href="listar_partidas.php">Volver al listado de partidas</a><?
		}
		else{ // si error al conectar con la BDA...
			?><fb:error><fb:message>Error</fb:message>Error al acceder a la Base de Datos. Vuelve a intentarlo m&aacute;s tarde. Gracias.</fb:error><?			
		}
	}
	else{
		?><fb:redirect url="error.php?1" /><?
		return;		
	}
}
else{ // si a�n no se ha confirmado que se quiere hacer el sorteo...
	include_once LIB_PATH.'acceso_a_red.php';
	// 1. comprobar que la partida fue creada por �l.
	$partida = es_creador_partida($_REQUEST["id_partida"],$user);
	if($partida != false && $partida["estado_partida"] == 1){ // si es el creador de dicha partida, y la partida est� en estado 1	
		
		?><br/><h2>Se va a realizar el sorteo del amigo invisible.</h2><br/>
		<b>Nombre de la partida:</b> <?=$partida["titulo_partida"]?><br/>
		<b>Descripci&oacute;n:</b> <?=$partida["descripcion_partida"]?><br/><br/>
		
		<h3>Estos son los jugadores de la partida.</h3>
		S&oacute;lo se realizar&aacute; el sorteo entre los que confirmaron su participaci&oacute;n (icono verde).<br/><br/><?
		  mostrar_usuarios_con_icono($_REQUEST["id_partida"]);?>
		<br/><br/>
		<fb:editor action="sortear.php?id_partida=<?=$_REQUEST["id_partida"]?>">			
			<fb:editor-buttonset>
			<fb:editor-button value="realizar sorteo" name="sortear"/>
			<fb:editor-cancel value="cancelar" href="listar_partidas.php"/>
			</fb:editor-buttonset>
		  </fb:editor>
		  <br/>
		  
		  <a href="listar_partidas.php">Volver al listado de partidas</a><?
	}
	else{
		?><fb:redirect url="error.php?2" /><?
		return;					
	}
	
}


?>