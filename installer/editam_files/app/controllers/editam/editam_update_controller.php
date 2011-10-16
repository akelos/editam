<?php

# Author Bermi Ferrer - MIT LICENSE

class Editam_EditamUpdateController extends EditamController
{
    public $admin_only = true;

    public function listing(){
        $this->redirectToAction('index');
    }
    
    public function index ()
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

    public function confirm_update ()
    {
        if($this->Request->isPost() && !empty($this->params['update']) && !empty($this->params['update']['confirm']) && $this->params['update']['confirm'] == 1){
            $this->renderAction('perform_update');
        }
    }

    public function perform_update ()
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

    public function solve_conflicts ()
    {
        $this->conflicts = $this->EditamUpdate->getConflicts();
    }
}
