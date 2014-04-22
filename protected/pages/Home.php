<?php
class Home extends MainPageStore {
	public function onLoad($param) {		        
		parent::onLoad($param);
        $this->seletectedHomeMenu=true;
        $this->createObjFinance();
		if (!$this->IsPostBack&&!$this->IsCallBack) {
            $this->populateTopCart();
		}
	}
}
		