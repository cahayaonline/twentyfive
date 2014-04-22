<?php
prado::using('Application.MainPage');
class MainPageStore extends MainPage {	
    public $seletectedHomeMenu=false;
    public $CssClassBody='';
    /**
     * data product
     * @var type array
     */
    public $dataProduct=array();
	public function OnPreInit ($param) {	
		parent::onPreInit ($param);
		//instantiasi database		
		$this->DB = $this->Application->getModule ('db')->getLink();
		$this->MasterClass="Application.layouts.MainStore";		
		$this->Theme="defaultstore";
	}
	public function onLoad ($param) {		
		parent::onLoad($param);	
        if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (!isset($_SESSION['currentMainPageStore'])) {
                $_SESSION['currentMainPageStore']=array('catalogmode'=>'list','cart'=>array());             
            }        
        }
	}	
    protected function populateSubCategories ($parent_id) {        
        $str="SELECT c.category_id,cd.name,parent_id FROM category c,category_description cd WHERE c.category_id=cd.category_id AND parent_id=$parent_id";        
        $this->page->DB->setFieldTable(array('category_id','name','parent_id'));
        $r=$this->page->DB->getRecord($str); 
        $this->RepeaterSubCategory->DataSource=$r;
        $this->RepeaterSubCategory->dataBind();
    }
    public function populateTopCart() {    
        $this->repeaterTopCart->DataSource=$_SESSION['currentMainPageStore']['cart'];
        $this->repeaterTopCart->dataBind();
    }
    public function addItemToCart () {
        $index=$this->getIndexFromList($_SESSION['currentMainPageStore']['cart'],'product_id',$this->dataProduct['product_id']);                
        if($index>=0) {                        
            $_SESSION['currentMainPageStore']['cart'][$index]['quantity']=$this->dataProduct['quantity'];            
        }else{
            $_SESSION['currentMainPageStore']['cart'][]=array('product_id'=>$this->dataProduct['product_id'],'product_name'=>$this->dataProduct['product_name'],'model'=>$this->dataProduct['model'],'quantity'=>$this->dataProduct['quantity'],'price'=>$this->dataProduct['price'],'image'=>$this->dataProduct['image']);
        }        
        $this->populateTopCart();
    }
    public function deleteItemCart ($sender,$param) {
        $product_id=$this->getDataKeyField($sender, $this->repeaterTopCart);
        $deleteIndex=$this->getIndexFromList($_SESSION['currentMainPageStore']['cart'],'product_id',$product_id);                
        if($deleteIndex>=0) {                        
            unset($_SESSION['currentMainPageStore']['cart'][$deleteIndex]);            
        }        
        $this->populateTopCart();
    }
}
?>