<?php

# Author Bermi Ferrer - MIT LICENSE

class FileHelper extends AkActionViewHelper
{ 
    public function cancel_link($url = array('action' => 'listing'))
    {
        if(!empty($this->_controller->Snippet->id)){
            $url['id'] = $this->_controller->Snippet->id;
        }
        return $this->_controller->url_helper->link_to($this->t('Cancel'),$url, array('class'=>'action'));
    }

    public function save_button()
    {
        return '<input type="submit" value="'.$this->_controller->t('Save').'" class="primary" />';
    }


    public function confirm_delete()
    {
        return '<input type="submit" value="'.$this->_controller->t('Delete').'" />';
    }

    public function link_to_show(&$record)
    {
        return $this->_controller->url_helper->link_to($this->_controller->t('Show'), array('action' => 'show', 'id' => $record->getId()));
    }  

    public function link_to_edit(&$record)
    {
        return $this->_controller->url_helper->link_to($this->_controller->t('Edit'), array('action' => 'edit', 'id' => $record->getId()));
    }  

    public function link_to_destroy(&$record)
    {
        return $this->_controller->url_helper->link_to($this->_controller->t('Delete'), array('action' => 'destroy', 'id' => $record->getId()));
    }
}

