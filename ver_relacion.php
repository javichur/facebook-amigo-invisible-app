<?

// ver_relacion.php: para ver quién regaló a quién (sólo si la partida está en estado 3 (cerrado)


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

include_once LIB_PATH.'acceso_a_red.php';

$partida = obtener_partida($_REQUEST["id_partida"]);
if($partida != false && $partida["estado_partida"] == 3){ 
	// si la partida está en estado 3 (CERRADA)
		?><br/><h2>Se est&aacute; mostrando una partida cerrada.</h2><br/>
		<h3>&Eacute;sta es la relaci&oacute;n entre el comprador del regalo (izquierda) y el que lo recibi&oacute; (derecha).</h3><br/>
		
			<b>Nombre de la partida:</b> <?=$partida["titulo_partida"]?><br/>
			<b>Descripci&oacute;n:</b> <?=$partida["descripcion_partida"]?><br/>
			<b>Fecha creaci&oacute;n:</b> <?=$partida["fecha_creacion"]?><br/><br/><?
			
			mostrar_relacion_amigos_invisibles($_REQUEST["id_partida"]);
			?>
			
			<br/><br/><a href="listar_partidas.php">Volver al listado de partidas</a><?
}
else{
	?><fb:redirect url="error.php?1" /><?
	return;		
}


?>