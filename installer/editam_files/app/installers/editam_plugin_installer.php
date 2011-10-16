<?php

# Author Bermi Ferrer - MIT LICENSE

defined('AK_EDITAM_PLUGIN_MODIFY_DATA_DIR') ? null :define('AK_EDITAM_PLUGIN_MODIFY_DATA_DIR', AK_APP_PLUGINS_DIR.DS.'editam'.DS.'installer'.DS.'filemods'.DS.'data');
defined('AK_EDITAM_PLUGIN_FILE_BACKUP_DIR_MOD') ? null : define('AK_EDITAM_PLUGIN_FILE_BACKUP_DIR_MOD', AK_TMP_DIR.DS.'editam'.DS.'installer'.DS.'backup_files'.DS.'modified');

class EditamPluginInstaller extends AkInstaller
{
    public function up_1()
    {
        $this->createTable('content_layouts',
        'id,
            name(100),
            content text,
            content_type(40),
            locale string(5) not null default \''.Ak::lang().'\',
            created_at,
            updated_at,
            created_by,
            updated_by');

        $this->createTable('page_parts',
        'id,
            name(100),
            content text,
            filter,
            locale string(5) not null default \''.Ak::lang().'\',
            page_id');

        $this->createTable('pages',
        'id,
            title,
            slug(100),
            breadcrumb,
            layout_id,
            status string(12) default \'published\' index,
            behavior,
            created_at,
            updated_at,
            published_at,
            created_by,
            updated_by,
            locale string(5) not null default \''.Ak::lang().'\',
            parent_id,
            lft integer(8) index,
            rgt integer(8) index,
            is_virtual');


        $this->createTable('snippets',
        'id,
            name string(100),
            filter_using string(25),
            content text,
            description,
            is_enabled bool default 1,
            has_inline_php bool default 1,
            created_at,
            updated_at,
            created_by,
            updated_by');

        $this->createTable('translations',
        'id,
            origin_locale string(5) not null index,
            target_locale string(5) not null index,
            owner_type,
            origin_id,
            target_id,
            translator_id,
            created_at,
            updated_at,
            completed_at,
            expires_at,
            is_up_to_date
            ');

        $this->createTable('translators',
        'id,
            user_id,
            pending_tasks,
            is_available,
            created_at');

        $this->createTable('translator_capabilities',
        'id,
            translator_id,
            origin_locale string(5) not null index,
            target_locale string(5) not null index');

        $this->createTable('files',
        'id,
            mime_type,
            file_type_id,
            name,
            data binary,
            size integer,
            user_id,
            is_public,
            parent_id,
            locale string(5) not null default \''.Ak::lang().'\',
            updated_at,
            created_at');

        $this->createTable('file_types', "
            id,
            name,
            is_core,
            handler_name");

        $this->createTable('tags', '
            id,
            locale string(5) not null default \''.Ak::lang().'\',
            counter int,
            name');

        $this->createTable('site_preferences',
            'id,
            name,
            title,
            value text,
            parent_id,
            extension_id,
            is_editable,
            is_core'
            );

            $this->createTable('user_preferences',
            'id,
            name,
            value text,
            user_id,
            is_editable'
            );

            $this->createTable('editam_updates',
            'id,
            status,
            message,
            from_version,
            to_version,
            details binary,
            backup binary,
            has_been_performed,
            has_been_ignored,
            created_at,
            updated_at'
            );

            $this->createTable('system_messages',
            'id,
             value text,
             message_key,
             user_id,
             has_been_readed,
             can_be_hidded bool default 1,
             created_at,
             seconds_to_expire integer default 0
             '
             );

             if(!empty($this->do_not_create_sample_data)){
                 return;
             }
             $this->installProfiles(1, array('base_system','sample_website'));
             $this->createCMSRoles();
    }

    public function down_1()
    {
        $this->dropTables('content_layouts,page_parts,pages,snippets,translations,translators,
            translator_capabilities,files,site_preferences,user_preferences,system_messages,
            file_types,tags,editam_updates');
        $this->removeCMSRoles();
        $this->restoreFiles();
    }
    
    public function createCMSRoles(){
        Ak::import('User', 'Role', 'Permission', 'Extension');
        
        $Role = new Role();
        $Administrator = $Role->findFirstBy('name','Administrator');
        $Administrator->addChildrenRole('Contributor');
        $Administrator->addChildrenRole('Visitor');
    }
    
    public function removeCMSRoles(){
        Ak::import('User', 'Role', 'Permission', 'Extension');
        
        $Role = new Role();
        $CMSRole = $Role->findFirstBy('name','Contributor');
        $CMSRole->destroy();
        $CMSRole = $Role->findFirstBy('name','Visitor');
        $CMSRole->destroy();
    }

    public function installProfiles($version, $profiles)
    {
        $this->_installOrUninstallProfile('install', $version, $profiles);
    }


    public function uninstallProfiles($version, $profiles)
    {
        $this->_installOrUninstallProfile('uninstall', $version, $profiles);
    }

    public function _installOrUninstallProfile($install_or_uninstall = 'install', $version, $profiles)
    {
        foreach($profiles as $profile){
            $profile_file = AK_APP_DIR.DS.'installers'.DS.'editam_'.$version.DS.'profiles'.DS.$profile.DS.'profile.php';
            if(file_exists($profile_file)){
                include_once($profile_file);
                $profile_class_name = AkInflector::camelize($profile).'Profile';
                if(class_exists($profile_class_name)){
                    $Profile = new $profile_class_name();
                    $Profile->Installer = $this;
                    $install_or_uninstall == 'uninstall' ? $Profile->uninstall() : $Profile->install();
                }
            }
        }
    }

    public function installDataFiles($version)
    {
        $files = AkFileSystem::dir(AK_APP_DIR.DS.'installers'.DS.'editam_'.$version.DS.'data');
        sort($files);
        foreach ($files as $file){
            if($file[0] == '_' || !strstr($file,'yaml')){
                continue;
            }
            $file = preg_replace('/^([0-9]*_)/','', $file);
            $this->addBatchRecords(
            AkInflector::camelize(substr($file, 0, strrpos($file,'.'))),
            Ak::convert('yaml','array',file_get_contents(AK_APP_DIR.DS.'installers'.DS.'editam_'.$version.DS.'data'.DS.$file))
            );
        }
    }

    public function addBatchRecords($model, $record_details)
    {
        Ak::import($model);
        foreach($record_details as $record_detail){
            $Element = new $model();
            $Element->setAttributes($record_detail);
            $Element->save();
            if($Element->hasErrors()){
                echo "<p>There was an error while adding a new ".$Element->getModelName().'</p>';
                echo "<p>Please <a href='http://trac.editam.com/newticket'>notify the Editam team</a> about this issue providing the message bellow</p>";
                echo "<pre>".print_r($Element->getErrors(),true)."</pre>";
            }
        }

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
                $current_file = AK_BASE_DIR.DS.substr($path,$this->tmp_str_idx);
                $backup_file = AK_EDITAM_PLUGIN_FILE_BACKUP_DIR_MOD.DS.substr($path,$this->tmp_str_idx);
                $this->_replaceFile($backup_file,$current_file);
            }elseif(is_array($node)){
                foreach ($node as $dir=>$items){
                    $path = $base_path.DS.$dir;
                    if(is_dir($path)){
                        $this->_restoreFiles($items,$path);
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
}
