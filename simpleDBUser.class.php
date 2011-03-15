<?php

/********************
* TUser is the definition of a user  
* @package user                     
*
*****************************/
function gatekeeper()
{
    global $registry;
 if (empty($registry->session->user_id) )
     die('not logged in');
}

class simpleDBUser extends user
{
public $user_id;
public $usertable = 'users';
/**
* Return true is $group is in $this->groups
* example : $regestry->user->auth('admin');
* @param string $group 
* @return boolean 
*/

 function __construct($registry) {
      parent::__construct($registry);
      $this->init();
 }
 function init()
 {
        if (!empty($this->registry->session->user_id))
        {
            $this->user_id = $this->registry->session->user_id;

         $sql = 'select * from `'.$this->usertable .'` where id = '.$this->user_id;
         $rs = db::prepare($sql);
         $rs -> execute();
         if(!$rs)
        {
             echo $sql;

             throw new TException('Invalid query: ' . $rs -> errorInfo());
         }


     $arr = $rs -> fetch(PDO :: FETCH_ASSOC);
     $this->username = $arr['username'];
     $this->groups = explode(',',$arr['groups']);
            }
 }
 function login($user,$password)
 {
     unset( $_SESSION['user_id']);
     $sql = 'select id from `'.$this->usertable .'` where username = "'.$user.'" and password = "'.$password.'"';
     $rs = db::prepare($sql);
     $rs -> execute();
     $arr = $rs -> fetch(PDO :: FETCH_ASSOC);
     if (!empty($arr))
     {
     $this->registry->session->user_id = $arr['id'];
     $this->init();
         return   $this->user_id ;
     }
  return false;
 }
 function auth($group)
   {
    return in_array(  $group,$this->groups);
   }
 function logout()
 {
     unset( $_SESSION['user_id']);
  
 }  

}