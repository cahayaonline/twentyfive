<?php
class ContactUs extends MainPageStore {
	public function onLoad($param) {		        
		parent::onLoad($param);
        $this->seletectedHomeMenu=true;
		if (!$this->IsPostBack&&!$this->IsCallBack) {
            $this->populateTopCart();
		}
	}
}
		