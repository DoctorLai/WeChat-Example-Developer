<?php
// https://helloacm.com

define('APPID', 'APPID');
define('APPSECRET', 'APPSECRET');
define('TOKENFILE', 'token.txt'); 
define('EXPIRY', 'expiry.txt');
 
date_default_timezone_set('UTC');
 
function getToken() {
  $tokenfile = TOKENFILE;
  $expiryfile = EXPIRY;
  if (is_file($tokenfile)) {
    $token = trim(file_get_contents($tokenfile));
  }
  if ($token && is_file($expiryfile)) {
    $x = (integer)trim(file_get_contents($expiryfile));
    if ($x > 0) {
      if (time() < $x) { // token still valid
        return $token;
      } 
    }  
  } 
  // invoke wechat API if token is not found or has expired
  $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=" . APPSECRET;
  $data = trim(file_get_contents($url)); 
  if ($data) {
    $data = json_decode($data, true);
    if ($data) {
      $token = $data['access_token'];
      $expiry = time() + (integer)$data['expires_in'];
      // save for cache until expiry
      file_put_contents($tokenfile, $token, LOCK_EX);
      file_put_contents($expiryfile, $expiry, LOCK_EX);
      return $token;
    }
  }
  // can't get valid token
  return ""; 
}
