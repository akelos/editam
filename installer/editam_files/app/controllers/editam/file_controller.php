<?php

# Author Bermi Ferrer - MIT LICENSE

class Editam_FileController extends EditamController
{
    public $selected_tab = 'Assets';

    public function index()
    {
        $this->redirectToAction('listing');
    }

    public function listing()
    {
        $this->file_pages = $this->pagination_helper->getPaginator($this->File, array('items_per_page' => 10));
        $this->files = $this->File->find('all', $this->pagination_helper->getFindOptions($this->File));
    }

    public function show()
    {
        $this->file = $this->File->find(@$this->params['id']);
    }


    public function upload()
    {
        if($this->file_upload_helper->handle_partial_upload()){
            return ;
        }else{

            if(!empty($this->params['file'])){
                if ($this->Request->isPost() && !empty($this->params['file'])){
                    $this->_doFileUpload($this->params['file']);
                }
            }
        }
    }

    public function add()
    {
        if(!empty($this->params['file'])){
            $this->File->setAttributes($this->params['file']);

            if ($this->Request->isPost() && $this->File->save()){
                $this->flash['notice'] = $this->t('File was successfully created.');
                $this->redirectTo(array('action' => 'show', 'id' => $this->File->getId()));
            }
        }
    }

    public function edit()
    {
        if(!empty($this->params['file']) && !empty($this->params['id'])){
            $this->file = $this->File->find($this->params['id']);
            $this->file->setAttributes($this->params['file']);
            if($this->Request->isPost() && $this->file->save()){
                $this->flash['notice'] = $this->t('File was successfully updated.');
                $this->redirectTo(array('action' => 'show', 'id' => $this->file->getId()));
            }
        }
    }

    public function destroy()
    {
        if(!empty($this->params['id'])){
            $this->file = $this->File->find($this->params['id']);
            if($this->Request->isPost()){
                $this->file->destroy();
                $this->redirectTo(array('action' => 'listing'));
            }
        }
    }
}

