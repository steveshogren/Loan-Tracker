<?php

$GLOBALS['testing'] = true;
Zend_Session::start();

function __autoload ($class_name)
{
    include_once '../Autoloader.php';
    $Autoloader = new Autoloader();
    $Autoloader->autoload($class_name);
}
?>