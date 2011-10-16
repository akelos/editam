<?php

# Author Bermi Ferrer - MIT LICENSE

class Editam_SystemMessageController extends EditamController
{
    public function dont_show_again ()
    {
        if($this->credentials->id == $this->SystemMessage->user_id){
            $this->SystemMessage->set('has_been_readed', true);
            $this->SystemMessage->save();
            if(!empty($this->params['url'])){
                $this->redirectTo($this->params['url']);
            }
        }
        $this->redirectTo(array('controller'=>Editam::settings_for('core', 'default_controller')));
    }
}

