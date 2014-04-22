<?php

class MainPageProduct extends MainPageSystem {    
    protected $product_id;
    public function onLoad ($param) {		
		parent::onLoad($param);			
		$this->showProducts=true;                
        $this->createObjProduct();
        $this->createObjFinance();
	}
}
?>