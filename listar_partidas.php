<?php
/*
 * listar_partidas.php
 *
 */
 
 function mostrar_acciones_creador($id_partida, $estado){
	if($estado==1){ // esperando confirmación de los invitados...
		?><a href="mas_participantes.php?id_partida=<?=$id_partida?>">A&ntilde;adir m&aacute;s participantes</a>.<br>
		<a href="sortear.php?id_partida=<?=$id_partida?>">Sortear amigos invisibles</a>.<?
	}
	else if($estado==2){ // ya se ha hecho el sorteo...
		?><a href="cerrar_partida.php?id_partida=<?=$id_partida?>">Cerrar partida (todos podr&aacute;n ver qui&eacute;n regalaba a qui&eacute;n).</a>.<?
	}
	else if($estado == 3){
		?><a href="ver_relacion.php?id_partida=<?=$id_partida?>">Ver qui&eacute;n regalaba a qui&eacute;n.</a><?
	}
	
 
 }
 
 function mostrar_acciones_participante($estado_partida, $estado_jugador, $id_partida,$regala_a){
 
	//echo "estado partida: $estado_partida <br> estado jugador: $estado_jugador";
	
	if($estado_partida==1){ // esperando confirmación de los participantes...
		if($estado_jugador == 1 || $estado_jugador == 3){ // si el jugador no ha elegido aún o ha dicho que no...
			?><a href="contestar.php?id_partida=<?=$id_partida?>&a=si">Voy a ir</a>.<br/><?			
		}
		if($estado_jugador == 1 || $estado_jugador == 2){ // si el jugador no ha elegido aún o ha dicho que Sí...
			?><a href="contestar.php?id_partida=<?=$id_partida?>&a=no">No voy a ir</a>.<br/><?			
		}	
	}
	
	if($estado_partida == 2){ // si ya se ha realizado el sorteo...
		if($estado_jugador == 2){ // ...y tú juegas...
			?>Tienes que regalar a <a href="http://www.facebook.com/profile.php?id=<?=$regala_a?>" target="_blank">esta</a> persona.<br/><?	
		}
		else{ // si no juegas...
			?><i>(no est&aacute;s jugando. No confirmaste tu participaci&oacute;n :( )</i><?			
		}
	}
	
 	if($estado_partida == 3){
		?><a href="ver_relacion.php?id_partida=<?=$id_partida?>">Ver qui&eacute;n regalaba a qui&eacute;n.</a><?
	}
 
 }
 
 function mostrar_tabla_partidas($p,$rol,$user){
 	// rol = "creador" o "participante"
 
 ?><table style="text-align: left; width: 100%;" border="0" cellpadding="5" cellspacing="0">
  <tbody>
    <tr>
      <th style="width: 20%; color: rgb(236, 239, 246); background-color: rgb(59, 89, 152);">T&iacute;tulo</th>
      <th style="width: 15%; color: rgb(236, 239, 246); background-color: rgb(59, 89, 152);">Creador</th>
      <th style="width: 10%; color: rgb(236, 239, 246); background-color: rgb(59, 89, 152);">Creaci&oacute;n</th>
	  <th style="width: 20%; color: rgb(236, 239, 246); background-color: rgb(59, 89, 152);">Participantes</th>
	  <th style="width: 15%; color: rgb(236, 239, 246); background-color: rgb(59, 89, 152);">Estado</th>
	  <th style="width: 20%; color: rgb(236, 239, 246); background-color: rgb(59, 89, 152);">Acciones</th>
    </tr><?
	
	for($i=0;$i<count($p);$i++){
		?><tr>
      <td><b><?=$p[$i]["titulo_partida"]?>.</b><br/><?=$p[$i]["descripcion_partida"]?>.</td>
      <td><fb:profile-pic uid="<?=$p[$i]["id_creador"]?>" size="thumb" linked="true" /> <br/><fb:name uid="<?=$p[$i]["id_creador"]?>" useyou="false"/></td>
      <td><?=$p[$i]["fecha_creacion"]?></td>
	  <td><?mostrar_usuarios_con_icono($p[$i]["id_partida"]);?></td>
	  <td><?=$p[$i]["texto_estado_partida"]?>.</td>
      <td><?if($rol=="creador"){
				mostrar_acciones_creador($p[$i]["id_partida"],$p[$i]["estado_partida"]);
			}
			else{
				mostrar_acciones_participante($p[$i]["estado_partida"], $p[$i]["estado_jugador"], $p[$i]["id_partida"],$p[$i]["regala_a"]);
			}?>
	  </td>
    </tr><?
	}
	?>
  </tbody>
</table><?
 }
 
 /*------------------------------------------------------------------------------------------------------*/
include_once 'constants.php';
include_once LIB_PATH.'moods.php';
include_once LIB_PATH.'display.php';

include_once LIB_PATH.'acceso_a_red.php';


$fb = get_fb();
$user = $fb->require_login();

echo render_header("listar_partidas");

?><br>
<h2>Listado de partidas del juego <i>Amigo Invisible</i></h2>
<p>Desde esta p&aacute;gina puedes <a href="#creadas_por_mi">administrar las partidas que has
creado</a> (a&ntilde;adir jugadores, realizar el sorteo, ...) y
<a href="#participo_en">participar en las partidas a las que te han invitado</a> (confirmar mi
asistencia, ver a qui&eacute;n tengo que hacer el regalo, etc).
</p><br/>
<hr>

<a name="creadas_por_mi"></a><h3>Partidas creadas por mi:</h3><br>
<?
$partidas = obtener_partidas_creadas($user);
if($partidas == false){
	if($ERROR == ""){
		?><fb:success>
			<fb:message>Aviso:</fb:message>
			Todav&iacute;a no has creado ninguna partida. <a href="nueva_partida.php">Pincha aqu&iacute;</a> para crear una nueva partida.
		</fb:success><?
	}
	else{
		?><fb:error>  <fb:message>Error</fb:message><?=$ERROR?></fb:error><?
	}
}
else{ // si hay partidas creadas por mi...
	
	mostrar_tabla_partidas($partidas, "creador",$user);
}

?>
<br/><hr>
<a name="participo_en"></a><h3>Partidas en las que participo:</h3><br><?
$partidas = obtener_partidas_participo($user);
if($partidas == false){
	if($ERROR == ""){
		?><fb:success>
			<fb:message>Aviso:</fb:message>
			Todav&iacute;a no participas en ninguna partida.
		</fb:success><?
	}
	else{
		?><fb:error>  <fb:message>Error</fb:message><?=$ERROR?></fb:error><?
	}
}
else{ // si hay partidas en las que participo...
	mostrar_tabla_partidas($partidas, "participante",$user);
}

?>