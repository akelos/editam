<?php

defined('AK_TEST_DATABASE_ON') ? null : define('AK_TEST_DATABASE_ON', true);
require_once(dirname(__FILE__).'/../fixtures/config/config.php');

class EditamUnitTest extends AkUnitTest
{
    function test_setup()
    {
        require_once(AK_APP_DIR.DS.'installers'.DS.'editam_installer.php');
        $installer = new EditamInstaller();
        $installer->uninstall();
        $installer->install();    
    }
    
}

