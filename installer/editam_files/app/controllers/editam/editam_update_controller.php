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

class Editam_EditamUpdateController extends EditamController
{
    var $admin_only = true;

    function index ()
    {
        $this->flash_now['editam_update_pending'] = '';
        $this->menu_options['Updates'] = array('id' => 'editam_update', 'url'=>array('controller'=>'editam_update'));
        if($this->update_details = $this->EditamUpdate->getUpdateDetailsIfAvailable()){
            if($this->EditamUpdate->canPerformCleanUpdate($this->update_details['update'])){
                $this->renderAction('confirm_update');
            }else{
                $this->renderAction('solve_conflicts');
            }
        }
    }

    function confirm_update ()
    {
        if($this->Request->isPost() && @$this->params['update']['confirm'] == 1){
            $this->renderAction('perform_update');
        }
    }

    function perform_update ()
    {
        $this->flash_now['editam_update_pending'] = '';
        @set_time_limit(0);
        if(empty($this->params['update']['confirm'])){
            $this->flash['error'] = $this->t('You need to confirm that you have made a backup up your system and that are aware of the risk that this automated update has before updating Editam.');
            $this->redirectToAction('index');
        }

        if(!empty($this->params['update']['conflicts'])){
            $this->EditamUpdate->setConflictResolutions($this->params['update']['conflicts']);
        }
        if(empty($this->params['update']['from']) || 
        empty($this->params['update']['to']) || 
        !$this->EditamUpdate->update($this->params['update']['from'], $this->params['update']['to'])){
            $this->flash['error'] = $this->t('There were problems during the update process. Please try again');
            $this->redirectToAction('index');
        }else{
            Ak::import('SystemMessage');
            $SystemMessage = new SystemMessage(array('message_key' => 'editam_update_pending'));
            $SystemMessage->unregisterMessageForAdmins();
        }
    }

    function solve_conflicts ()
    {
        $this->conflicts = $this->EditamUpdate->getConflicts();
    }
}

?>
