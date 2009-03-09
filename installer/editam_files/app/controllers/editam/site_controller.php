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

class Editam_SiteController extends EditamController
{
    var $app_helpers = 'editags,layout';
    var $models = 'page,snippet';
    var $layout = 'website';

    function __construct(){
    	parent::__construct();
    	$this->skipBeforeFilter('authenticate');
    }
    
    function index()
    {
        $this->redirectToAction('show_page');
    }

    function show_page()
    {
        $this->Page =& new Page();
        $this->Page->initiateBehaviour(&$this);

        if(
        $Page =& $this->Page->findByUrl(@$this->params['url']) ||
        $Page =& $this->Page->findMissingPageForUrl(@$this->params['url'])
        ){
            $this->Page =& $Page;
            $this->Page->initiateBehaviour(&$this);
            $this->Page->process();
        }else{
            $this->render(array('template'=>'not_found','status' => 404));
        }
    }
    
    function not_found(){
    	$this->render(array('template'=>'not_found','status' => 404));
    }
    
    function error(){
        $this->render(array('template'=>'error500','status' => 500));
    }

}

?>