<?php

defined('AK_EDITAM_PLUGIN_FILES_DIR') ? null : define('AK_EDITAM_PLUGIN_FILES_DIR', AK_APP_PLUGINS_DIR.DS.'editam'.DS.'installer'.DS.'editam_files');
define('AK_EDITAM_PLUGIN_MODIFY_DATA_DIR', AK_APP_PLUGINS_DIR.DS.'editam'.DS.'installer'.DS.'filemods'.DS.'data');
define('AK_EDITAM_PLUGIN_FILE_BACKUP_DIR_MOD', AK_TMP_DIR.DS.'editam'.DS.'installer'.DS.'backup_files'.DS.'modified');

class EditamInstaller extends AkInstaller
{
    public $site_details = array();
    
    public function up_1()
    {
        if(!$this->_dependenciesSatisfied()){
            exit;
        }
        
        if(!file_exists(AK_TMP_DIR.DS.'editam_installed.flag')){
            echo "\nWe need some details for setting up the Editam.\n\n ";
            $this->files = AkFileSystem::dir(AK_EDITAM_PLUGIN_FILES_DIR, array('recurse'=> true));
            empty($this->options['force']) ? $this->checkForCollisions($this->files) : null;
            $this->copyEditamFiles();
            $this->modifyFiles();
            
            $f = fopen(AK_TMP_DIR.DS.'editam_installed.flag','w');
            fclose($f);
            passthru('/usr/bin/env php '.AK_BASE_DIR.DS.'script'.DS.'plugin install --force '.AK_PLUGINS_DIR.DS.'editam');
        }else{
            $this->suggestSiteDetails();
            $this->runMigration();
            unlink(AK_TMP_DIR.DS.'editam_installed.flag');
        }
        echo "\n\nInstallation completed\n";
    }

    public function down_1()
    {
        include_once(AK_APP_INSTALLERS_DIR.DS.'editam_plugin_installer.php');
        $Installer = new EditamPluginInstaller();

        echo "Uninstalling the editam plugin migration\n";
        $Installer->uninstall();
    }


    public function checkForCollisions(&$directory_structure, $base_path = AK_EDITAM_PLUGIN_FILES_DIR)
    {
        foreach ($directory_structure as $k=>$node){
            if(!empty($this->skip_all)){
                return ;
            }
            $path = str_replace(AK_EDITAM_PLUGIN_FILES_DIR, AK_BASE_DIR, $base_path.DS.$node);
            if(is_file($path)){
                $message = Ak::t('File %file exists.', array('%file'=>$path));
                $user_response = AkConsole::promptUserVar($message."\n d (overwrite mine), i (keep mine), a (abort), O (overwrite all), K (keep all)", array('default'=>'i'));
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

    public function copyEditamFiles()
    {
        $this->_copyFiles($this->files);
    }

    public function runMigration()
    {
        include_once(AK_APP_INSTALLERS_DIR.DS.'editam_plugin_installer.php');
        $Installer = new EditamPluginInstaller();
        $Installer->site_details = $this->site_details;

        echo "Running the editam plugin migration\n";
        $Installer->install();
    }
    
    public function _copyFiles($directory_structure, $base_path = AK_EDITAM_PLUGIN_FILES_DIR)
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
    
    public function _makeDir($path)
    {
        $dir = str_replace(AK_EDITAM_PLUGIN_FILES_DIR, AK_BASE_DIR,$path);
        if(!is_dir($dir)){
            mkdir($dir);
        }
    }

    public function _copyFile($path)
    {
        $destination_file = str_replace(AK_EDITAM_PLUGIN_FILES_DIR, AK_BASE_DIR,$path);
        $this->_copyFileWithPermission($path,$destination_file);
    }
    
    public function suggestSiteDetails(){
        Ak::import('User', 'Role', 'Permission', 'Extension');
        
        $ApplicationOwner = $this->getApplicationOwner();
        
        $this->site_details['site_name'] = AkConsole::promptUserVar("\n Site Name", array('default' => $this->getApplicationName()));
        $this->site_details['administrator_login'] = $ApplicationOwner->get('login');        
        $this->site_details['administrator_email'] = AkConsole::promptUserVar(" Administrator Email", array('default' => $ApplicationOwner->get('login').'@'.AK_HOST));
    }
    
    function getApplicationName()
    {
        if(!isset($this->application_name)){
            $this->setApplicationName($this->guessApplicationName());
        }
        return $this->application_name;
    }
    
    public function setApplicationName($application_name)
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
        $Role = new Role();
        $ApplicationOwnerRole = $Role->findFirstBy('name','Application owner');
        $ApplicationOwnerRole->user->load();
        return $ApplicationOwnerRole->users[0];
    }
    
    public function _modifyFiles($directory_structure, $base_path = null){
        foreach($directory_structure as $k => $node){
            $path = $base_path.DS.$node;
            if(is_file($path)){
                $source_file = AK_BASE_DIR.DS.substr($path,$this->tmp_str_idx);
                $this->_backupFile($source_file);
                $this->_searchAndReplaceFile($path,$source_file);
            }elseif(is_array($node)){
                foreach ($node as $dir=>$items){
                    $path = $base_path.DS.$dir;
                    if(is_dir($path)){
                        $this->_modifyFiles($items,$path);
                    }
                }
            }
        }
    }
    
    public function modifyFiles($base_path = null){
        $base_path = empty($base_path)?AK_EDITAM_PLUGIN_MODIFY_DATA_DIR : $base_path;
        $this->tmp_str_idx = strlen($base_path.DS);
        $directory_structure = AkFileSystem::dir($base_path, array('recurse'=> true));
        $this->_modifyFiles($directory_structure, $base_path);
    }
    
    public function _searchAndReplaceFile($path,$source_file){
        require_once($path);
        $contents = AkFileSystem::file_get_contents($source_file);
        if(empty($search_replace)) return;
        $modified = false;
        foreach($search_replace as $replace_data){
            if(preg_match($replace_data['detect_modified'],$contents) == 1){
                continue; // skip already modified lines
            }
            $contents = preg_replace($replace_data['searched'],$replace_data['replaced'],$contents);
            $modified = true;
        }
        
        if($modified){
            echo "Modifiying file ".AK_BASE_DIR.DS.$source_file."\n";
            AkFileSystem::file_put_contents(AK_BASE_DIR.DS.$source_file,$contents);
        }
    }
    
    public function _backupFile($path,$is_modified = true){
        if(!file_exists($path)){ return; }
        $backup_dir = ($is_modified===true)?AK_EDITAM_PLUGIN_FILE_BACKUP_DIR_MOD:AK_EDITAM_PLUGIN_FILE_BACKUP_DIR_UPG;
        $destination_file = str_replace(AK_BASE_DIR,$backup_dir,$path);
        if(file_exists($destination_file) && md5_file($path)!=md5_file($destination_file)){
            return;
        }
        $dirs = explode(DS,$destination_file);
        $max_depth = count($dirs)-1;
        $backup_file_path = '';
        for($i=0; $i<$max_depth; $i++){
            $backup_file_path .= $dirs[$i];
            if(!empty($dirs[$i]) && !is_dir($backup_file_path)){
                mkdir($backup_file_path);
            }
            $backup_file_path .= DS;
        }
        $backup_file_path .= $dirs[$i];
        $this->_copyFileWithPermission($path,$backup_file_path);
    }
    
    public function restoreFiles(){
        $backup_paths = array(AK_EDITAM_PLUGIN_MODIFY_DATA_DIR);
        foreach($backup_paths as $backup_path){
            $this->tmp_str_idx = strlen($backup_path.DS);
            $directory_structure = AkFileSystem::dir($backup_path, array('recurse'=> true));
            $this->_restoreFiles($directory_structure,$backup_path);
        }
    }
    
    public function _restoreFiles($directory_structure,$base_path = null){
        foreach($directory_structure as $k => $node){
            $path = $base_path.DS.$node;
            if(is_file($path)){
                $restored_file = AK_BASE_DIR.DS.substr($path,$this->tmp_str_idx);
                $this->_replaceFile($path,$restored_file);
            }elseif(is_array($node)){
                foreach ($node as $dir=>$items){
                    $path = $base_path.DS.$dir;
                    if(is_dir($path)){
                        $this->_modifyFiles($items,$path);
                    }
                }
            }
        }
    }
    
    public function _copyFileWithPermission($src,$dst){
        copy($src,$dst);
        $source_file_mode =  fileperms($src);
        $target_file_mode =  fileperms($dst);
        if($source_file_mode != $target_file_mode){
            chmod($dst,$source_file_mode);
        }
    }
    
    public function _replaceFile($new,$replaced){
        unlink($replaced);
        $this->_copyFileWithPermission($new,$replaced);
    }
    
    public function _dependenciesSatisfied(){
        // check for admin plugin
        $result = true;
        if(!file_exists(AK_BASE_DIR.DS.'app'.DS.'controllers'.DS.'admin')){
            echo "\nEditam need admin_plugin to be installed first.\nYou can add admin_plugin by running './script/plugin install admin'\n";
            $result = false;
        }
        
        return $result;
    }
}

