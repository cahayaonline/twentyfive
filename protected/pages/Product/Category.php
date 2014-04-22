<?php

class Category extends MainPageStore {
    public $dataCategory=array();
    private $parent_id;
    private $category_id;
	public function onLoad($param) {		
		parent::onLoad($param);	        
        $this->createObjProduct();
		if (!$this->IsPostBack&&!$this->IsCallBack) {
            $this->parent_id=addslashes(trim($this->request['parent']==0?$this->request['category']:$this->request['parent']));
            $this->category_id = addslashes(trim($this->request['category']));
            $this->populateData();
		}
	}
    protected function populateData() {
        $str = "SELECT name,description FROM category_description WHERE category_id={$this->category_id}";
        $this->DB->setFieldTable (array('name','description'));
        $r=$this->Page->DB->getRecord($str);
        if (isset($r[1])){            
            $this->dataCategory=$r[1]; 
            $this->product->setCategoryID($this->category_id);
            $listproduct=$this->product->getProduct(1);            
            $this->RepeaterS->DataSource=$listproduct;
            $this->RepeaterS->dataBind();
        }else{
            $this->dataCategory['name']='Kategori tidak ditemukan';            
        }
        $this->Page->Title="{$this->dataCategory['name']} | CahayaOnline.com";
    }
}
		