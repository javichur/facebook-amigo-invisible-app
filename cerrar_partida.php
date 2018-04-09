<?

// cerrar_partida.php


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

if(isset($_REQUEST["cerrar"])){ // si se ha confirmado que se quiere realizar el sorteo...
include_once LIB_PATH.'acceso_a_red.php';
	$ret = es_creador_partida($_REQUEST["id_partida"],$user);
	if($ret != false && $ret["estado_partida"] == 2){ // si es el creador de dicha partida, y la partida está en estado 2
		// (acabamos de comprobar que la partida está en estado 2)
		
		$ret = actualizar_estado_partida($_REQUEST["id_partida"],3);
		if($ret == true){
		
			?><fb:success> <fb:message>Partida cerrada.</fb:message> 
			Se ha cerrado la partida.</fb:success>
			<?
			mostrar_relacion_amigos_invisibles($_REQUEST["id_partida"]);
			?>
			
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
else{ // si aún no se ha confirmado que se quiere hacer el sorteo...
	include_once LIB_PATH.'acceso_a_red.php';
	// 1. comprobar que la partida fue creada por él.
	$partida = es_creador_partida($_REQUEST["id_partida"],$user);
	if($partida != false && $partida["estado_partida"] == 2){ // si es el creador de dicha partida, y la partida está en estado 2	
		
		?><br/><h2>Se va a proceder al cierre de la partida</h2><br/>
		<h3>Esto supondr&aacute; que todos podr&aacute;n ver qui&eacute;n regalaba a qui&eacute;n.</h3><br/>
		<b>Nombre de la partida:</b> <?=$partida["titulo_partida"]?><br/>
		<b>Descripci&oacute;n:</b> <?=$partida["descripcion_partida"]?><br/><br/>
		
		<h3>Estos son los jugadores de la partida.</h3>
		S&oacute;lo se realiz&oacute; el sorteo entre los que confirmaron su participaci&oacute;n (icono verde).<br/><br/><?
		  mostrar_usuarios_con_icono($_REQUEST["id_partida"]);?>
		<br/><br/>
		<fb:editor action="cerrar_partida.php?id_partida=<?=$_REQUEST["id_partida"]?>">			
			<fb:editor-buttonset>
			<fb:editor-button value="cerrar partida" name="cerrar"/>
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