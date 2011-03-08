<?php

class session extends baseClass {
 function __construct($registry) {
        session_start();
        parent::__construct($registry);
 }
public function __set($index, $value)
 {
        $_SESSION[$index] = $value;
 }
 
 public function __get($index)
 {
     if (isset($_SESSION[$index]))
        return  $_SESSION[$index];
     return null;
 } 
}


