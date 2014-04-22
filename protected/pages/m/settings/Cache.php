<?php
prado::using ('Application.pages.m.settings.MainPageSetting');
class Cache extends MainPageSetting {    
	public function onLoad($param) {		
		parent::onLoad($param);				
		$this->showCache=true;              
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageClearCache'])||$_SESSION['currentPageClearCache']['page_name']!='m.settings.ClearCache') {
				$_SESSION['currentPageClearCache']=array('page_name'=>'m.settings.ClearCache','page_num'=>0);												
			}            
		}
	}    
    public function hapusCache ($sender,$param) {
        if ($this->Application->Cache) {
            $this->Application->Cache->flush();           
            $this->message->Text='<div class="alert alert-success"><button class="close" data-dismiss="alert">×</button><strong>Success!</strong> Cache cleared.</div>';                      
        }else {
            $this->message->Text='<div class="alert alert-info"><button class="close" data-dismiss="alert">×</button><strong>Info!</strong> Sistem cache tidak aktif.</div>';
        }
    }
}