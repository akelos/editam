<?php

# Author Bermi Ferrer - MIT LICENSE

class Editam_LoginController extends EditamController
{
    public $models = 'user';
    public $layout = 'editam_login';

    public function index()
    {
        $this->redirectToAction('authenticate');
    }

    public function authenticate()
    {
        $this->_checkIfAlreadyLogedIn();
        $this->_validateCookies();
        $_SESSION['cookie_check'] = Ak::randomString();

        if($this->Request->isPost()){
            $this->user->setAttributes(array('login'=>@$this->params['user']['login']));
            if ($this->credentials->authenticate(@$this->params['user']['login'], @$this->params['user']['password'])) {
                $this->flash['message'] = $this->t('Welcome <b>%name</b>', array('%name' => $this->credentials->get('name')));
                $landing_page = empty($_SESSION['_coming_from_url']) ?
                array('controller'=> Editam::settings_for('core','default_controller')) :
                $_SESSION['_coming_from_url'];

                unset($_SESSION['_coming_from_url']);
                $this->flash_options = array('seconds_to_close'=>5);
                $this->redirectTo($landing_page);
                return;
            }else{
                $this->flash_options = array('seconds_to_close'=>5);
                $this->flash_now['error'] = $this->t('Invalid login or password');
            }
        }elseif(empty($_SESSION['__credentials']['id']) && $this->Request->isAjax()){
            $this->renderText($this->renderPartial('_move_to_authentication'));

        }
    }

    public function log_out()
    {
        $this->credentials->revokeCredentials();
        $this->flash_options = array('seconds_to_close'=>5);
        $this->flash['notice'] = $this->t('You have been successfully logged out.');
        unset($_SESSION['_coming_from_url']);
        $this->redirectTo(array('controller' => 'login','action'=>'authenticate'));
    }

    public function account_recovery()
    {
        $this->_checkIfAlreadyLogedIn();
        if ($this->Request->isPost()) {

            if (empty($this->params['user']['login']) || empty($this->params['user']['email'])) {
                $this->flash_options = array('seconds_to_close'=>5);
                $this->flash['message'] = $this->t('Please fill at least one field');
                $this->redirectTo(array('controller'=>'login', 'action' => 'user'));

            }elseif ($User = $this->User->findFirstBy('login OR email', @$this->params['user']['login'], @$this->params['user']['email'], array('default'=>false))){

                if (!empty($User->email) && Credentials::sendAccountRecoveryMail($User)) {
                    $this->flash['message'] = $this->t('We\'ve sent you the instructions to restore your account to your email address');
                }else{
                    $this->flash['message'] = $this->t('Could not send you the instructions to restore your account. Please contact the admin.');
                }
                $this->flash_options = array('seconds_to_close'=>5);
                $this->redirectToAction('authenticate');
            }else{
                $this->flash_options = array('seconds_to_close'=>5);
                $this->flash['message'] = $this->t('Account not found');
                $this->redirectTo(array('controller'=>'login', 'action' => 'authenticate'));
            }
        }
    }

    public function _validateCookies()
    {
        if(!empty($this->params['validate_cookies'])){
            $this->layout = false;
            $this->renderNothing(empty($_SESSION['cookie_check']) ? 400 : 200);
        }
    }

    public function _checkIfAlreadyLogedIn()
    {
        if (!empty($_SESSION['__credentials']['id'])) {
            $this->flash_options = array('seconds_to_close'=>5);
            $this->flash['message'] = $this->t('<b>%name</b> you\'re already authenticated', array('%name'=>$_SESSION['__credentials']['name']));
            $this->redirectTo(array('controller'=> 'site'));
        }
    }

}
