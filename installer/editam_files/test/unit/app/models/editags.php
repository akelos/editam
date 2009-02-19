<?php

require_once(AK_MODELS_DIR.DS.'editags.php');

define('EDITAM_AVALABLE_HELPERS', 'a:13:{s:7:"url_for";s:10:"url_helper";s:7:"link_to";s:10:"url_helper";s:7:"mail_to";s:10:"url_helper";s:10:"email_link";s:10:"url_helper";s:9:"translate";s:11:"text_helper";s:20:"number_to_human_size";s:13:"number_helper";s:6:"render";s:10:"controller";s:25:"distance_of_time_in_words";s:11:"date_helper";s:7:"content";s:11:"site_helper";s:9:"site_name";s:11:"site_helper";s:5:"title";s:11:"site_helper";s:4:"part";s:11:"site_helper";s:7:"snippet";s:11:"site_helper";}');


class EditagsTest extends  AkUnitTest
{
    function test_editags()
    {
       $this->_run_from_file('editags_test_data.txt');
    }
    function test_editags_helpers()
    {
        $this->_run_from_file('editags_helpers_data.txt');
    }

    function _run_from_file($file_name, $all_in_one_test = true)
    {
        $multiple_expected_php = $multiple_editags = '';
        $tests = explode('===================================',
        file_get_contents(AK_TEST_DIR.DS.'fixtures'.DS.'data'.DS.$file_name));
        foreach ($tests as $test) {
            list($editags_code, $php) = explode('-----------------------------------',$test);
            $editags_code = trim($editags_code);
            $expected_php = trim($php);
            if(empty($editags_code)){
                break;
            }else{
                $multiple_editags .= $editags_code;
                $multiple_expected_php .= $expected_php;
            }
            $Editags =& new Editags();
            $Editags->init(array('code'=>$editags_code));
            $php = $Editags->toPhp();
            if($php != $expected_php){
                Ak::trace("GENERATED: \n".$php);
                Ak::trace("EXPECTED: \n".$expected_php);
                Ak::trace("EDITAGS: \n".$editags_code);
            }

            $this->assertEqual($php, $expected_php);
        }

        if($all_in_one_test){            
            $Editags =& new Editags();
            $Editags->init(array('code'=>$multiple_editags));
            $php = $Editags->toPhp();
            if($php != $multiple_expected_php){
                Ak::trace("GENERATED: \n".$php);
                Ak::trace("EXPECTED: \n".$multiple_expected_php);
                Ak::trace("EDITAGS: \n".$editags_code);
            }
            $this->assertEqual($php, $multiple_expected_php);
        }
    }
}

?>
