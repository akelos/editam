<?php

# Author Bermi Ferrer - MIT LICENSE

class SystemMessage extends ActiveRecord
{
    public $belongs_to = 'user';

    public function validate()
    {
        $this->validatesPresenceOf('value');
    }

    public function beforeSaveOnCreate()
    {
        empty($this->message_key) ? $this->set('message_key', Ak::uuid()) : null;
        return true;
    }

    public function addMessagesToController(&$Controller)
    {
        if($UserMessages = $this->findAllBy('user_id AND has_been_read', $Controller->credentials->id, 0, array('order'=>'created_at DESC', 'default'=>false))){

            foreach (array_keys($UserMessages) as $k){
                if(!$UserMessages[$k]->hasExpired($Controller)){
                    $UserMessages[$k]->addToController($Controller);
                }
            }
        }
    }

    public function hasExpired()
    {
        if(!empty($this->seconds_to_expire)){
            $created_at_ts = Ak::getTimestamp($this->get('created_at'));
            $now_ts = Ak::getTimestamp();
            if($now_ts > $created_at_ts + $this->seconds_to_expire){
                $this->destroy();
                return true;
            }
        }
        return false;
    }

    public function addToController(&$Controller)
    {
        $Controller->flash_now[$this->get('message_key')] = $this->get('value').' '.
        $this->getDontShowAgainLink();
    }
    
    public function getDontShowAgainLink()
    {
        if($this->get('can_be_hidded')){
            $url = Ak::toUrl(array('controller'=>'system_message', 'action' => 'dont_show_again', 'id'=>$this->getId(), 'url' => AK_CURRENT_URL));
            $onclick = defined('AVOID_USING_AJAX_ON_SYSTEM_MESSAGES') ? '' : " onclick=\"new Ajax.Request(this.href, {onSuccess:function(request){if($('flash').childNodes.length == 1){new Effect.Fade('flash');}else{new Effect.Fade('flash_editam_update_pending')}}});return false;\"";
        return ' <a href="'.$url.'" class="information alternative"'.$onclick.'>('.$this->t('Dont show again').')</a>';
        }

    }

    public function registerMessageForAdmins($attributes = array())
    {
        $User = new User();
        $this->transactionStart();
        if($Admins = $User->findAllBy('is_admin AND is_enabled', true, true, array('default'=>false, 'include'=>'system_messages'))){
            foreach (array_keys($Admins) as $k) {
                if(empty($attributes['allow_repeated']) && 
                !$this->isUserAwareOfMessage($Admins[$k], @$attributes['message_key'])){
                    $Message = new SystemMessage(array_merge(array('user_id'=>$Admins[$k]->id), $attributes));
                    $Message->save();
                    if($Message->hasErrors()){
                        $this->addErrorToBase($Message->getFullErrorMessages());
                    }
                }
            }
        }
        if($this->hasErrors()){
            $this->transactionFail();
        }
        $this->transactionComplete();
        return !$this->hasErrors();
    }

    public function unregisterMessageForAdmins()
    {
        if($Messages = $this->findAllBy('message_key', $this->message_key, array('default'=>array()))){
            foreach (array_keys($Messages) as $k) {
                $Messages[$k]->destroy();
            }
        }
    }

    public function isUserAwareOfMessage(&$User, $message_key)
    {
        if(!empty($User->system_messages)){
            foreach (array_keys($User->system_messages) as $k){
                if($User->system_messages[$k]->get('message_key') == $message_key){
                    return true;
                }
            }
        }
        return false;
    }
}
