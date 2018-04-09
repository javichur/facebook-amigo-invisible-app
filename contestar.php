<?

if(!isset($_REQUEST["a"])){
	?><fb:redirect url="error.php" /><?
	return;
}
if($_REQUEST["a"]!="si" && $_REQUEST["a"]!="no"){
	?><fb:redirect url="error.php" /><?
	return;
}

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

if($_REQUEST["a"] == "si")
	$accion = 2;
else
	$accion = 3;

include_once LIB_PATH.'acceso_a_red.php';
$ret = actualizar_asistencia_partida($_REQUEST["id_partida"],$user,$accion);

?><fb:redirect url="<?=URL_APP?>listar_partidas.php" /><?
return;

?>