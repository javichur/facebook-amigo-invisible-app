<?php


// increment these when you change css or js files
define('CSS_VERSION', '20');
define('JS_VERSION',  '28');

function render_bool__($res) {
  if ($res) {
    return 'true';
  } else {
    return 'false';
  }
}


/**
 * Render the dashboard and header for a page
 *
 * @param  string $selected The tab that is currently selected
 * @return void        Or a type, with a description here
 * @author
 */

function render_header($selected ='inicio') {
  //$header = '<link rel="stylesheet" type="text/css" href="'.ROOT_LOCATION.'/css/page.css?id='.CSS_VERSION.'" />';
  //$header .= '<script src="'.ROOT_LOCATION.'/js/base.js?id='.JS_VERSION.'" ></script>';

  $header = '<fb:dashboard/>';

  $header .=
    '<fb:tabs>'
    .'<fb:tab-item title="Inicio"  href="index.php" selected="' . ($selected == 'inicio') .'" />'
	.'<fb:tab-item title="Mis partidas"  href="listar_partidas.php" selected="' . ($selected == 'listar_partidas') .'" />'
	.'<fb:tab-item title="Crear partida"  href="nueva_partida.php" selected="' . ($selected == 'crear_partida') .'" />'
    .'<fb:tab-item title="Recomendar a amigos"  href="recomendar.php" selected="' . ($selected == 'recomendar') . '" />'
    .'</fb:tabs>';
  $header .= '<div id="main_body">';
  return $header;
}

function render_footer__() {
  $footer = '</div>';
  return $footer;

}

 function mostrar_usuarios_con_icono($id_partida){
 
	$usuarios = obtener_usuarios_partida($id_partida);
	if($usuarios == false){
		if($ERROR == ""){
			?>0 usuarios invitados.<?
		}
		else{
			?><fb:error>  <fb:message>Error</fb:message><?=$ERROR?></fb:error><?
		}
	}
	else{
		for($i=0;$i<count($usuarios);$i++){
			$urlImagen = ROOT_LOCATION . "images/";
			if($usuarios[$i]["estado_jugador"]==1){ // no ha contestado
				$urlImagen .= "nsnc.jpg";
				$tit = "No ha confirmado su participacion.";
			}
			elseif($usuarios[$i]["estado_jugador"]==2){ //sí va
				$urlImagen .= "yes.jpg";
				$tit = "Ha confirmado que participa.";
			}
			elseif($usuarios[$i]["estado_jugador"]==3){ // no va
				$urlImagen .= "no.jpg";
				$tit = "Ha dicho que no participa :(";
			}
			?>
			
			<img src="<?=$urlImagen?>" title="<?=$tit?>"> <fb:name uid="<?=$usuarios[$i]["id_jugador"]?>" useyou="false"/><br/><?
		}
	}
 }
 
 function mostrar_relacion_amigos_invisibles($id_partida){
 	$usuarios = obtener_usuarios_partida($id_partida);
	if($usuarios == false){
		if($ERROR == ""){
			?>0 usuarios invitados.<?
		}
		else{
			?><fb:error>  <fb:message>Error</fb:message><?=$ERROR?></fb:error><?
		}
	}
	else{
		?><table style="text-align: left; width: 60%;" border="0" cellpadding="5" cellspacing="0">
  <tbody>
	  <tr>
	      <th style="color: rgb(236, 239, 246); background-color: rgb(59, 89, 152);">Participante</th>
	      <th style="color: rgb(236, 239, 246); background-color: rgb(59, 89, 152);">relaci&oacute;n</th>
	      <th style="color: rgb(236, 239, 246); background-color: rgb(59, 89, 152);">Participante</th>
	    </tr><?
	
	for($i=0;$i<count($usuarios);$i++){
		?><tr>
      <td><fb:profile-pic uid="<?=$usuarios[$i]["id_jugador"]?>" size="thumb" linked="true" /> <br/><fb:name uid="<?=$usuarios[$i]["id_jugador"]?>" useyou="false"/></td>
      <td>regalaba a...</td>
	  <td><fb:profile-pic uid="<?=$usuarios[$i]["regala_a"]?>" size="thumb" linked="true" /> <br/><fb:name uid="<?=$usuarios[$i]["regala_a"]?>" useyou="false"/></td>
    </tr><?
	}
	?>
  </tbody>
</table><?
		
		
		
		
		
		for($i=0;$i<count($usuarios);$i++){
			
		}
	}
 	
 }
 
 ?>