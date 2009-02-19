<?php

// +----------------------------------------------------------------------+
// Editam is a content management platform developed by Akelos Media, S.L.|
// Copyright (C) 2006 - 2007 Akelos Media, S.L.                           |
//                                                                        |
// This program is free software; you can redistribute it and/or modify   |
// it under the terms of the GNU General Public License version 3 as      |
// published by the Free Software Foundation.                             |
//                                                                        |
// This program is distributed in the hope that it will be useful, but    |
// WITHOUT ANY WARRANTY; without even the implied warranty of             |
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                   |
// See the GNU General Public License for more details.                   |
//                                                                        |
// You should have received a copy of the GNU General Public License      |
// along with this program; if not, see http://www.gnu.org/licenses or    |
// write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth |
// Floor, Boston, MA 02110-1301 USA.                                      |
//                                                                        |
// You can contact Akelos Media, S.L. headquarters at                     |
// C/ Pasodoble Amparito Roca, 6, 46240 - Carlet (Valencia) - Spain       |
// or at email address contact@akelos.com.                                |
//                                                                        |
// The interactive user interfaces in modified source and object code     |
// versions of this program must display Appropriate Legal Notices, as    |
// required under Section 5 of the GNU General Public License version 3.  |
//                                                                        |
// In accordance with Section 7(b) of the GNU General Public License      |
// version 3, these Appropriate Legal Notices must retain the display of  |
// the "Powered by Editam" logo. If the display of the logo is not        |
// reasonably feasible for technical reasons, the Appropriate Legal       |
// Notices must display the words "Powered by Editam".                    |
// +----------------------------------------------------------------------+

class Editam_FileController extends EditamController
{
    var $selected_tab = 'Assets';

    function index()
    {
        $this->redirectToAction('listing');
    }

    function listing()
    {
        $this->file_pages = $this->pagination_helper->getPaginator($this->File, array('items_per_page' => 10));
        $this->files = $this->File->find('all', $this->pagination_helper->getFindOptions($this->File));
    }

    function show()
    {
        $this->file = $this->File->find(@$this->params['id']);
    }


    function upload()
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

    function add()
    {
        if(!empty($this->params['file'])){
            $this->File->setAttributes($this->params['file']);

            if ($this->Request->isPost() && $this->File->save()){
                $this->flash['notice'] = $this->t('File was successfully created.');
                $this->redirectTo(array('action' => 'show', 'id' => $this->File->getId()));
            }
        }
    }

    function edit()
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

    function destroy()
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

?>