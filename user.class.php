<?php

/********************
* TUser is the definition of a user  
* @package user                     
*
*****************************/
class TDBUser extends TEntity
{
  public $entity_type = 'user';
}
class user extends baseClass
{
 public $id = 0;
 public $username = 'public';
 public $password = null;
 public $groups = array('public');
 public $GUID = '';
 
/**
* Return true is $group is in $this->groups
* example : $regestry->user->auth('admin');
* @param string $group 
* @return boolean 
*/
 function auth($group)
   {
    return in_array(  $group,$this->groups);
   }
 function logout()
 {
       setcookie('sdfkjasdvisdgff', false, time()+60*60*24*365, '/');
  
 }  

}