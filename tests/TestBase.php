<?php
class TestBase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        set_include_path(
            get_include_path()
          . PATH_SEPARATOR
          . '/usr/share/php/libzend-framework-php/'
        );
    }
    public function __autoload($class_name)
    {
        include_once '../Autoloader.php';
        $Autoloader = new Autoloader();
        $Autoloader->autoload($class_name);
    }

}