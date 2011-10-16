<?php

# Author Bermi Ferrer - MIT LICENSE

class EditamFilter
{
    public function getFilteredContent($Item)
    {
        $Filter = EditamFilter::_getFilterInstance($Item->get('filter'));
        if(!is_object($Filter)){
            return $Item->get('content');
        }
        return $Filter->filter($Item->get('content'));
    }

    public function &_getFilterInstance($filter_name)
    {
        static $filters;
        if(!empty($filters[$filter_name])){
            return $filters[$filter_name];
        }

        if(empty($filter_name) || !in_array($filter_name, array_keys(EditamFilter::getAvailableFilters()))){
            return $GLOBALS['false'];
        }

        if(!file_exists(AK_APP_DIR.DS.'filters'.DS.$filter_name.'_filter.php')){
            trigger_error(Ak::t('Could not find %filter filter file',array('%filter'=>$filter_name),' editam_filter'), E_USER_WARNING);
            return $GLOBALS['false'];
        }
        $filter_class_name = AkInflector::camelize($filter_name.'Filter');
        $filters[$filter_name] = new $filter_class_name();
        return $filters[$filter_name];
    }

    public function getAvailableFilters()
    {
        static $filters = array();
        if(empty($filters)){
            if(defined('EDITAM_AVAILABLE_FILTERS')){
                foreach (Ak::toArray(EDITAM_AVAILABLE_FILTERS) as $filter){
                    $filters[$filter] = Ak::t($filter, null , 'editam_filter');
                }
            }else{
                foreach (AkFileSystem::dir(AK_APP_DIR.DS.'filters') as $file){
                    if(substr($file,-11) == '_filter.php'){
                        $filter = substr($file,0,-11);
                        $filters[$filter] = Ak::t($filter, null, 'editam_filter');
                    }
                }
            }
            $filters = array_map(array('AkInflector','humanize'), $filters);
        }
        return $filters;
    }
}


