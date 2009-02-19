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

defined('AK_EMAIL_REGULAR_EXPRESSION') ? null : define('AK_EMAIL_REGULAR_EXPRESSION',"/^([a-z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-z0-9\-]+\.)+))([a-z]{2,4}|[0-9]{1,3})(\]?)$/i");

class FormBehaviour extends BaseBehaviour
{
    var $name = 'Form';
    var $description =
    'The form behaviour helps you to submit contact forms.
        You just need to create a part named "destination_email" and
        another one named "subject" and this behaviour will take care of the rest.';
    
    
    function init(&$Controller)
    {
        parent::init($Controller);
        $this->observe($this->Page);
    }
    
    function afterSave()
    {
        $email = empty($this->Controller->params['form_destination_email']) ? false : $this->Controller->params['form_destination_email'];
        $StoredEmail =& $this->Page->getPart('destination_email');
        $existing_email = $StoredEmail ? $StoredEmail->get('content') : false;
        if($email && $email != Editam::settings_for('core', 'administrator_email') && $email != $existing_email){
            if($StoredEmail){
                $StoredEmail->set('content', $email);
            }else{
                $this->Page->part->create(array('name'=>'destination_email', 'content'=>$email));
            }
        }
        return true;
    }
    
    
    function isPageVirtual()
    {
        return false;
    }
    
    function canUsePageCache()
    {
        return false;
    }

    function renderPage()
    {
        if(!empty($this->Controller->params['form'])){
            $this->sendForm();
        }
        parent::renderPage();
    }

    function sendForm()
    {
        $post = $this->Controller->params['form'];

        $errors = array();
        if(count($post)){
            $email = $this->getSenderEmail();
            if(empty($email) || !preg_match(AK_EMAIL_REGULAR_EXPRESSION,$email)){
                $errors['email'] = Ak::t("Please insert a valid email.", array(), 'form_behaviour');
            }
        }
        if(count($post) && count($errors) == 0){
            $message = '';
            foreach ($post as $k=>$v){
                $v = is_array($v) ? implode(', ',$v) : $v;
                $message .= ucfirst(str_replace("_"," ",$k)).": ".stripslashes($v)."\r\n";
            }
            $this->Controller->form_sent = mail($this->getDestinationEmail(), $this->getSubject(),  $message, $this->getFromHeader()."\r\nContent-Type: text/plain;charset=UTF-8;\r\n");
            
            $this->Controller->form_message_contents = $email.' >> '.$this->getDestinationEmail()."\n".$this->getSubject()."\n-------------------\n".$message;
        }
        $this->Controller->form_errors = $errors;
    }

    function getDestinationEmail()
    {
        $destination = $this->Page->getFilteredPart('destination_email');
        return empty($destination) ?  Editam::settings_for('core', 'administrator_email') : $destination;
    }

    function getSubject()
    {
        $subject = $this->Page->getFilteredPart('subject');
        return empty($subject) ?  Ak::t('[Form Request] %site', array('%site'=>Editam::settings_for('core', 'site_title')), 'form_behaviour') : $subject;
    }

    function getSenderEmail()
    {
        return empty($this->Controller->params['form']['email']) ? '' : $this->Controller->params['form']['email'];
    }

    function getSenderName()
    {
        return empty($this->Controller->params['form']['name']) ? '' : $this->Controller->params['form']['name'];
    }

    function getFromHeader()
    {
        $sender_name = $this->getSenderName();
        $email = $this->getSenderEmail();
        return preg_replace('/(\n|\r)*/','', 'From: '.(empty($sender_name) ? $email : $sender_name)." <{$email}>");
    }
    
    function enable_behaviour_html(&$Controller)
    {
        $Controller->FormBehaviour =& $this;
        $this->init($Controller);
        return $Controller->renderTemplate(Ak::file_get_contents(AK_APP_DIR.DS.'behaviours'.DS.'form'.DS.'edit.tpl'));
    }
    
    function disable_behaviour_html()
    {
    }
}

?>