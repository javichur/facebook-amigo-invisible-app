<?

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
		
if(isset($_REQUEST["ok"])){ // si acaban de añadirse participantes...
	?><fb:success> <fb:message>Nuevos participantes incluidos.</fb:message> 
	Puedes incluir m&aacute;s participantes si quieres. Se ha enviado una notificaci&oacute;n a los nuevos participantes.</fb:success><?
}

if(isset($_REQUEST["ids"])){ // si se han recibido nuevos participantes... almacenar
include_once LIB_PATH.'acceso_a_red.php';
	$ret = es_creador_partida($_REQUEST["id_partida"],$user);
	if($ret != false && $ret["estado_partida"] == 1){ // si es el creador de dicha partida, y la partida está en estado 1

		if(count($_REQUEST["ids"])>200){
			?><fb:error><fb:message>Error</fb:message>No se permite incluir m&aacute;s de 200 participantes. Lo siento :(</fb:error><?
			return false;
		}
		
		incluir_participantes_en_partida($_REQUEST["id_partida"],$_REQUEST["ids"]);
		
		// Mandar notificación a los nuevos participantes.
		require_once(LIB_PATH."lib_notificaciones.php");
		enviar_notificaciones($_REQUEST["ids"],$fb);
		
		// Mandar también emails
		//$fb->api_client->notifications_sendEmail(implode(',',$_REQUEST["ids"]),"Te he invitado a una partida", "Tienes que confirmar si quieres jugar. Visita http://www.apps.facebook.com/prueba_ai para mas informacion", "<b>Tienes que confirmar si quieres jugar. Visita http://www.apps.facebook.com/prueba_ai para mas informacion</b>")
		
		?><fb:redirect url="mas_participantes.php?id_partida=<?=$_REQUEST["id_partida"]?>&ok" /><?	
		
	}
	else{
		?><fb:redirect url="error.php?1" /><?
		return;		
	}
	
	
}
else{ // si aún no se han enviado nuevos participantes...
	include_once LIB_PATH.'acceso_a_red.php';
	// 1. comprobar que la partida fue creada por él.
	$partida = es_creador_partida($_REQUEST["id_partida"],$user);
	if($partida != false && $partida["estado_partida"] == 1){ // si es el creador de dicha partida, y la partida está en estado 1
		
		?><br><br><b>Nombre de la partida:</b> <?=$partida["titulo_partida"]?><br/>
		<b>Descripci&oacute;n:</b> <?=$partida["descripcion_partida"]?></br><br/><?
		
		$usuarios_partida = obtener_usuarios_partida($_REQUEST["id_partida"]);
		for($i=0;$i<count($usuarios_partida);$i++){
			$id_usuarios_partida[$i] = $usuarios_partida[$i]["id_jugador"];			
		}		
		
		?><h2>Escribe los nombre de los nuevos participantes para esta partida.</h2><br/>
		<fb:editor action="mas_participantes.php?id_partida=<?=$_REQUEST["id_partida"]?>" labelwidth="128">
			<fb:editor-custom label="Escribe nuevos participantes:"> 
			<fb:multi-friend-input width="350px" border_color="#8496ba" max="200" include_me="true" exclude_ids="<?=implode(',', $id_usuarios_partida)?>"/>
			</fb:editor-custom>
			<fb:editor-custom>
			Nota: los usuarios ya invitados a la partida no aparecen en la lista.
			</fb:editor-custom>
			<fb:editor-buttonset>
			<fb:editor-button value="Guardar" name="guardar"/>
			<fb:editor-cancel value="cancelar" href="index.php"/>
			</fb:editor-buttonset>
		  </fb:editor>
		  <br/>
		  <h2>Estos son los jugadores ya almacenados en la base de datos de esta partida:</h2><br/><?
		  mostrar_usuarios_con_icono($_REQUEST["id_partida"]);?>
		  <br/><br/>
		  <a href="listar_partidas.php">Volver al listado de partidas</a><?
	}
	else{
		?><fb:redirect url="error.php?2" /><?
		return;					
	}
	
}


?>