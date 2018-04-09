<?php
/*
 * index.php
 *
 */
 
 function mi_boton(){
 ?>
<br />
<br />
		<fb:if-section-not-added section="profile">
			<fb:error>  <fb:message>Recuerda! Pincha aqu&iacute; para...</fb:message>
				<fb:add-section-button section="profile" />
			</fb:error>
		</fb:if-section-not-added>
<br />
   <?
 }
 
 /******************************************/
 
include_once 'constants.php';

include_once LIB_PATH.'display.php';

echo render_header();

?><br><h1>Bienvenido a <i>Amigo Invisible</i>.</h1>
<br>
<h2><span style="color: rgb(59, 89, 152);">&iquest;Qu&eacute; es 'El Amigo Invisible'?</span></h2>
<h3>
Es un juego que consiste en lo siguiente: un grupo de amigos se
re&uacute;ne para celebrar algo (fin de curso, navidad, etc). Todos
escriben su nombre en un papel peque&ntilde;o y los meten todos
en un saco. A continuaci&oacute;n, uno por uno, van sacando uno de
esos papelitos (sin ense&ntilde;arle lo que pone a nadie). El
nombre que ponga en el papel ser&aacute; el de la
persona a la que tendr&aacute; que hacer un regalo. El grupo
acordar&aacute; un precio m&aacute;ximo para el regalo, y una
fecha en la que todos volver&aacute;n a reunirse para entregarse
los regalos.</h3><br>
<br>
<h2><span style="color: rgb(59, 89, 152);">&iquest;Qu&eacute; hace esta aplicaci&oacute;n?</span></h2>
<h3>Esta aplicaci&oacute;n para facebook permite crear partidas de
'Amigo invisible' en segundos. Al crear una partida hay que indicar un
nombre, una descripci&oacute;n, y elegir tantos amigos de facebook
como quieras. Todos ellos recibir&aacute;n una
notificaci&oacute;n avis&aacute;ndoles de que les has invitado
a una partida de 'Amigo Invisible'. Todos ellos podr&aacute;n
confirmar si quieren participar o no.<br>
<br>
Cuando el creador de la partida lo crea oportuno, podr&aacute;
realizar el "sorteo autom&aacute;tico" de los nombres. De esta
forma, cada uno de los participantes confirmados, recibir&aacute;n el nombre de la persona
a la que tienen que comprarle un regalo :)</h3>
<br/>

<hr>
<fb:share-button class="meta">
	<meta name="title" content="Aplicacion para jugar al 'AMIGO INVISIBLE'"/>
	<meta name="description" content="Nueva aplicaci&oacute;n para Facebook: 'Amigo Invisible'"/> 
	<link rel="image_src" href="<?=ROOT_LOCATION?>logo.jpg"/> 
	<link rel="target_url" href="<?=URL_APP?>"/> 
</fb:share-button>

