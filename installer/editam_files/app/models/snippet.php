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

Ak::import('editags,EditamFilter');

defined('EDITAM_SNIPPETS_DIRECTORY') ? null : define('EDITAM_SNIPPETS_DIRECTORY', AK_TMP_DIR.DS.'editam'.DS.'snippets');

class Snippet extends ActiveRecord
{
    var $base_path = EDITAM_SNIPPETS_DIRECTORY;

    function render(&$Controller, $local_assigns = array())
    {
        if($this->get('is_enabled')){
            $Controller->_addVariablesToAssigns();
            $local_assigns = array_merge($local_assigns, $Controller->_assigns);
            extract($local_assigns, EXTR_SKIP);
            ob_start();
            include($this->getPath());
            !empty($shared) ? $Controller->Template->addSharedAttributes($shared) : null;
            return  ob_get_clean();
        }
    }

    function validate()
    {
        $this->validatesPresenceOf(array('name','description','content'));
        $this->validatesUniquenessOf('name');
    }

    function beforeSave()
    {
        if(!$this->validatesEditagsField('content', true)){
            $this->addError('content', $this->t('has non secure PHP code'));
            return false;
        }
        AkFileSystem::file_put_contents($this->getPath(), $this->_editags_php);
        return true;
    }

    function getPath()
    {
        return $this->base_path.DS.$this->get('name').'.php';
    }

}

?>
