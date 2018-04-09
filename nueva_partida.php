<?

include_once 'constants.php';

function notificar_partida_en_mi_muro($fb){
	
	$tokens = '{"images":[{"src":"'.ROOT_LOCATION. 'images/regalo_thum.jpg", "href":"'.URL_APP.'"}]}';
	
	//$target_ids would be an array of user IDs 
	$body_general = '';
	$fb->api_client->feed_publishUserAction( FEED_STORY_MIO, $tokens , "", $body_general,2);
}

function mostrar_javascript(){
?>
<script type="text/javascript" charset="utf-8"> 

</script>
<?
}

function inicializar_vars(){
	if(!isset($_REQUEST["titulo"]))
		$_REQUEST["titulo"] = "ejemplo: partida amigo invisible navidad 2009";
	if(!isset($_REQUEST["descripcion"]))
		$_REQUEST["descripcion"] = "Si falta alguien por incluir avisadme. &iquest;cu&aacute;ndo quedamos para entregar los regalos?";
}

/******************************************************************************/

function participantes_elegidos_comas(){

	if(isset($_SESSION["participantes"])){
		for($i=0;$i<count($_SESSION["participantes"]);$i++){
			if($i>0) echo ",";
			echo $_SESSION["participantes"][$i];
		}
	}
	else{ // si no hay participantes seleccionados...
		echo $_SESSION["user"];
	}
}

/******************************************************************************/

function mostrar_formulario_nueva($error=""){

if($error != ""){?>
	<fb:error><fb:message>Error</fb:message><?=$error?></fb:error><?
}

inicializar_vars();
?>

<fb:editor action="nueva_partida.php" labelwidth="128">
<fb:editor-text label="T&iacute;tulo" name="titulo" value="<?=$_REQUEST["titulo"]?>" maxlength="128"/>
<fb:editor-textarea label="Descripci&oacute;n" name="descripcion">
<?=$_REQUEST["descripcion"]?>
</fb:editor-textarea>
<fb:editor-custom label="Escribe los participantes:"> 
<fb:multi-friend-input width="350px" border_color="#8496ba" max="200" include_me="true" prefill_ids="<?participantes_elegidos_comas();?>"/>
</fb:editor-custom>
<fb:editor-custom>
Nota: incl&uacute;yete en la lista si quieres participar :)
</fb:editor-custom>
<fb:editor-buttonset>
<fb:editor-button value="Previsualizar" name="previsualizar"/>
<fb:editor-cancel value="cancelar" href="index.php"/>
</fb:editor-buttonset>
</fb:editor>
<?
}

/*  comprobaciones_antes_guardar() 
 *
 * comprueba que los campos existen y tienen un tama�o v�lido.
 *
 * devuelve FALSE si error.
 */
function comprobaciones_antes_guardar(){

	if(!isset($_REQUEST["titulo"]) || !isset($_REQUEST["descripcion"])){
		mostrar_formulario_nueva("Utiliza el formulario y completa todos los campos.");
		return false;
	}
	if(strlen(trim($_REQUEST["titulo"]))==0){
		mostrar_formulario_nueva("Has dejado el t&iacute;tulo en blanco. Escribe algo.");
		return false;
	}
	if(strlen(trim($_REQUEST["titulo"]))>128){
		mostrar_formulario_nueva("El t&iacute;tulo tiene una longitud m&aacute;xima de 128 caracteres. Sintetiza :P");
		return false;
	}
	if(strlen(trim($_REQUEST["descripcion"]))==0){
		mostrar_formulario_nueva("Has dejado la descripci&oacute;n en blanco. Escribe algo.");
		return false;
	}
	if(strlen(trim($_REQUEST["descripcion"]))>255){
		mostrar_formulario_nueva("La descripci&oacute;n tiene una longitud m&aacute;xima de 255 caracteres. Sintetiza :P");
		return false;
	}
	
	$_REQUEST["ids"] = (isset($_REQUEST["ids"]) ? $_REQUEST["ids"] : null);
	if(count($_REQUEST["ids"])>200){
		mostrar_formulario_nueva("No se permiten partidas de m&aacute;s de 200 participantes. Lo siento :(");
		return false;
	}
	if(count($_REQUEST["ids"])<3){
		mostrar_formulario_nueva("Elige al menos 3 participantes. Lo siento :(");
		return false;
	}
	
	return true;
}

/******************************************************************************/

function previsualizar_nueva($user){
	
	// si se llega aqu�, todo correcto
	
	?><fb:success><fb:message>Nueva partida</fb:message>Los datos introducidos son v&aacute;lidos. 
	A continuaci&oacute;n se muestra una previsualizaci&oacute;n de la partida. 
	Si todo es correcto, pulsa en <i>guardar</i>. Si no, puedes volver a la p&aacute;gina de edici&oacute;n.</fb:success><?
	
	// 1. almacenar en hidden los campos.
	?><fb:editor action="nueva_partida.php" labelwidth="1" width="600">
	<input  type="hidden" value="<?=trim($_REQUEST["titulo"])?>" name="titulo">
	<input  type="hidden" value="<?=trim($_REQUEST["descripcion"])?>" name="descripcion">
<?
	// 2. mostrar la previsualizaci�n
	// 3. botones de 'guardar' y 'volver a editar'
	?>
	<h3>T&iacute;tulo:</h3><?=trim($_REQUEST["titulo"])?><br/><br/>
	<h4>Descripci&oacute;n:</h4> <?=trim($_REQUEST["descripcion"])?><br/><br/>
	<h4>Participantes:</h4><br/>
	<?
	for($i=0;$i<count($_REQUEST["ids"]);$i++){
		?><fb:profile-pic uid="<?=$_REQUEST["ids"][$i]?>" size="thumb" linked="true" /> <br/><fb:name uid="<?=$_REQUEST["ids"][$i]?>" useyou="false"/> <br/><br/><?
	}
	
	// guardar participantes en variable sesi�n
	$_SESSION["participantes"] = $_REQUEST["ids"];
	?>
	<fb:editor-buttonset>
	<fb:editor-button value="guardar" name="guardar"/>
	<fb:editor-button value="volver a editar" name="editar"/>
	<fb:editor-cancel value="cancelar" href="index.php"/>
	</fb:editor-buttonset>
	</fb:editor><?
}

/******************************************************************************/

include_once 'constants.php';
include_once LIB_PATH.'moods.php';
include_once LIB_PATH.'display.php';

@session_start();


$fb = get_fb();
$user = $fb->require_login();
$_SESSION["user"] = $user;

echo render_header("crear_partida");

if(isset($_REQUEST["previsualizar"])){
	
	$ret = comprobaciones_antes_guardar();
	if($ret == false) return; // si hubo error en las comprobaciones... fin.
	
	// esto se muestra si correcto
	previsualizar_nueva($user);
}
else if(isset($_REQUEST["guardar"])){
	$_REQUEST["ids"] = $_SESSION["participantes"];
	$ret = comprobaciones_antes_guardar();
	if($ret == false) return;
	
	// si correcto... almacenar en la BDA...
	$val["id_creador"] = $user;
	$val["titulo_partida"] = trim($_REQUEST["titulo"]);
	$val["descripcion_partida"] = trim($_REQUEST["descripcion"]);
	$val["participantes"] = $_SESSION["participantes"];
	
	include_once LIB_PATH.'acceso_a_red.php';
	
	$ret = existe_partida($val["titulo_partida"], $user);
	if($ret == false){
		?><fb:error><fb:message>Error</fb:message>Se produjo un error al insertar la partida en la base de datos. 
		Es posible que ya exista una partida creada por ti y con el mismo nombre. Revisa tus partidas.</fb:error>
		<br /><br />
		<h2><a href="listar_partidas.php">ver partidas</a></h2><?
		return;
	}
	
	$ret = alta_partida($val);
	if($ret === false){ // si error al insertar...
		?><fb:error><fb:message>Error</fb:message>Se produjo un error al insertar la partida en la base de datos. 
		Vuelve a intentarlo m&aacute;s tarde. Gracias :)</fb:error>
		<br />
		
		<a href="javascript:history.back()">Volver</a><?
		
		return;
	}
	else{
	
		require_once(LIB_PATH."lib_notificaciones.php");
		enviar_notificaciones($val["participantes"],$fb);
		
		?><fb:success><fb:message>Nueva partida almacenada correctamente</fb:message>
		La partida se ha guardado y se ha enviado una notificaci&oacute;n a los participantes. Ahora hay que esperar a que ellos confirmen si quieren participar en la partida.</fb:success>
		<br /><br/>
		<h2><a href="listar_partidas.php">ver partidas</a></h2>
		<?
		notificar_partida_en_mi_muro($fb);
	}
}
else{
	
	if(!isset($_SESSION["participantes"])) // a�adirme yo si no hay ning�n participante
		$_SESSION["participantes"][0] = $user;
		
	?><br>
	<h2>Crear una partida nueva de <i>Amigo invisible</i></h2>
	<p>Vas a crear una nueva partida. Elige un t&iacute;tulo, una
descripci&oacute;n, y elige a los amigos que quieres invitar a
participar. Puedes a&ntilde;adirte t&uacute; tambi&eacute;n
en la lista de jugadores.<br>
<br>
M&aacute;s adelante, cuando ellos confirmen que quieren participar
en esta partida, t&uacute; podr&aacute;s realizar el sorteo
autom&aacute;tico (desde la pesta&ntilde;a "mis partidas").
Tras el sorteo, cada jugador recibir&aacute; una
notificaci&oacute;n y sabr&aacute; a qui&eacute;n tiene que
hacer el regalo.
	</p>
	<br><hr><?
	mostrar_formulario_nueva();
}
?>