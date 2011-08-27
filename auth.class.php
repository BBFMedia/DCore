<?php

// make sure session is started
if(session_id() == '')
  session_start();
/// make sure same user agent
if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])) {
  session_regenerate_id(true);
  $session[md5($_SERVER['HTTP_USER_AGENT'])] = '';
}
 //set user agaent
$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);


if (isset($_GET['logout']))
  {

    session_regenerate_id(true);
            setcookie('sl1295874', '', false, '/' );
            setcookie('sl1sdrt4vbys45', '', false, '/' );
        $_SESSION['HTTP_USER_AGENT'] = '';
       $session[md5($_SERVER['HTTP_USER_AGENT'])] = '';
  session_destroy();

  OpCore::redirect('');
  }



global $userMap,$authlist;

$userMap = array('public' => 0,'client'=> 5,'staff' =>20 , 'sales'=>25,'ops'=>30,'accounting'=>30,'manager'=>35,'admin'=>50);
$authlist = array(
                  'login:*'=> 'public',
                  'Application.pages.mod.clientview.*'=>'staff');
class auth
{

 private static $instance = NULL;

static function inst()
 {
  if (!self::$instance)
    {
    self::$instance = new user();

    }
    return  self::$instance;
 }

static function loginUsername($username,$password,$remember= false)
 {
   session_regenerate_id(true);
  $valid = self::$instance->db->loginUsername($username,$password,self::$instance);
  $_SESSION[md5($_SERVER['HTTP_USER_AGENT'])] =   self::$instance->hash;
  return  $valid;
 }
static function loginCookie($cookie)
 {

 }
static function loginMd5($md5)
 {
  self::inst()->db->loginHash($md5,self::$instance);
 }

 static function check($group)
 {

  $usergroups = self::inst()->groups;
  if (in_array($group,$usergroups))
       return true;
  return false;
 }
 static function title()
 {
 return self::inst()->title;
 }
 static function client_id()
 {
 return self::inst()->id;
 }
 static function noAuth()
 {
 die('need todo noAuth()');
 }
 static function owned($user_id ,$group= 'staff')
 {
      if ((!self::check($group)) and ($user_id <> self::id())) {
            self::noAuth();
        }
 }
 static function IsCurrentUser($client_id)
 {
    return (self::id() == $client_id);
 }
 static function gate($group= 'staff')
 {
      if (!self::check($group)) {
            self::noAuth();
        }
 }
static function startSession($database)
 {

  self::inst()->db = $database;
 // uses user agent to double up the naming safe
 if (!empty($_SESSION[md5($_SERVER['HTTP_USER_AGENT'])] ))
    {
    self::loginMd5($_SESSION[md5($_SERVER['HTTP_USER_AGENT'])]);
    $_SESSION[md5($_SERVER['HTTP_USER_AGENT'])] =   self::inst()->hash;
    }
 }
}

