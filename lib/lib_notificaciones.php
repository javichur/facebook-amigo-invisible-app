<?

function enviar_notificaciones($participantes,$fb){

$fb->api_client->notifications_send( implode(',',$participantes), " acaba de invitarte a una <a href='".URL_APP."'>partida de amigo invisible</a>","user_to_user");
}

function enviar_notificaciones_sorteo_realizado($id_usuarios_confirmados,$fb){
	$fb->api_client->notifications_send( implode(',',$id_usuarios_confirmados), " acaba de hacer el sorteo de la <a href='".URL_APP."'>partida de amigo invisible</a> que cre&oacute;","user_to_user");		
}

/******************************************************************************/
?>