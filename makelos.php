#!/usr/bin/env php
<?php

array_shift($argv);
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

$task = empty($argv) ? false : array_shift($argv);
$task = !in_array($task,get_class_methods('Makelos')) ? 'help' : $task;

include(dirname(__FILE__).str_repeat(DS.'..', 4).DS.'config'.DS.'config.php');

class Makelos
{
    var $repository = 'http://svn.akelos.org/plugins/editam';

    function help()
    {
        echo "\nValid commands are ".join(', ', get_class_methods('Makelos'))."\n";
    }

    function test($options = array())
    {
        system('/usr/bin/env php '.dirname(__FILE__).'/test/editam.php');
    }

    function install()
    {
    	// check for dependencies
    	if(!$this->_dependenciesSatisfied()){
    		exit;
    	}
    	
        $Installer =& $this->_getInstaller();
        $Installer->install();
    }

    function uninstall()
    {
        $Installer =& $this->_getInstaller();
        $Installer->uninstall();
    }

    function &_getInstaller()
    {
        require_once(dirname(__FILE__).DS.'installer'.DS.'editam_installer.php');
        $Installer =& new EditamInstaller();
        return $Installer;
    }

    function connectToDatabase($database_settings)
    {
        $this->_includeDependencies();
        Ak::db($database_settings[AK_ENVIRONMENT]);
    }
    
    function _includeDependencies()
    {
        require_once(AK_LIB_DIR.DS.'Ak.php');
        require_once(AK_LIB_DIR.DS.'AkObject.php');
        require_once(AK_LIB_DIR.DS.'AkInflector.php');
        require_once(AK_LIB_DIR.DS.'AkPlugin.php');
        require_once(AK_LIB_DIR.DS.'AkPlugin/AkPluginManager.php');
        require_once(AK_LIB_DIR.DS.'AkInstaller.php');
        require_once(AK_LIB_DIR.DS.'utils'.DS.'generators'.DS.'AkelosGenerator.php');
    }
    
    function _dependenciesSatisfied(){
    	/*
    	$files = array();
		// -- unfinished : complete check for admin_plugin files (exclude installer) --
    	$files['admin_plugin'] = array(
	    		'app'.DS.'controllers' => array(
	    				'account_controller.php',
	    				'admin_controller.php',
	    				'admin'.DS.'permissions_controller.php',
	    				'admin'.DS.'roles_controller.php',
	    				'admin'.DS.'users_controller.php'
	    			),
	    		'app'.DS.'helpers' => array(
	    				'admin_helper.php',
	    				'admin'.DS.'permission_helper.php',
	    				'admin'.DS.'role_helper.php',
	    				'admin'.DS.'user_helper.php'
	    			),
	    		'app'.DS.'models' => array(
	    				'account_mailer.php',
	    				'extension.php',
	    				'permission.php',
	    				'permission_role.php',
	    				'role.php',
	    				'role_user.php',
	    				'sentinel.php',
	    				'user.php'
	    			),
	    		'app'.DS.'views' => array(
	    				'account'.DS.'logout.tpl',
	    				'account'.DS.'_password_field.tpl',	
	    				'account'.DS.'password_field.tpl',
	    			),
	    		'config' => array('admin.yml')
    	);
		*/
    	
    	// check for admin plugin
    	$result = true;
    	if(!file_exists(AK_BASE_DIR.DS.'app'.DS.'controllers'.DS.'admin')){
    		echo "\nEditam need admin_plugin to be installed first.\nYou can add admin_plugin by running './script/plugin install admin'\n";
    		$result = false;
    	}
    	
    	return $result;
    }
}

$Makelos = new Makelos();
$Makelos->connectToDatabase(@$database_settings);
$Makelos->$task(@$argv);


?>
