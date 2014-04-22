<?php

class MainPageReport extends MainPageSystem {
    /**
     * menampilkan halaman home report
     * @var boolean
     */
    public $showReportHome=false;   
    public function onLoad ($param) {		
		parent::onLoad($param);			
		$this->showReports=true;                       
	}
}
?>