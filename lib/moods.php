<?php
error_log(CLIENT_PATH);
include_once CLIENT_PATH.'facebook.php';

function get_fb() {
  return new Facebook(API_KEY,
                      SECRET_KEY);
}
