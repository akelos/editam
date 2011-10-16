<?php

# Author Bermi Ferrer - MIT LICENSE

class TextileFilter
{
    public function filter($textile)
    {
        return TextHelper::textilize($this->_escapeEditagsAndPhp($textile));
    }
    
    public function _escapeEditagsAndPhp($textile)
    {
        $no_textile = array(
            '<?'=>'<notextile><?', 
            '?>'=>'?></notextile>',
            '{%'=>'<notextile>{%',
            '%}'=>'%}</notextile>',
            );

        return str_replace(array_keys($no_textile),array_values($no_textile), $textile);
    }

}
