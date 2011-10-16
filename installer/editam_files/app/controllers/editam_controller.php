<?php

require_once(AK_MODELS_DIR.DS.'editam.php');
require_once(AK_LIB_DIR.DS.'AkActionController.php');

class EditamController extends ApplicationController
{
    var $app_models = array('user','role','permission','extension');
    var $protect_all_actions = true;
    var $layout = 'admin';
    var $app_helpers = 'admin,layout,editags';

    var $_admin_menu_options = array(
    'Dashboard'   => array('id' => 'dashboard', 'url'=>array('controller'=>'dashboard', 'module' => 'admin'), 'link_options'=>array(
            'accesskey'=>'h',
            'title' => 'general status and information'
    )),
    'Manage Users'   => array('id' => 'users', 'url'=>array('controller'=>'users', 'module' => 'admin'), 'link_options'=>array(
            'accesskey' => 'u',
            'title' => 'add user, change password, manage user settings'
    )),
    'CMS'   => array('id' => 'page', 'url'=>array('controller'=>'page', 'module' => 'editam'), 'link_options'=>array(
            'accesskey' => 'p',
            'title' => 'manage Editam CMS'
    ))
    );
    
    var $admin_menu_options = array();
	
	function __construct()
    {
        $this->site_url = $this->base = rtrim(AK_URL,'/');
        $this->is_multilingual = Editam::isMultilingual();
        $this->title = empty($this->title) ? Editam::settings_for('core','site_title') : $this->title;
        $this->host = AK_HOST;
        $this->lang = Ak::lang();

        $this->referrer = @$_SERVER['HTTP_REFERER'];

        $this->beforeFilter('_loadSettings');
        $this->beforeFilter(array('_instantiateCredentials'=>array('except'=>array('show_page'))));
        $this->beforeFilter(array('_disableLinkPrefetching'=>array('except'=>array('show_page'))));
        $this->beforeFilter(array('_initAdminOptions'=>array('except'=>array('show_page'))));

        $this->_engageHooks();

		/*
    	 * admin_plugin parts -------------------------------------------------
    	 */
		$this->beforeFilter('authenticate');
		/*---------------------------------------------------------------*/
    }
    
    function _loadSettings()
    {
        require_once(AK_MODELS_DIR.DS.'site_preference.php');
        $Preference = new SitePreference();
        Editam::settings_for($Preference->_loadPreferences(), null, true);
		
		/*
    	 * admin_plugin parts -------------------------------------------------
    	 */
		$this->admin_settings = Ak::getSettings('admin');
        return true;
		/*---------------------------------------------------------------*/
		
    }

    function _initAdminOptions()
    {
        $this->selected_tab = empty($this->selected_tab) ?
        AkInflector::pluralize($this->getControllerName()) : $this->selected_tab;
        $this->logo = Editam::settings_for('core','logo');
        $this->title = Editam::settings_for('core','site_title');
        $this->community_edition = Editam::settings_for('core','license_key') == 'MPL';
        
        if(!empty($this->credentials->is_admin) && $this->credentials->get('has_checked_for_updates') !== true){
            $this->_checkForUpdates();
        }
        empty($this->credentials->id) ? null : $this->_loadSystemMessages();
    }

    function _instantiateCredentials()
    {
        require_once(AK_MODELS_DIR.DS.'credentials.php');
        $this->credentials = new Credentials();
        return true;
    }

    function _authenticate()
    {
        if($this->credentials->hasCredentials()){
            if($this->admin_only && empty($this->credentials->is_admin)){
                $this->flash['error'] = Ak::t('You do not have enough privileges to perform the action'.
                ' you requested');
                $this->redirectTo(array('controller'=>'page'));
            }
            return true;
        }else{
            if(@$this->params['controller'] != 'login'){
                $_SESSION['_coming_from_url'] = strstr(AK_CURRENT_URL,'login') ? '' : AK_CURRENT_URL;
            }
            $this->redirectTo($this->urlFor(array('controller'=>'login','action'=>'authenticate')));
            return false;
        }
    }

    /**
     * Avoids link prefectching
     * 
     * Google is essentially clicking every link on the page - including 
     * links like “delete this” or “cancel that.” And to make matters worse, 
     * Google ignores the Javascript confirmations. So, if you have a 
     * “Are you sure you want to delete this?” Javascript confirmation 
     * behind that “delete” link, Google ignores it and 
     * performs the action anyway
     */
    function _disableLinkPrefetching()
    {
        if(isset($this->Request->env["HTTP_X_MOZ"]) && $this->Request->env["HTTP_X_MOZ"] == 'prefetch'){
            $this->renderNothing(403);
            return false;
        }
        return true;
    }

    function getUrlizedControllerName()
    {
        return AkInflector::urlize($this->getControllerName());
    }

    function defaultUrlOptions()
    {
        return $this->is_multilingual ? array('lang'=> Ak::lang()) : null;
    }

    function _enableCache()
    {
        if(EDITAM_CACHE_ENABLED && empty($_POST) && empty($_SESSION['__credentials'])){
            $this->Cache =& Ak::cache();
            $this->Response->addHeader(array(
            'Cache-Control' => "max-age=".$this->Cache->_driverInstance->_lifeTime.", must-revalidate",
            'Pragma' => "max-age=".$this->Cache->_driverInstance->_lifeTime.", must-revalidate",
            'Last-Modified' => gmdate("D, d M Y H:i:s", Ak::getTimestamp()),
            'ETag' => md5($this->Cache->_driverInstance->_id.$this->Cache->_driverInstance->_group).'_'.Ak::randomString(),
            ));
        }
    }

    function _saveCache()
    {
        if(EDITAM_CACHE_ENABLED && isset($this->Cache->_driverInstance) &&
        empty($_POST) && empty($_SESSION['__credentials'])){
            $this->Cache->save(serialize($this->Response->_headers).'~~'.$this->Response->body, $this->Cache->_driverInstance->_id, $this->Cache->_driverInstance->_group);
        }
    }


    /**
     * Editam Controller Hooks
     *
     * Controller hooks allows you to add filters to a controllers.
     * 
     * Controller hooks are located at includes/editam/controllers/hooks/CONTROLLER_NAME/500_extension_name-optional_text.php
     * 
     * Controller hooks are evaled into their target controller constructor. This allows a high degree of
     * flexibility.
     * 
     * Example:
     * 
     * Say you want to filter incoming parameters for an action named "add" on the page_controller (in 
     * order to protect email addresses before they are stored into the database), from your
     * extension wymeditor (standards compliant WYMIWYG editor)
     * 
     *  * First you'll create the file  includes/editam/controllers/hooks/page/500_wymeditor.php
     *  * Then you add to your 500_wymeditor.php file the following code
     *      
     *     <?php
     *        $this->beforeFilter(new EmailProtectionFilter()); // EmailProtectionFilter::filter must exist
     *     ?>
     * 
     *  * Thats it.
     * 
     * Here is another example for sending GZ compressed output
     * 
     * <?php
     * 
     *      if(!class_exists('GzOutputFilter')){
     *          class GzOutputFilter{
     *              function filter(&$Controller)
     *              {
     *                  if(function_exists('ob_gzhandler') &&
     *                  preg_match('/gzip|deflate/', @$_SERVER['HTTP_ACCEPT_ENCODING'])){
     *                      ob_start('ob_gzhandler');
     *                  }
     *                  $Controller->Response->sendHeaders();
     *                  echo $Controller->Response->body;
     *                  exit;
     *              }
     *          }
     *      }
     *      
     *      $this->afterFilter(new GzOutputFilter());
     * ?>
     *  
     *  You can modify the controller in many different ways, as it is a default Akelos Framework controller.
     */
    function _engageHooks()
    {
        $hook_dir = AK_CONTROLLERS_DIR.DS.'hooks'.DS.AkInflector::underscore($this->getControllerName()).'_controller';
        if(is_dir($hook_dir)){
            $controller_hooks = AkFileSystem::dir($hook_dir);
            if(!empty($controller_hooks)){
                sort($controller_hooks);
                foreach ($controller_hooks as $controller_hook){
                    eval(' ?>'.file_get_contents($hook_dir.DS.$controller_hook).'<?php ');
                }
            }
        }
    }

    /**
     * Editam View Hooks
     * 
     * You can add views before and after each template or partial rendered by editam.
     * 
     * Example:
     * 
     * If you wan't to add a WYSIWYG editor, you can add your hook before rendering the page/_form.tpl partial
     * 
     * This can be achieved by placing your hook at.
     * 
     *     includes/editam/views/hooks/page/_form/before/YOUR_WYSIWYG_HOOK.tpl
     * 
     * It is recommended that you prefix your hook name with 500_, so if a hook needs to be 
     * loaded earlier it can do it by simply selecting a lower number.
     * 
     * If you wan't to distribute your hook, you need to name it like this 
     * 
     *     500_my_extension_name-step_1.tpl where 
     * 
     *  * 500 is the priority
     *  * my_extension_name is the tecnical name for your extension
     *  * -step_1 is OPTIONAL, you can place anything after the dash if you need to add several hooks for
     *     the same view.
     *  * .tpl the extension
     * 
     * For post-view-hooks, simply add your hooks into an "after" forlder intead of "before"
     */
    function render($options = null, $status = 200)
    {
        $hook_dir = $this->_getTemplateHookBasePath($options);
        $pre_rendered = $hook_dir ? $this->renderBeforeHooks($hook_dir) : '';
        $rendered = parent::render($options, $status);
        $post_rendered = $hook_dir ? $this->renderAfterHooks($hook_dir) : '';
        return str_replace(array('\{','\}'),array('{','}'), $pre_rendered.$rendered.$post_rendered);
    }

    function _renderHooks($type, $hook_dir)
    {
        $result = '';
        if(is_dir($hook_dir.DS.$type)){
            $before_hooks = AkFileSystem::dir($hook_dir.DS.$type);
            if(!empty($before_hooks)){
                sort($before_hooks);
                foreach ($before_hooks as $before_hook){
                    $result .= $this->renderToString($hook_dir.DS.$type.DS.$before_hook);
                }
            }
        }
        return $result;
    }

    function renderBeforeHooks($hook_dir)
    {
        return $this->_renderHooks('before', $hook_dir);
    }

    function renderAfterHooks($hook_dir)
    {
        return $this->_renderHooks('after', $hook_dir);
    }

    function _getTemplateHookBasePath($options = array())
    {
        if(empty($options['partial'])){
            return false;
        }
        if(is_string($options['partial'])){
            $path = $this->Template->_partialPathPiece($options['partial']);
            $partial_name = $this->Template->_partialPathName($options['partial']);
            $template_path = (empty($path) ? '' : $path.DS).'_'.$partial_name;
        }else{
            $template_path = $options['partial'];
        }

        $template_path = substr($template_path,0,7) === 'layouts' ? AK_VIEWS_DIR.DS.$template_path.'.tpl' : $template_path;
        $template_file_name = $this->Template->getFullTemplatePath($template_path, '');
        $hook_dir = rtrim(str_replace(AK_VIEWS_DIR,AK_VIEWS_DIR.DS.'hooks',$template_file_name),'.');
        return is_dir($hook_dir) ? $hook_dir : false;
    }


    /**
     * Editam looks for updates automatically when the administrator logs into the system.
     * If an update is found a persistent flash message is set for the administrator to take 
     * action.
     */
    function _checkForUpdates()
    {
        Ak::import('EditamUpdate');
        $Update = new EditamUpdate();
        $this->credentials->set('has_checked_for_updates', true);
        if($message = $Update->getUpdateMessageIfNewVersionIsAvailable()){
            Ak::import('SystemMessage');
            $SystemMessage = new SystemMessage();
            $SystemMessage->registerMessageForAdmins(array(
            'value' => $message,
            'message_key' => 'editam_update_pending',
            'can_be_hidded' => true,
            'seconds_to_expire' => 432000 // 5 days
            ));
        }
    }

    /**
     * Retrieves IMPORTANT flash messages from the database, that are not 
     * related to current action. This only happens on the admin.
     */
    function _loadSystemMessages()
    {
        Ak::import('SystemMessage');
        $SystemMessage =& new SystemMessage();
        $SystemMessage->addMessagesToController($this);
    }

	/*
     * admin_plugin parts -------------------------------------------------
     */
	function authenticate()
    {
        Ak::import('sentinel');
        $Sentinel =& new Sentinel();
        $Sentinel->init($this);
        return $Sentinel->authenticate();
    }

	/*---------------------------------------------------------------*/
}

?>
