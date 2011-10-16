<?php

# Author Bermi Ferrer - MIT LICENSE

class SmartypantsFilter
{
    public $_escape = array('<?','?>','{%','%}',);
    public $_unescape = array('<!-- NO_MARKDOWN<?','?>NO_MARKDOWN --!>','<!-- NO_MARKDOWN{%','%}NO_MARKDOWN --!>');

    public function filter($text)
    {
        require_once(AK_VENDOR_DIR.DS.'TextParsers'.DS.'smartypants.php');
        $Smartypants = new SmartyPantsTypographer_Parser();
        return str_replace($this->_unescape,$this->_escape,
        $Smartypants->transform(str_replace($this->_escape,$this->_unescape, $text)));
    }

}
