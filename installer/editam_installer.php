<?php

defined('AK_EDITAM_PLUGIN_FILES_DIR') ? null : define('AK_EDITAM_PLUGIN_FILES_DIR', AK_APP_PLUGINS_DIR.DS.'editam'.DS.'installer'.DS.'editam_files');

class EditamInstaller extends AkInstaller
{
	var $site_details = array();
	
    function up_1()
    {
        echo "\nWe need some details for setting up the Editam.\n\n ";
        $this->relativizeStylesheetPaths();
        $this->suggestSiteDetails();
        $this->runMigration();
        echo "\n\nInstallation completed\n";
    }

    function down_1()
    {
        include_once(AK_APP_INSTALLERS_DIR.DS.'editam_plugin_installer.php');
        $Installer =& new EditamPluginInstaller();

        echo "Uninstalling the editam plugin migration\n";
        $Installer->uninstall();
    }


    function checkForCollisions(&$directory_structure, $base_path = AK_EDITAM_PLUGIN_FILES_DIR)
    {
        foreach ($directory_structure as $k=>$node){
            if(!empty($this->skip_all)){
                return ;
            }
            $path = str_replace(AK_EDITAM_PLUGIN_FILES_DIR, AK_BASE_DIR, $base_path.DS.$node);
            if(is_file($path)){
                $message = Ak::t('File %file exists.', array('%file'=>$path));
                $user_response = AkInstaller::promptUserVar($message."\n d (overwrite mine), i (keep mine), a (abort), O (overwrite all), K (keep all)", array('default'=>'i'));
                if($user_response == 'i'){
                    unset($directory_structure[$k]);
                }    elseif($user_response == 'O'){
                    return false;
                }    elseif($user_response == 'K'){
                    $directory_structure = array();
                    return false;
                }elseif($user_response != 'd'){
                    echo "\nAborting\n";
                    exit;
                }
            }elseif(is_array($node)){
                foreach ($node as $dir=>$items){
                    $path = $base_path.DS.$dir;
                    if(is_dir($path)){
                        if($this->checkForCollisions($directory_structure[$k][$dir], $path) === false){
                            $this->skip_all = true;
                            return;
                        }
                    }
                }
            }
        }
    }

    function copyEditamFiles()
    {
        $this->_copyFiles($this->files);
    }

    function runMigration()
    {
        include_once(AK_APP_INSTALLERS_DIR.DS.'editam_plugin_installer.php');
        $Installer =& new EditamPluginInstaller();
        $Installer->site_details = $this->site_details;

        echo "Running the editam plugin migration\n";
        $Installer->install();
    }
    function relativizeStylesheetPaths()
    {
//        $url_suffix = AkInstaller::promptUserVar(
//        'The editam plugin comes with some fancy CSS background images.
//
//Your aplication might be accesible at /myapp, 
//and your images folder might be at /myapp/public
//
//Insert the relative path where your images folder is
//so you don\'t need to manually edit the CSS files', array('default'=>'/'));
//        
//        $url_suffix =  trim(preg_replace('/\/?images\/editam\/?$/','',$url_suffix),'/');
//        
//        if(!empty($url_suffix)){
//            $stylesheets = array('editam/admin','admin/menu');
//            foreach ($stylesheets as $stylesheet) {
//                $filename = AK_PUBLIC_DIR.DS.'stylesheets'.DS.$stylesheet.'.css';
//                $relativized_css = preg_replace("/url\((\'|\")?\/images/","url($1/$url_suffix/images", @Ak::file_get_contents($filename));
//                !empty($relativized_css) && @Ak::file_put_contents($filename, $relativized_css);
//            }
//        }
    }
    
    function _copyFiles($directory_structure, $base_path = AK_EDITAM_PLUGIN_FILES_DIR)
    {
        foreach ($directory_structure as $k=>$node){
            $path = $base_path.DS.$node;
            if(is_dir($path)){
                echo 'Creating dir '.$path."\n";
                $this->_makeDir($path);
            }elseif(is_file($path)){
                echo 'Creating file '.$path."\n";
                $this->_copyFile($path);
            }elseif(is_array($node)){
                foreach ($node as $dir=>$items){
                    $path = $base_path.DS.$dir;
                    if(is_dir($path)){
                        echo 'Creating dir '.$path."\n";
                        $this->_makeDir($path);
                        $this->_copyFiles($items, $path);
                    }
                }
            }
        }
    }

    function _makeDir($path)
    {
        $dir = str_replace(AK_EDITAM_PLUGIN_FILES_DIR, AK_BASE_DIR,$path);
        if(!is_dir($dir)){
            mkdir($dir);
        }
    }

    function _copyFile($path)
    {
        $destination_file = str_replace(AK_EDITAM_PLUGIN_FILES_DIR, AK_BASE_DIR,$path);
        copy($path, $destination_file);
        $source_file_mode =  fileperms($path);
        $target_file_mode =  fileperms($destination_file);
        if($source_file_mode != $target_file_mode){
            chmod($destination_file,$source_file_mode);
        }
    }
    
    function suggestSiteDetails(){
    	Ak::import('User', 'Role', 'Permission', 'Extension');
    	
    	$ApplicationOwner = $this->getApplicationOwner();
    	
    	$this->site_details['site_name'] = AkInstaller::promptUserVar("\n Site Name", array('default' => $this->getApplicationName()));
		$this->site_details['administrator_login'] = $ApplicationOwner->get('login');    	
    	$this->site_details['administrator_email'] = AkInstaller::promptUserVar(" Administrator Email", array('default' => $ApplicationOwner->get('login').'@'.AK_HOST));
    }
    
	function getApplicationName()
    {
        if(!isset($this->application_name)){
            $this->setApplicationName($this->guessApplicationName());
        }
        return $this->application_name;
    }
	
    function setApplicationName($application_name)
    {
        $this->application_name = $application_name;
    }
    
	function guessApplicationName()
    {
        $application_name = empty($application_name) ? substr(AK_BASE_DIR, strrpos(AK_BASE_DIR, DS)+1) : $application_name;
        return empty($application_name) ? 'editam' : $application_name;
    }
    
	function getApplicationOwner()
    {
    	$Role =& new Role();
        $ApplicationOwnerRole = $Role->findFirstBy('name','Application owner');
        $ApplicationOwnerRole->user->load();
        return $ApplicationOwnerRole->users[0];
    }
    
    function modifyFiles($base_path = null){
    	$base_path = empty($base_path)?dirname(__FILE__).DS.'filemods'.DS.'data' : $base_path;
    	$str_idx = strlen(dirname(__FILE__).DS.'filemods'.DS.'data'.DS);
    	$directory_structure = Ak::dir($base_path);
    	foreach($directory_structure as $k => $node){
    		$path = $base_path.DS.$node;
    		if(is_file($path)){
    			$source_file = AK_BASE_DIR.DS.substr($path,$str_idx);
	    		$this->_searchAndReplaceFile($path,$source_file);
    		}elseif(is_array($node)){
    			foreach ($node as $dir=>$items){
                    $path = $base_path.DS.$dir;
	                if(is_dir($path)){
						$this->modifyFiles($path);
	                }
    			}
    		}
    	}
    }
    
    function _searchAndReplaceFile($path,$source_file){
		require_once($path);
		$contents = Ak::file_get_contents($source_file);
		if(empty($search_replace)) return;
		$modified = false;
    	if(strpos($source_file,AK_CONFIG_DIR.DS.'routes.php')!==false){
    		$preffix = '/'.trim($this->promptUserVar('Editam url preffix',  array('default'=>'/editam/')), "\t /").'/';
    		foreach($search_replace as $k => $replace_data){
    			foreach($replace_data as $l => $data){
					$search_replace[$k][$l] = str_replace("%prefix%",$preffix,$data);
    			}
    		}
		}
		foreach($search_replace as $replace_data){
			if(preg_match($replace_data['detect_modified'],$contents) == 1){
				continue; // skip already modified lines
			}
            $contents = preg_replace($replace_data['searched'],$replace_data['replaced'],$contents);
            $modified = true;
		}
		
		if($modified){
			echo "Modifiying file ".AK_BASE_DIR.DS.$source_file."\n";
			Ak::file_put_contents(AK_BASE_DIR.DS.$source_file,$contents);
		}
    }
}

?>
