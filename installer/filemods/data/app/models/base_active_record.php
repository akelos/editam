<?php
    $search_replace = array(
            array(
                "searched" => "/(class\s*BaseActiveRecord\s*extends\s*AkActiveRecord\s*{)/",
                "detect_modified" => "/function\s*init[\w\W]*function\s*validatesEditagsField[\w\W]*function\s*_engageHooks[\w\W]*'\<\?php '\);\s*\}\s*\}/",
                "replaced" => "$1\n    function init(\$attributes)
    {
        \$this->_engageHooks(\$attributes);
        return parent::init(\$attributes);
    }

    public function validatesEditagsField(\$column = 'content', \$can_have_php = true)
    {
        \$Editags = new Editags();

        \$Editags->Parser->available_helpers = EditagsHelper::_getEditagsHelperMethods();

        \$this->_editags_php = \$Editags->toPhp(\$this->get(\$column), \$can_have_php);

        if(\$this->validatesPhpContent(\$this->_editags_php) && !\$Editags->hasErrors()){
            return true;
        }elseif(\$Editags->hasErrors()){

            \$errors = \$this->t(' syntax error').
            \"<ul><li>\".join(\"</li>\\n<li>\",\$Editags->Parser->errors).\"</li></ul>\";
            \$this->addError(\$column, \$errors);
            return false;
            
        }elseif(!\$can_have_php){

            \$errors = \$this->t('%model security error', array('%model'=> \$this->getModelName())).
            \"<ul><li>\".join(\"</li>\\n<li>\",\$this->PhpSanitizer->getErrors()).\"</li></ul><hr />
                <h3>\".\$this->t('Compiled %model', array('%model'=> \$this->getModelName())).\"</h3>
                <pre>\".htmlentities(\$this->_editags_php).'</pre><hr />';
            \$this->addError(\$column, \$errors);
            return false;
        }
        return true;
    }

    public function validatesPhpContent(\$php_content)
    {
        \$this->PhpSanitizer = new AkPhpCodeSanitizer();
        \$this->PhpSanitizer->secure_active_record_method_calls = true;
        return \$this->PhpSanitizer->isCodeSecure(\$php_content, false);
    }

    public function _engageHooks(\$attributes)
    {
        \$hook_file = AK_MODELS_DIR.DS.'hooks'.DS.AkInflector::underscore(\$this->getModelName()).'.php';
        if(file_exists(\$hook_file)){
            eval(' ?>'.file_get_contents(\$hook_file).'<?php ');
        }
    }\n"
            )
    );
