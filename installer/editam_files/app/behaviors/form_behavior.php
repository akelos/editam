<?php

# Author Bermi Ferrer - MIT LICENSE

defined('AK_EMAIL_REGULAR_EXPRESSION') ? null : define('AK_EMAIL_REGULAR_EXPRESSION',"/^([a-z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-z0-9\-]+\.)+))([a-z]{2,4}|[0-9]{1,3})(\]?)$/i");

class FormBehavior extends BaseBehavior
{
    public $name = 'Form';
    public $description =
    'The form behavior helps you to submit contact forms.
        You just need to create a part named "destination_email" and
        another one named "subject" and this behavior will take care of the rest.';
    
    
    public function init(&$Controller)
    {
        parent::init($Controller);
        $this->observe($this->Page);
    }
    
    public function afterSave()
    {
        $email = empty($this->Controller->params['form_destination_email']) ? false : $this->Controller->params['form_destination_email'];
        $StoredEmail = $this->Page->getPart('destination_email');
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
    
    
    public function isPageVirtual()
    {
        return false;
    }
    
    public function canUsePageCache()
    {
        return false;
    }

    public function renderPage()
    {
        if(!empty($this->Controller->params['form'])){
            $this->sendForm();
        }
        parent::renderPage();
    }

    public function sendForm()
    {
        $post = $this->Controller->params['form'];

        $errors = array();
        if(count($post)){
            $email = $this->getSenderEmail();
            if(empty($email) || !preg_match(AK_EMAIL_REGULAR_EXPRESSION,$email)){
                $errors['email'] = Ak::t("Please insert a valid email.", array(), 'form_behavior');
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

    public function getDestinationEmail()
    {
        $destination = $this->Page->getFilteredPart('destination_email');
        return empty($destination) ?  Editam::settings_for('core', 'administrator_email') : $destination;
    }

    public function getSubject()
    {
        $subject = $this->Page->getFilteredPart('subject');
        return empty($subject) ?  Ak::t('[Form Request] %site', array('%site'=>Editam::settings_for('core', 'site_title')), 'form_behavior') : $subject;
    }

    public function getSenderEmail()
    {
        return empty($this->Controller->params['form']['email']) ? '' : $this->Controller->params['form']['email'];
    }

    public function getSenderName()
    {
        return empty($this->Controller->params['form']['name']) ? '' : $this->Controller->params['form']['name'];
    }

    public function getFromHeader()
    {
        $sender_name = $this->getSenderName();
        $email = $this->getSenderEmail();
        return preg_replace('/(\n|\r)*/','', 'From: '.(empty($sender_name) ? $email : $sender_name)." <{$email}>");
    }
    
    public function enable_behavior_html(&$Controller)
    {
        $Controller->FormBehavior = $this;
        $this->init($Controller);
        return $Controller->renderTemplate(AkFileSystem::file_get_contents(AK_APP_DIR.DS.'behaviors'.DS.'form'.DS.'edit.tpl'));
    }
    
    public function disable_behavior_html()
    {
    }
}

