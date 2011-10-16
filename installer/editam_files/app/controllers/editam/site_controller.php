<?php

# Author Bermi Ferrer - MIT LICENSE

class Editam_SiteController extends EditamController
{
    public $app_helpers = 'editags,layout';
    public $models = 'page,snippet';
    public $layout = 'website';

    public function __construct(){
        parent::__construct();
        $this->skipBeforeFilter('authenticate');
    }
    
    public function index()
    {
        $this->redirectToAction('show_page');
    }

    public function show_page()
    {
        $this->Page = new Page();
        $this->Page->initiateBehavior($this);

        if(
        $Page = $this->Page->findByUrl(@$this->params['url']) ||
        $Page = $this->Page->findMissingPageForUrl(@$this->params['url'])
        ){
            $this->Page = $Page;
            $this->Page->initiateBehavior($this);
            $this->Page->process();
        }else{
            $this->render(array('template'=>'not_found','status' => 404));
        }
    }
    
    public function not_found(){
        $this->render(array('template'=>'not_found','status' => 404));
    }
    
    public function error(){
        $this->render(array('template'=>'error500','status' => 500));
    }

}
