<?php

# Author Bermi Ferrer - MIT LICENSE

defined('EDITAM_DISABLE_PHP_ON_SNIPPETS') ? null : define('EDITAM_DISABLE_PHP_ON_SNIPPETS', false);

// Setting EDITAGS_REMOVE_PHP_SILENTLY will completelly disable PHP even on snippets.
// This might be useful for creating a set of packaged helper that the users can
// use to build their snippets.
defined('EDITAGS_REMOVE_PHP_SILENTLY') ? null : define('EDITAGS_REMOVE_PHP_SILENTLY', false);
defined('EDITAGS_HELPERS_DIR') ? null : define('EDITAGS_HELPERS_DIR', AK_APP_DIR.DS.'vendor'.DS.'helpers');

class Editags extends AkSintags
{
    public $Parser;

    public function Editags()
    {
        $this->Parser = new EditagsParser();
    }

    public function toPhp($code = false, $allow_inline_php = false)
    {
        $code = $code ? $code : $this->_code;
        $this->Parser->_DISABLE_PHP = !$allow_inline_php;
        return $this->Parser->parse($code);
    }

    public function isValidPhp($code)
    {
        static $CodeSanitizer;
        if(empty($CodeSanitizer)){
            $CodeSanitizer = new AkPhpCodeSanitizer();
        }
        return $CodeSanitizer->isCodeSecure($code) ? true : $CodeSanitizer->getErrors();
    }

    public function hasErrors()
    {
        return !empty($this->Parser->errors);
    }
}

class EditagsLexer extends AkSintagsLexer
{
    public $_SINTAGS_OPEN_HELPER_TAG = '{%';
    public $_SINTAGS_CLOSE_HELPER_TAG = '%}';

    public $_SINTAGS_REMOVE_PHP_SILENTLY = EDITAGS_REMOVE_PHP_SILENTLY;
}


class EditagsParser extends AkSintagsParser
{
    public $_SINTAGS_OPEN_HELPER_TAG = '{%';
    public $_SINTAGS_CLOSE_HELPER_TAG = '%}';
    public $_SINTAGS_HASH_KEY_VALUE_DELIMITER = '=';
    public $_DISABLE_PHP = true;
    public $_lexer_name = 'EditagsLexer';
    public $_EditagsHelper;
    public $_Controller;
    public $available_helpers = array();
    public $errors = array();

    public function _getAvailableHelpers()
    {
        if(empty($this->available_helpers)){
            $helpers = array();
            if(defined('EDITAM_AVALABLE_HELPERS')){
                $this->available_helpers = unserialize(EDITAM_AVALABLE_HELPERS);;
            }
        }
        return $this->available_helpers;
    }

    public function PhpCode($match, $state)
    {
        if(EDITAM_DISABLE_PHP_ON_SNIPPETS || $this->_DISABLE_PHP){
            return true;
        }else{
            return parent::PhpCode($match, $state);
        }
    }
    
    public function raiseError($error, $type)
    {
        $this->errors[] = $error;
    }
}

class EditagsTemplateHandler extends AkPhpTemplateHandler
{
    public $_templateEngine = 'Editags';

    public function _templateNeedsCompilation()
    {
        return !file_exists($this->_getCompiledTemplatePath());
    }

}

