<?php

# Author Bermi Ferrer - MIT LICENSE

defined('EDITAM_UPDATE_TYPE') ? null : define('EDITAM_UPDATE_TYPE', 'stable');

class EditamUpdate extends ActiveRecord
{
    public $cache_update_files = true;
    public $_update_conflicts = array();
    public $_skipped_files = array();
    public $modifications = array();

    public function getUpdateMessageIfNewVersionIsAvailable($options = array())
    {
        $details = $this->getUpdateDetails($options);
        if(!empty($details)){
            $details = @unserialize(trim($details));
        }
        return !empty($details['status']) && !empty($details['message']) && $details['status'] == 'available' ?
        $this->t($details['message'], array('%update_url' => Ak::toUrl(array('controller'=>'editam_update')))) :
        false;
    }

    public function getUpdateDetailsIfAvailable($options = array())
    {
        $details = $this->getUpdateDetails($options);
        if(!empty($details)){
            $details = @unserialize($details);
            if(isset($details['status'])){
                $status_handler = AkInflector::variablize('handle '.$details['status'].' update');
                if(method_exists($this, $status_handler)){
                    return $this->$status_handler($details);
                }
            }
        }
        return false;
    }

    public function handleAvailableUpdate($update_details)
    {
        $update_details['update'] = unserialize($update_details['update']);
        $this->canPerformCleanUpdate($update_details['update']);
        return $update_details;
    }

    public function canPerformCleanUpdate($update_files_and_directories)
    {
        $result = true;
        foreach ($update_files_and_directories as $file_or_directory){
            if($this->hasConflicts($file_or_directory)){
                $result = false;
            }
        }
        return $result && empty($this->_update_conflicts);
    }

    public function hasConflicts($file_or_directory)
    {
        return $this->_isFile($file_or_directory) ? $this->fileHasConflicts($file_or_directory) : $this->directoryHasConflicts($file_or_directory);
    }

    public function fileHasConflicts($file_details)
    {
        $file_exists = file_exists(AK_BASE_DIR.DS.$file_details['path']);
        $file_checksum = $file_exists ? md5_file(AK_BASE_DIR.DS.$file_details['path']) : false;

        if(empty($file_details['from_checksum']) && $file_exists){
            $this->addConflict($file_details, 'These <strong>files will be replaced</strong>. Please, <em>check the files that you want to prevent from being replaced</em>. <br /><span class="information">The following files exists in your system with same naming that other files included on the update, and will be replaced unless they are checked:</span>');
            return true;
        }elseif(empty($file_details['to_checksum']) && $file_exists && $file_checksum != $file_details['from_checksum']){
            $this->addConflict($file_details, 'These existing <strong>files will be removed</strong>. Please, <em>check the files you want to keep from being removed</em>.
            <br /><span class="information">The following files have local modifications and will removed during the update process unless they are checked:</span>');
            return true;
        }

        if($file_exists && $file_checksum != @$file_details['from_checksum']){
            $this->addConflict($file_details, 'These <strong>files will be overwritten</strong>. Please, <em>check the files you want to keep from being altered</em>.<br /><span class="information">The following files with local modifications will be modified on the update process unless they are checked:</span>');
            return true;
        }

        return false;
    }

    public function directoryHasConflicts($directory_details)
    {
        return false;
    }

    public function addConflict($file_or_directory_details, $error_message)
    {
        $this->_update_conflicts[$error_message][$file_or_directory_details['path']] = $file_or_directory_details['path'];
    }

    public function getConflicts()
    {
        return $this->_update_conflicts;
    }

    public function _isFile($file_details)
    {
        return !empty($file_details['path']) && (!empty($file_details['to_checksum']) || !empty($file_details['from_checksum']));
    }

    public function getCurrentVersion()
    {
        return file_exists(AK_APP_DIR.DS.'version.txt') ? AkFileSystem::file_get_contents(AK_APP_DIR.DS.'version.txt') : false;
    }

    public function getUpdateDetails($options = array())
    {
        if($params = $this->getUpdateUrlParams($options)){
            $default_timeout = @ini_get('default_socket_timeout');
            if(!empty($default_timeout)){
                @ini_set('default_socket_timeout', 5); 
            }
            $update_details = @file_get_contents('http://updates.editam.com/?'.$params);
            $update_details = empty($update_details) ? @Ak::url_get_contents('http://updates.editam.com/?'.$params) : $update_details;
            if(!empty($default_timeout)){
                @ini_set('default_socket_timeout', $default_timeout); 
            }
        }
        return empty($update_details) ? false : $update_details;
    }
    
    public function getFullUpdate($options = array())
    {
        return $this->getUpdateDetails(array_merge(array('include_file_contents'=>1), $options));
    }

    public function getUpdateUrlParams($options)
    {
        if($version = $this->getCurrentVersion()){
            ak_compat('http_build_query');
            list($default_from_type, $default_from_version) = explode('-',$version);
            $default_options = array(
            'version' => $default_from_version,
            'type' => $default_from_type,
            'product' => 'editam',
            'license' => 'GPL3',
            'edition' => 'community',
            'host' => AK_HOST
            );
            $options = array_merge($default_options, $options);
            return http_build_query($options);
        }else{
            return false;
        }
    }

    public function setConflictResolutions($resolutions)
    {
        foreach ((array)$resolutions as $file=>$keep){
            $keep == 1 ? array_push($this->_skipped_files, $file) : null;
        }
    }

    public function update($from, $to)
    {
        $success = true;
        if(!strstr($from,'-')|| !strstr($to,'-')){
            return false;
        }
        list($from_type, $from_version) = explode('-', $from);
        list($type, $to_version) = explode('-', $to);
        $backup_path = AK_TMP_DIR.DS.'editam'.DS.'update_backup'.DS.$from.'_'.$to;
        if($update_details = $this->getUpdateDetailsIfAvailable(array(
        'include_file_contents' => true,
        'version' => $from_version,
        'required_version' => $to,
        'type' => $type
        ))){
            foreach ($update_details['update'] as $item){
                if($this->_isFile($item)){
                    if($this->_canUpdateFile($item['path'])){
                        $action = 'C';
                        if(!empty($item['from_checksum'])){
                            $action = 'M';
                            AkFileSystem::file_put_contents($backup_path.DS.$item['path'], AkFileSystem::file_get_contents($item['path']));
                        }
                        if(!empty($item['remove'])){
                            $action = 'D';
                            AkFileSystem::file_delete($item['path']);
                        }else{
                            AkFileSystem::file_put_contents($item['path'], base64_decode($item['content']));
                        }

                        $this->modifications[] = $action.' '.$item['path'];
                    }else{
                        $this->modifications[] = 'I '.$item['path'];
                    }
                }else{
                    if(!empty($item['remove'])){
                        $this->modifications[] = 'D '.$item['path'].DS;
                        Ak::copy($item['path'], $backup_path.DS.$item['path']);
                        AkFileSystem::directory_delete($item['path']);
                    }else{
                        $this->modifications[] = 'A '.$item['path'].DS;
                        Ak::make_dir($item['path']);
                    }
                }
            }
        }
        if(!empty($update_details['migrate_to_version'])){
            $this->runMigration($update_details['migrate_to_version']);
        }
        return true;
    }

    public function _canUpdateFile($file_path)
    {
        return !in_array($file_path, $this->_skipped_files);
    }

    public function runMigration($version)
    {

    }
}

