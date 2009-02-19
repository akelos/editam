<?php

// +----------------------------------------------------------------------+
// Editam is a content management platform developed by Akelos Media, S.L.|
// Copyright (C) 2006 - 2007 Akelos Media, S.L.                           |
//                                                                        |
// This program is free software; you can redistribute it and/or modify   |
// it under the terms of the GNU General Public License version 3 as      |
// published by the Free Software Foundation.                             |
//                                                                        |
// This program is distributed in the hope that it will be useful, but    |
// WITHOUT ANY WARRANTY; without even the implied warranty of             |
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                   |
// See the GNU General Public License for more details.                   |
//                                                                        |
// You should have received a copy of the GNU General Public License      |
// along with this program; if not, see http://www.gnu.org/licenses or    |
// write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth |
// Floor, Boston, MA 02110-1301 USA.                                      |
//                                                                        |
// You can contact Akelos Media, S.L. headquarters at                     |
// C/ Pasodoble Amparito Roca, 6, 46240 - Carlet (Valencia) - Spain       |
// or at email address contact@akelos.com.                                |
//                                                                        |
// The interactive user interfaces in modified source and object code     |
// versions of this program must display Appropriate Legal Notices, as    |
// required under Section 5 of the GNU General Public License version 3.  |
//                                                                        |
// In accordance with Section 7(b) of the GNU General Public License      |
// version 3, these Appropriate Legal Notices must retain the display of  |
// the "Powered by Editam" logo. If the display of the logo is not        |
// reasonably feasible for technical reasons, the Appropriate Legal       |
// Notices must display the words "Powered by Editam".                    |
// +----------------------------------------------------------------------+

class Editam_PreferencesController extends EditamController
{
    var $models = 'site_preference, user, role';
    var $admin_only = true;
    
    var $app_helpers = 'admin,layout,editags,preferences';

    function index()
    {
        $this->redirectToAction('setup');
    }

    function setup()
    {
    	/*
    	 * @todo : replace $this->credentials->is_admin 
		*/
//        if(empty($this->credentials->is_admin)){
//            $this->flash['error'] = $this->t('You can\'t manage this site preferences unless '.
//            'you have administration rights. Please contact this site admin if you '.
//            'where suposed to be able to perform selected action.');
//            $this->redirectTo(array('controller'=>'page'));
//        }

        $this->menu_options['Updates'] = array('id' => 'updates', 'url'=>array('controller'=>'editam_update'));
        $this->menu_options['Preferences'] = array('id' => 'preferences', 'url'=>array('controller'=>'preferences','action'=>'setup'));

        if($this->Request->isPost() && !empty($this->params['preferences'])){
            $sucess = true;
            $this->SitePreferences =& $this->SitePreference->find('all', array('conditions' => 'id = '.join(' OR id=',array_keys($this->params['preferences']))));
            foreach (array_keys($this->SitePreferences) as $k){
                $Preference =& $this->SitePreferences[$k];
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
            $this->SitePreferences =& $this->SitePreference->findAllBy('is_core', true);
            $this->Roles =& $this->Role->find('all');
        }
    }
}

?>