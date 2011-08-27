<?php
  // make sure session is started
 global $registry;
/// make sure same user agent
if ($registry->session->HTTP_USER_AGENT != md5($_SERVER['HTTP_USER_AGENT'])) {
 // session_regenerate_id(true);
  $session_name = md5($_SERVER['HTTP_USER_AGENT']);
  $registry->session->$session_name = '';
}
 //set user agaent
$registry->session->HTTP_USER_AGENT = md5($_SERVER['HTTP_USER_AGENT']);


if (isset($_GET['logout']))
  {

    session_regenerate_id(true);
            setcookie('mjhgbjhg874', '', false, '/' );
            setcookie('nbkghvbgfvbys45', '', false, '/' );
  $registry->session->HTTP_USER_AGENT = '';
  $session_name = md5($_SERVER['HTTP_USER_AGENT']);
  $registry->session->$session_name = '';
  session_destroy();

  DCore::redirect('');
  }
/********************
* TUser is the definition of a user  
* @package user                     
*
*        The db class must have these two functions
*         db->loginUsername($username,$password,$userobject);
*         db->loginHash($md5,$userobject);
*****************************/


class user extends baseClass
{
 public $id = 0;
 public $username = 'public';
 public $password = null;
 public $groups = array('public');
 public $db = null;
 public $title = 'Guest';
 public $hash = '';


 function init()
 {
   $session_name = md5($_SERVER['HTTP_USER_AGENT']);

  $this->hash =  $this->registry->session->$session_name; 
  if ( !empty($this->hash ))
   $this->db->loginHash($this->hash,$this);
 }
 function loginUsername($username,$password,$remember= false)
 {
    session_regenerate_id(true);
    $valid = $this->db->loginUsername($username,$password,$this);
    $session_name = md5($_SERVER['HTTP_USER_AGENT']);

    $this->registry->session->$session_name =   $this->hash;
  return  $valid;
 }
 
 function loginCookie($cookie)
 {

 }
 function loginMd5($md5)
 {
  $this->db->loginHash($md5,$this);
 }

  function check($group)
 {

  $usergroups = $this->groups;
  if (in_array($group,$usergroups))
       return true;
  return false;
 }
 static function title()
 {
 return $this->title;
 }
 static function user_id()
 {
 return $this->id;
 }
 static function noAuth()
 {
 die('need todo noAuth()');
 }
 function owned($user_id ,$group= 'staff')
 {
      if ((!$this->check($group)) and ($user_id <> $this->user_id())) {
            $this->noAuth();
        }
 }
  function IsCurrentUser($client_id)
 {
    return ($this->user_id() == $client_id);
 }
  function gate($group= 'staff')
 {
      if (!$this->check($group)) {
            $this->noAuth();
        }
 }

}

