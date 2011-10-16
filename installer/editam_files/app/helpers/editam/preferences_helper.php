<?php

# Author Bermi Ferrer - MIT LICENSE

class PreferencesHelper extends AkActionViewHelper
{ 
    public function cancel_link($url = array('action' => 'listing'))
    {
        return $this->_controller->url_helper->link_to($this->t('Cancel'),$url, array('class'=>'action'));
    }

    public function save_button()
    {
        return '<input type="submit" value="'.$this->_controller->t('Save').'" class="primary" />';
    }

    public function save_and_continue_button()
    {
        return '<input id="save_and_continue" type="button" 
        onclick="Preferences.submitAndContinueEditing($(\'preferences_form\'));" value="'.$this->_controller->t('Save and continue editing').'" />';
    }
    
    public function confirm_delete()
    {
        return '<input type="submit" value="'.$this->t('Delete').'" class="primary" /> ';
    }

    public function link_to_show(&$record)
    {
        return $this->_controller->url_helper->link_to($this->_controller->t('Show'), array('action' => 'show', 'id' => $record->getId()), array('class'=>'action'));
    }  

    public function link_to_edit(&$record)
    {
        return $this->_controller->url_helper->link_to($this->_controller->t('Edit'), array('action' => 'edit', 'id' => $record->getId()), array('class'=>'action'));
    }  

    public function link_to_destroy(&$record)
    {
        return $this->_controller->url_helper->link_to($this->_controller->t('Delete'), array('action' => 'destroy', 'id' => $record->getId()), array('class'=>'action'));
    }
    
    public function render_preference(&$Preference)
    {
        if($Preference->is_core) {
            $PreferenceHandler = new CorePreferences();
        }
        $view_getter_method_name = 'get'.AkInflector::camelize($Preference->name).'FormView';
        if(isset($PreferenceHandler) && method_exists($PreferenceHandler, $view_getter_method_name)){
            $field_view = $PreferenceHandler->$view_getter_method_name($Preference);
        }elseif (!$Preference->is_editable){
            return '';
        }
        $this->_controller->Preference =& $Preference;
        return $this->_controller->render(array('partial'=>'../form_fields/'.(empty($field_view) ? 'text' : $field_view)));
    }
    
    public function time_zone_selector()
    {
    }
}

?>