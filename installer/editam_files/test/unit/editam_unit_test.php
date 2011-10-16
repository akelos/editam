<?php

defined('AK_TEST_DATABASE_ON') ? null : define('AK_TEST_DATABASE_ON', true);
require_once(dirname(__FILE__).'/../fixtures/config/config.php');

class EditamUnitTest extends AkUnitTest
{
    public function test_setup()
    {
        $installer = new EditamInstaller();
        $installer->uninstall();
        $installer->install();    
    }
    
}

