<?php

# Author Bermi Ferrer - MIT LICENSE

class Editam_PreferencesController extends EditamController
{
    public $models = 'site_preference, user, role';
    public $admin_only = true;
    
    public $app_helpers = 'admin,layout,editags,preferences';
    
    public $admin_selected_tab = 'CMS';
    
    public $controller_menu_options = array(
    'Pages'   => array('id' => 'page', 'url'=>array('controller'=>'page', 'action'=>'listing', 'module'=>'editam')),
    'Layouts'   => array('id' => 'content_layout', 'url'=>array('controller'=>'content_layout', 'module'=>'editam')),
    'Snippets'   => array('id' => 'snippet', 'url'=>array('controller'=>'snippet', 'action'=>'manage', 'module'=>'editam')),
    'Preferences'   => array('id' => 'preferences', 'url'=>array('controller'=>'preferences', 'action'=>'setup', 'module'=>'editam'))
    );
    public $controller_selected_tab = 'Preferences';

    public function index()
    {
        $this->redirectToAction('setup');
    }

    public function setup()
    {
        $this->menu_options['Updates'] = array('id' => 'updates', 'url'=>array('controller'=>'editam_update'));
        $this->menu_options['Preferences'] = array('id' => 'preferences', 'url'=>array('controller'=>'preferences','action'=>'setup'));

        if($this->Request->isPost() && !empty($this->params['preferences'])){
            $sucess = true;
            $this->SitePreferences = $this->SitePreference->find('all', array('default' => array(), 'conditions' => 'id = '.join(' OR id=',array_keys($this->params['preferences']))));
            foreach ($this->SitePreferences as $Preference){
                if ($Preference->get('is_editable') && isset($this->params['preferences'][$Preference->id])) {
                    $Preference->set('value', $this->params['preferences'][$Preference->id]);
                    $Preference->save();
                    $sucess = $Preference->hasErrors() ? false : $sucess;
                }
            }
            if ($sucess){
                unset($_SESSION['__preferences']);
                $this->flash_options = array('seconds_to_close' => 10);
                $this->flash['message'] = $this->t('Editam preferences updated');
            }else{
                $this->flash['error'] = $this->t('Could not update "ALL" your Editam preferences. Please correct existing errors.');
            }
            $this->redirectTo(array('action' => 'setup'));
        }else{
            $this->SitePreferences = $this->SitePreference->findAllBy('is_core', true, array('default'=>array()));
            $this->Roles = $this->Role->find('all', array('default'=>array()));
        }
    }
}

