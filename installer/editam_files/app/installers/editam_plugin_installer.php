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

class EditamPluginInstaller extends AkInstaller
{
    function up_1()
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
            behaviour,
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
//             $this->createCMSRoles();
    }

    function down_1()
    {
        $this->dropTables('content_layouts,page_parts,pages,snippets,translations,translators,
            translator_capabilities,files,site_preferences,user_preferences,system_messages,
            file_types,tags,editam_updates');
    }
    
    function createCMSRoles(){
    	Ak::import('User', 'Role', 'Permission', 'Extension');
    	
    	$Role =& new Role();
    	$Administrator =& $Role->findFirstBy('name','Administrator');
    	$RegisteredUser =& $Role->findFirstBy('name','Registered user');
		
        $Extension =& new Extension();
        $this->AdminPages =& $Extension->create(array('name'=>'Admin::Pages','is_core'=>true, 'is_enabled' => true));
        $this->AdminLayouts =& $Extension->create(array('name'=>'Admin::Layouts','is_core'=>true, 'is_enabled' => true));
        $this->AdminSnippets =& $Extension->create(array('name'=>'Admin::Snippets','is_core'=>true, 'is_enabled' => true));
        
        $this->AdminMenuTabs =& $Extension->findFirstBy('name','Admin Menu Tabs');
        $Administrator->addPermission(array('name'=>'CMS (page controller, listing action)', 'extension' => $this->AdminMenuTabs));
    }

    function installProfiles($version, $profiles)
    {
        $this->_installOrUninstallProfile('install', $version, $profiles);
    }


    function uninstallProfiles($version, $profiles)
    {
        $this->_installOrUninstallProfile('uninstall', $version, $profiles);
    }

    function _installOrUninstallProfile($install_or_uninstall = 'install', $version, $profiles)
    {
        foreach($profiles as $profile){
            $profile_file = AK_APP_DIR.DS.'installers'.DS.'editam_'.$version.DS.'profiles'.DS.$profile.DS.'profile.php';
            if(file_exists($profile_file)){
                include_once($profile_file);
                $profile_class_name = AkInflector::camelize($profile).'Profile';
                if(class_exists($profile_class_name)){
                    $Profile = new $profile_class_name();
                    $Profile->Installer =& $this;
                    $install_or_uninstall == 'uninstall' ? $Profile->uninstall() : $Profile->install();
                }
            }
        }
    }

    function installDataFiles($version)
    {
        $files = Ak::dir(AK_APP_DIR.DS.'installers'.DS.'editam_'.$version.DS.'data');
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

    function addBatchRecords($model, $record_details)
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
}

?>