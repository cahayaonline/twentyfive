<?php

class MainPageSales extends MainPageAccount {    
    /**
     * menyimpan data product_id
     * @var int
     */
    protected $product_id;
    /**
     * menyimpan data order
     * @var array     
     */
    protected $dataOrder = array();
    public function onLoad ($param) {		
		parent::onLoad($param);			
		$this->showSales=true;                       
	}
}
?>