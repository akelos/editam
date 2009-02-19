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

class Editam_LoginController extends EditamController
{
    var $models = 'user';
    var $layout = 'editam_login';

    function index()
    {
        $this->redirectToAction('authenticate');
    }

    function authenticate()
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

    function log_out()
    {
        $this->credentials->revokeCredentials();
        $this->flash_options = array('seconds_to_close'=>5);
        $this->flash['notice'] = $this->t('You have been successfully logged out.');
        unset($_SESSION['_coming_from_url']);
        $this->redirectTo(array('controller' => 'login','action'=>'authenticate'));
    }

    function account_recovery()
    {
        $this->_checkIfAlreadyLogedIn();
        if ($this->Request->isPost()) {

            if (empty($this->params['user']['login']) || empty($this->params['user']['email'])) {
                $this->flash_options = array('seconds_to_close'=>5);
                $this->flash['message'] = $this->t('Please fill at least one field');
                $this->redirectTo(array('controller'=>'login', 'action' => 'user'));

            }elseif ($User =& $this->User->findFirstBy('login OR email', @$this->params['user']['login'], @$this->params['user']['email'])){

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

    function _validateCookies()
    {
        if(!empty($this->params['validate_cookies'])){
            $this->layout = false;
            $this->renderNothing(empty($_SESSION['cookie_check']) ? 400 : 200);
        }
    }

    function _checkIfAlreadyLogedIn()
    {
        if (!empty($_SESSION['__credentials']['id'])) {
            $this->flash_options = array('seconds_to_close'=>5);
            $this->flash['message'] = $this->t('<b>%name</b> you\'re already authentified', array('%name'=>$_SESSION['__credentials']['name']));
            $this->redirectTo(array('controller'=> 'site'));
        }
    }

}

?>
