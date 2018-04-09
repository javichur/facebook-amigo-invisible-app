<?php
  /*
   * multiFeedHandler.php - Posting to other's feed form handler
   *
   */

include_once '../constants.php';
include_once LIB_PATH.'moods.php';
include_once LIB_PATH.'display.php';

$fb = get_fb();
$canvas_url = URL_APP;

//if ($_POST['method']=='publisher_getFeedStory') {
	if(!isset($_POST['todos'])) $_POST['todos'] = "...";
	$todos = $_POST['todos'];

  $feed = array('template_id' =>  FEED_STORY_AMIGO,
                'template_data' => array('target_id'       => $todos));

  $data = array('method'=> 'multiFeedStory',
                'content' => array( 'feed'    => $feed,
                                    'next'    => $canvas_url
                                    ));

//}

echo json_encode($data);
