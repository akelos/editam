<?php
	$search_replace = array(
    		array(
    			"searched" => "/(\<\?php\s*)/",
    			"detect_modified" => "/require_once\s*\(AK_MODELS_DIR\.DS\.'editam\.php'\);/",
    			"replaced" => "$1require_once(AK_MODELS_DIR.DS.'editam.php');\n\n"
    		),
    		array(
    			"searched" => "/(function\s*__construct\(\)\s*\{\s*\\\$[\w\-\>\s\:\(\)\'\=]*;\s*)/",
    			"detect_modified" => "/\\\$this-\>site_url[\w\s\W]*show_page'\){4};/",
    			"replaced" => "$1\n        \$this->site_url = \$this->base = rtrim(AK_URL,'/');
    	\$this->is_multilingual = Editam::isMultilingual();
        \$this->title = empty(\$this->title) ? Editam::settings_for('core','site_title') : \$this->title;
        \$this->host = AK_HOST;
        \$this->lang = Ak::lang();
        
        \$this->beforeFilter('_loadSettings');
        \$this->beforeFilter(array('_instantiateCredentials'=>array('except'=>array('show_page'))));
        \$this->beforeFilter(array('_disableLinkPrefetching'=>array('except'=>array('show_page'))));
        \$this->beforeFilter(array('_initAdminOptions'=>array('except'=>array('show_page'))));\n    "),
    		array(
    			"searched" =>  "/(function\s*reset_password\(\)\s*\{\s*[\w\s\W]*perform_logout\(\w*\);\s*\}\s*)/",
    			"detect_modified" => "/function\s*_loadSettings\(\)\s*\{\s*[\w\s\W]*function\s*_disableLinkPrefetching[\w\s\W]*return\s*true;\s*\}/",
    			"replaced" => "$1\n\n    function _loadSettings()
    {
        require_once(AK_MODELS_DIR.DS.'site_preference.php');
        \$Preference = new SitePreference();
        Editam::settings_for(\$Preference->_loadPreferences(), null, true);
    }
    
	function _initAdminOptions()
    {
        \$this->selected_tab = empty(\$this->selected_tab) ?
        AkInflector::pluralize(\$this->getControllerName()) : \$this->selected_tab;
        \$this->logo = Editam::settings_for('core','logo');
        \$this->title = Editam::settings_for('core','site_title');
        \$this->community_edition = Editam::settings_for('core','license_key') == 'MPL';
        
        if(!empty(\$this->credentials->is_admin) && \$this->credentials->get('has_checked_for_updates') !== true){
            \$this->_checkForUpdates();
        }
        empty(\$this->credentials->id) ? null : \$this->_loadSystemMessages();
    }

    function _instantiateCredentials()
    {
        require_once(AK_MODELS_DIR.DS.'credentials.php');
        \$this->credentials = new Credentials();
        return true;
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
        if(isset(\$this->Request->env[\"HTTP_X_MOZ\"]) && \$this->Request->env[\"HTTP_X_MOZ\"] == 'prefetch'){
            \$this->renderNothing(403);
            return false;
        }
        return true;
    }\n"
    		)
    	);
?>