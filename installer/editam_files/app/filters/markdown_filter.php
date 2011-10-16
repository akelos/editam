<?php

# Author Bermi Ferrer - MIT LICENSE

class MarkdownFilter
{
    public $_escape = array('<?','?>','{%','%}',);
    public $_unescape = array('<!-- NO_MARKDOWN<?','?>NO_MARKDOWN --!>','<!-- NO_MARKDOWN{%','%}NO_MARKDOWN --!>');
    
    public function filter($text)
    {
        return 
        str_replace(array('&lt;!-- NO_MARKDOWN','NO_MARKDOWN --!&gt;'), '',
        str_replace($this->_unescape,$this->_escape,
        TextHelper::markdown(
        str_replace($this->_escape,$this->_unescape, $text))));
    }
}
