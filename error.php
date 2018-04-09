<?

include_once 'constants.php';
include_once LIB_PATH.'moods.php';
include_once LIB_PATH.'display.php';


$fb = get_fb();
$user = $fb->require_login();

echo render_header("error");

?><fb:error><fb:message>Error</fb:message>La URL es incorrecta. Utiliza los links.</fb:error>