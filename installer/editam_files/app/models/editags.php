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

include_once(AK_LIB_DIR.DS.'AkActionView'.DS.'TemplateEngines'.DS.'AkSintags.php');

defined('EDITAM_DISABLE_PHP_ON_SNIPPETS') ? null : define('EDITAM_DISABLE_PHP_ON_SNIPPETS', false);

// Setting EDITAGS_REMOVE_PHP_SILENTLY will completelly disable PHP even on snippets.
// This might be useful for creating a set of packaged helper that the users can
// use to build their snippets.
defined('EDITAGS_REMOVE_PHP_SILENTLY') ? null : define('EDITAGS_REMOVE_PHP_SILENTLY', false);
defined('EDITAGS_HELPERS_DIR') ? null : define('EDITAGS_HELPERS_DIR', AK_APP_DIR.DS.'vendor'.DS.'helpers');

class Editags extends AkSintags
{
    var $Parser;

    function Editags()
    {
        $this->Parser =& new EditagsParser();
    }

    function toPhp($code = false, $allow_inline_php = false)
    {
        $code = $code ? $code : $this->_code;
        $this->Parser->_DISABLE_PHP = !$allow_inline_php;
        return $this->Parser->parse($code);
    }

    function isValidPhp($code)
    {
        static $CodeSanitizer;
        if(empty($CodeSanitizer)){
            require_once(AK_LIB_DIR.DS.'AkActionView'.DS.'AkPhpCodeSanitizer.php');
            $CodeSanitizer = new AkPhpCodeSanitizer();
        }
        return $CodeSanitizer->isCodeSecure($code) ? true : $CodeSanitizer->getErrors();
    }

    function hasErrors()
    {
        return !empty($this->Parser->errors);
    }
}

class EditagsLexer extends AkSintagsLexer
{
    var $_SINTAGS_OPEN_HELPER_TAG = '{%';
    var $_SINTAGS_CLOSE_HELPER_TAG = '%}';

    var $_SINTAGS_REMOVE_PHP_SILENTLY = EDITAGS_REMOVE_PHP_SILENTLY;
}


class EditagsParser extends AkSintagsParser
{
    var $_SINTAGS_OPEN_HELPER_TAG = '{%';
    var $_SINTAGS_CLOSE_HELPER_TAG = '%}';
    var $_SINTAGS_HASH_KEY_VALUE_DELIMITER = '=';
    var $_DISABLE_PHP = true;
    var $_lexer_name = 'EditagsLexer';
    var $_EditagsHelper;
    var $_Controller;
    var $available_helpers = array();
    var $errors = array();

    function _getAvailableHelpers()
    {
        if(empty($this->available_helpers)){
            $helpers = array();
            if(defined('EDITAM_AVALABLE_HELPERS')){
                $this->available_helpers = unserialize(EDITAM_AVALABLE_HELPERS);;
            }
        }
        return $this->available_helpers;
    }

    function PhpCode($match, $state)
    {
        if(EDITAM_DISABLE_PHP_ON_SNIPPETS || $this->_DISABLE_PHP){
            return true;
        }else{
            return parent::PhpCode($match, $state);
        }
    }
    
    function raiseError($error, $type)
    {
        $this->errors[] = $error;
    }
}


require_once(AK_LIB_DIR.DS.'AkActionView.php');
require_once(AK_LIB_DIR.DS.'AkActionView'.DS.'AkPhpTemplateHandler.php');

class EditagsTemplateHandler extends AkPhpTemplateHandler
{
    var $_templateEngine = 'Editags';

    function _templateNeedsCompilation()
    {
        return !file_exists($this->_getCompiledTemplatePath());
    }

}

?>