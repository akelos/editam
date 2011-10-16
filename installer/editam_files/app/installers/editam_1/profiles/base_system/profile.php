<?php

# Author Bermi Ferrer - MIT LICENSE

/**
* Editam setup allows users to decide how their system will look like
* when installing their system. This is carried by the default installer
* instantiating this class and making current installer instance available
* BaseSystemSetup::Installer for you to perform specific actions.
*/
class BaseSystemProfile
{
    public $Installer; // Holds an instance of current installer
    public $priority = 0;
    
    public function _fill_site_details(){
        /*
         * This is used for testing env. only
         */
        if(empty($this->Installer->site_details)){
            echo "\n\n---------------------------------------------------------------------------\n";
            echo "   Todo: \$this->Installer->site_details -> must be filled from setup wizard\n";
            echo "\n\n---------------------------------------------------------------------------\n\n";

            $this->Installer->site_details = array();
            
            $this->Installer->site_details['site_name'] = 'Site Name should be filled from Application Setup Wizard';
            $this->Installer->site_details['administrator_email'] = 'todo@administrator.email.example.com';
            $this->Installer->site_details['administrator_login'] = 'admin';
            $this->Installer->site_details['administrator_password'] = 'admin';
            $this->Installer->site_details['administrator_password_confirmation'] = 'admin';
            
            if(!defined('AK_SITE_URL_SUFFIX')){
                define('AK_SITE_URL_SUFFIX',null);
            }
        }
    }
    
    public function install()
    {
        $this->_createDefaultPreferences();
        $this->Installer->installDataFiles(1);
        $this->_addHtaccessIfRemoved();
        $this->_fixThemeStylesheet();
    }

    public function uninstall()
    {

    }

    public function _createDefaultPreferences()
    {
        Ak::import('site_preference');
        $SitePreference = new SitePreference();
        
        if(AK_ENVIRONMENT == 'testing'){
            $this->_fill_site_details();
        }
        
        $Preference = new SitePreference(array(
        'name'=>'site_title',
        'title'=>'Site Title',
        'value' => $this->Installer->site_details['site_name'],
        'is_editable' => true,
        'is_core' => true));
        $Preference->save();

        $version = file_exists(AK_APP_DIR.DS.'version.txt') ? AkFileSystem::file_get_contents(AK_APP_DIR.DS.'version.txt') : false;

        $Preference = new SitePreference(array(
        'name'=>'editam_version',
        'title'=>'Editam Version',
        'value' => empty($version) ? '2.0' : $version,
        'is_editable' => false,
        'is_core' => true));
        $Preference->save();

        $Preference = new SitePreference(array(
        'name'=>'license_key',
        'title'=>'License (Use GPL3 as the license for the Community Edition)',
        'value' => 'GPL3',
        'is_editable' => true,
        'is_core' => true));
        $Preference->save();


        $Preference = new SitePreference(array(
        'name'=>'administrator_email',
        'title'=>'Administrator email',
        'value' => $this->Installer->site_details['administrator_email'],
        'is_editable' => true,
        'is_core' => true));
        $Preference->save();

        $Preference = new SitePreference(array(
        'name'=>'default_controller',
        'title'=>'Default controller',
        'value' => 'page',
        'is_editable' => false,
        'is_core' => true));

        $Preference = new SitePreference(array(
        'name'=>'theme',
        'title'=>'Theme',
        'value' => 'default',
        'is_editable' => true,
        'is_core' => true));

        $Preference->save();
        
        Ak::import('role');
        $Role = new Role();
        if ($Role = $Role->findFirstBy('name', 'user')){
            $Preference = new SitePreference(array(
            'name' => 'new_user_roles',
            'title' => 'Default roles for newly created users',
            'value' => $Role->getId(),
            'is_editable' => true,
            'is_core' => true));
            $Preference->save();
        }

        $Preference = new SitePreference(array(
        'name'=>'time_zone',
        'title'=>'Time zone',
        'value' => 'UTC',
        'is_editable' => true,
        'is_core' => true));

        $Preference->save();

        $Preference = new SitePreference(array(
        'name'=>'site_languages',
        'title'=>'Website languages',
        'value' => join(',',Ak::langs()),
        'is_editable' => true,
        'is_core' => true));

        $Preference->save();
    
    }

    public function _createDefaultAdministratorAccount()
    {
        $this->Admin = new User(array('name'=>'Administrator', 'email' => $this->Installer->site_details['administrator_email'], 'login' => $this->Installer->site_details['administrator_login'], 'password' => $this->Installer->site_details['administrator_password'], 'password_confirmation' => $this->Installer->site_details['administrator_password_confirmation'], 'is_enabled' => true, 'is_admin' => true));
        $this->Admin->_byspass_email_validation = true;
        $this->Admin->save();
    }
    
    
    public function _addHtaccessIfRemoved()
    {
        if(!is_file(AK_PUBLIC_DIR.DS.'.htaccess')){
            $htaccess = file_get_contents(AK_APP_DIR.DS.'installers'.DS.'editam_1'.DS.'data'.DS.'htaccess');
            AkFileSystem::file_put_contents(AK_PUBLIC_DIR.DS.'.htaccess', $htaccess);
        }
    }

    public function _fixThemeStylesheet() 
    {
        if(!defined('AK_SITE_URL_SUFFIX')){
            define('AK_SITE_URL_SUFFIX',null);
           }
        $stylesheet = file_get_contents(AK_PUBLIC_DIR.DS.'themes'.DS.'default'.DS.'stylesheets'.DS.'screen.css'); 
        if(strlen(AK_SITE_URL_SUFFIX)>1){ 
            $stylesheet = preg_replace("/url\((\'|\")?\/themes/","url($1/".trim(AK_SITE_URL_SUFFIX,'/')."/themes",  $stylesheet); 
        } 
        file_put_contents(AK_PUBLIC_DIR.DS.'themes'.DS.'default'.DS.'stylesheets'.DS.'screen.css', $stylesheet); 
    }
}

