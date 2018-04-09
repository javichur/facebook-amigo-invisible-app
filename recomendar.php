<?php
/*
 * recomendar.php
 */
include_once 'constants.php';
include_once LIB_PATH.'moods.php';
include_once LIB_PATH.'display.php';

$invite_href = "recomendar.php"; // Rename this as needed 

$fb = get_fb();
$user = $fb->require_login();

echo render_header("recomendar");

if(isset($_POST["ids"])) { 
	echo "<center>Gracias por invitar a ".sizeof($_POST["ids"])." de tus amigos a <b><a href=\"".URL_APP."\"><fb:application-name /></a></b>.<br><br>\n</center>"; 
} 
else { // Retrieve array of friends who've already added the app. 
	$fql = 'SELECT uid FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1='.$user.') AND is_app_user = 1'; 
	$_friends = $fb->api_client->fql_query($fql); 
	
	// Extract the user ID's returned in the FQL request into a new array. 
	$friends = array();
	if (is_array($_friends) && count($_friends)) { 
		foreach ($_friends as $friend) { 
			$friends[] = $friend['uid']; 
		}
	} 
	
	// Convert the array of friends into a comma-delimeted string. 
	$friends = implode(',', $friends); 
	
	// Prepare the invitation text that all invited users will receive. 
	$content = 
		"<a href=\"".URL_APP."\"><fb:application-name /></a> ".DESCRIPCION."\n". 
		"<fb:req-choice url=\"".$fb->get_add_url()."\" label=\"Incluir ".NAME_APP." en tu perfil\"/>"; 
		
?> 
<fb:request-form 
	action="<? echo $invite_href; ?>" 
	method="post" 
	type="<?=NAME_APP?>" 
	content="<? echo htmlentities($content); ?>" 
	image="<?=URL_LOGO?>"> 
	
	<fb:multi-friend-selector 
		actiontext="Estos son los amigos que a&uacute;n no utilizan <?=NAME_APP?>. Inv&iacute;talos! Es gratis! :P" 
		bypass="cancel" 
		exclude_ids="<? echo $friends; ?>" /> 
</fb:request-form> 
<? 
} 
?>