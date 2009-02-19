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

class SystemMessage extends ActiveRecord
{
    var $belongs_to = 'user';

    function validate()
    {
        $this->validatesPresenceOf('value');
    }

    function beforeSaveOnCreate()
    {
        empty($this->message_key) ? $this->set('message_key', Ak::uuid()) : null;
        return true;
    }

    function addMessagesToController(&$Controller)
    {
        if($UserMessages = $this->findAllBy('user_id AND has_been_readed', $Controller->credentials->id, 0, array('order'=>'created_at DESC'))){

            foreach (array_keys($UserMessages) as $k){
                if(!$UserMessages[$k]->hasExpired($Controller)){
                    $UserMessages[$k]->addToController($Controller);
                }
            }
        }
    }

    function hasExpired()
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

    function addToController(&$Controller)
    {
        $Controller->flash_now[$this->get('message_key')] = $this->get('value').' '.
        $this->getDontShowAgainLink();
    }
    
    function getDontShowAgainLink()
    {
        if($this->get('can_be_hidded')){
            $url = Ak::toUrl(array('controller'=>'system_message', 'action' => 'dont_show_again', 'id'=>$this->getId(), 'url' => AK_CURRENT_URL));
            $onclick = defined('AVOID_USING_AJAX_ON_SYSTEM_MESSAGES') ? '' : " onclick=\"new Ajax.Request(this.href, {onSuccess:function(request){if($('flash').childNodes.length == 1){new Effect.Fade('flash');}else{new Effect.Fade('flash_editam_update_pending')}}});return false;\"";
        return ' <a href="'.$url.'" class="information alternative"'.$onclick.'>('.$this->t('Dont show again').')</a>';
        }

    }

    function registerMessageForAdmins($attributes = array())
    {
        Ak::import('user');
        $User =& new User();
        $this->transactionStart();
        if($Admins =& $User->findAllBy('is_admin AND is_enabled', true, true, array('include'=>'system_messages'))){
            foreach (array_keys($Admins) as $k) {
                if(empty($attributes['allow_repeated']) && 
                !$this->isUserAwareOfMessage($Admins[$k], @$attributes['message_key'])){
                    $Message =& new SystemMessage(array_merge(array('user_id'=>$Admins[$k]->id), $attributes));
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

    function unregisterMessageForAdmins()
    {
        if($Messages =& $this->findAllBy('message_key', $this->message_key)){
            foreach (array_keys($Messages) as $k) {
                $Messages[$k]->destroy();
            }
        }
    }

    function isUserAwareOfMessage(&$User, $message_key)
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

?>
