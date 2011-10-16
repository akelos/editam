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

class File extends ActiveRecord
{
    // var $has_many = 'tags';
    //var $belongs_to = array('file_type','user');
    var $atcs_as = 'tree';
    var $upload_using_ftp = AK_UPLOAD_FILES_USING_FTP;

    function beforeValidationOnCreate()
    {
        if(empty($this->uploaded_file_path) || !is_file($this->uploaded_file_path)){
            $this->addErrorToBase($this->t('An error occoured when uploading the file to the server.'));
            trigger_error('The system could not find where temporary uploaded files are. Use $File->setUploadedFilePath() to tell editam where to find them.', E_USER_NOTICE);
            return false;
        }
        return true;
    }

    function afterCreate()
    {
        if(!$this->moveFile(array('ftp'=>$this->upload_using_ftp))){
            $this->addErrorToBase($this->t('An error occoured when uploading the file to the server.'));
            trigger_error('Could not move the file to the target directory '.$this->getFilePath(), E_USER_NOTICE);
            return false;
        }
        return true;
    }

    function setAttributes($attributes)
    {
        if(!empty($attributes['tmp_name'])){
            $this->setUploadedFilePath($attributes['tmp_name']);
        }

        if ($this->isNewRecord() && empty($attributes['path'])) {
            $attributes['path'] = AkInflector::underscore($this->getFilePath());
        }

        if (!empty($attributes['name'])) {
            $attributes['name'] =
            AkInflector::underscore(substr($attributes['name'],0, strrpos($attributes['name'],'.'))).
            '.'.$this->getExtension($attributes['name']);

        }

        if (!empty($attributes['type'])) {
            $attributes['mime_type'] = $attributes['type'];
            unset($attributes['type']);
        }

        if ($this->isNewRecord() && !empty($attributes['mime_type']) && strstr($attributes['mime_type'], 'image/') && (empty($attributes['position']) || (int)$attributes['position'] < 0)) {
            $attributes['position'] = $this->getNextFilePositionForCurrentEvent();
        }

        return parent::setAttributes($attributes);
    }

    function setUploadedFilePath($uploaded_file_path)
    {
        $this->uploaded_file_path = $uploaded_file_path;
    }

    function moveFile($options = array())
    {
        if (file_exists($this->uploaded_file_path)) {
            $content = file_get_contents($this->uploaded_file_path );
            if (!empty($content)) {
                $path = $this->getFilePath();
                if ($path && AkFileSystem::file_put_contents($path, $content, $options)) {
                    return true;
                }elseif (!$path){
                    $this->addErrorToBase($this->t('You need to set a valid path for the file.
            	     This is usually produced by the fact that you provide an unsaved Model to the file.'));
                }
            }
        }
        return false;
    }

    function getFilePath($type = 'file')
    {
        return $this->_getLocation($type);
    }

    function getExtension($file_name = null)
    {
        return empty($file_name) ? array_pop(pathinfo($this->getFilePath())) : array_pop(pathinfo($file_name));
    }

    function getUrl()
    {
        return $this->_getLocation('/');
    }

    function _getLocation($type = 'file')
    {
        if(strtolower($type) == 'image'){
            return AK_PUBLIC_DIR.DS.'images'.DS.AkInflector::underscore($this->Model->getModelName()).DS.$this->Model->getId().DS.$this->get('name');
        }
        elseif(strtolower($type) == 'url'){
            $separator = '/';
            $base_location = AK_ASSET_HOST;
        }else{
            $separator = DS;
            $base_location = AK_PUBLIC_DIR;
        }

        if(!empty($this->Model) && $this->Model->isNewRecord()){
            return false;
        }elseif (!empty($this->Model)){
            $file_url = AkInflector::underscore($this->Model->getModelName()).$separator.$this->Model->getId().$separator.$this->get('name');
        }else{
            $file_url = $this->get('name');
        }
        return $base_location.$separator.AkInflector::underscore($this->getModelName()).$separator.$file_url;
    }
}

?>
