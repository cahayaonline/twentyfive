<?php
class ShoppingCart extends MainPageStore {        
	public function onLoad($param) {		        
		parent::onLoad($param);        
        $this->createObjProduct();
        $this->createObjFinance();
		if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (!isset($_SESSION['currentPageShoppingCart'])||$_SESSION['currentPageShoppingCart']['page_name']!='ShoppingCart') {
				$_SESSION['currentPageMember']=array('page_name'=>'ShoppingCart','page_num'=>0,'search'=>false);												
			}            
            $this->populateTopCart();
            $this->populateShoppingCart();
		}
	}   
    public function populateShoppingCart() {                
        $this->RepeaterShoppingCart->DataSource=$_SESSION['currentMainPageStore']['cart'];
        $this->RepeaterShoppingCart->dataBind();
    }
    public function deleteItemShoppingChart ($sender,$param) {
        $product_id=$this->getDataKeyField($sender, $this->RepeaterShoppingCart);
        $deleteIndex=$this->getIndexFromList($_SESSION['currentMainPageStore']['cart'],'product_id',$product_id);                
        if($deleteIndex>=0) {                        
            unset($_SESSION['currentMainPageStore']['cart'][$deleteIndex]);            
        }        
        $this->populateShoppingCart();
    }
}
		