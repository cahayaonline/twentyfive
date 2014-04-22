<?php
class Product extends MainPageStore {    
    private $product_id;    
	public function onLoad($param) {		        
		parent::onLoad($param);        
        $this->createObjProduct();
        $this->createObjFinance();
        $this->product_id = addslashes(trim($this->request['id']));       
		if (!$this->IsPostBack&&!$this->IsCallBack) {            
            if (isset($_SESSION['currentPageProductDetails'])||$_SESSION['currentPageProductDetails']['page_name']!='Product') {                                                
                $this->product->setProductID($this->product_id);
                $dataProduct=$this->product->getProduct(2);                
				$_SESSION['currentPageProductDetails']=array('page_name'=>'Product','page_num'=>0,'categoryData'=>array(),'dataProduct'=>$dataProduct);												                
			}elseif ($_SESSION['currentPageProductDetails']['dataProduct']['product_id']!=$this->product_id) {
                $this->product->setProductID($this->product_id);
                $dataProduct=$this->product->getProduct(2);
                $_SESSION['currentPageProductDetails']['dataProduct']=$dataProduct;
            }          
            $this->populateDataImage();          
            $this->populateTopCart();
		}
	}   
    public function populateDataImage () {
        $product_id=$_SESSION['currentPageProductDetails']['dataProduct']['product_id'];
        $str = "SELECT product_image_id,product_id,dirgambar,tipegambar FROM product_image WHERE product_id=$product_id ORDER BY sort_order ASC";
        $this->DB->setFieldTable(array('product_image_id','product_id','dirgambar','tipegambar'));
        $r=$this->DB->getRecord($str);        
        $result=array();   
        $_SESSION['currentPageProductDetails']['dataProduct']['firstImageShow']=isset($r[1])?$this->setup->getImagesProduct($r[1]['dirgambar'].$r[1]['product_image_id'].'.'.$r[1]['tipegambar']):$this->setup->getImagesProduct('noproduct.jpg');
        while (list($k,$v)=each($r)) {
            $v['image']=$this->setup->getImagesProduct($v['dirgambar'].'/'.$v['product_image_id'].'.'.$v['tipegambar']);            
            $result[$k]=$v;
        }
        $this->RepeaterImageProductDetails->DataSource=$result;
        $this->RepeaterImageProductDetails->dataBind();
//        
    }
    public function addToCart ($sender,$param) {        
        $this->dataProduct=$_SESSION['currentPageProductDetails']['dataProduct'];  
        $index=$this->getIndexFromList($_SESSION['currentMainPageStore']['cart'],'product_id',$this->dataProduct['product_id']);        ;
        $this->dataProduct['quantity']=$_SESSION['currentMainPageStore']['cart'][$index]['quantity']+$this->txtNumber->Text;        
        $this->dataProduct['image']=$this->dataProduct['firstImageShow'];        
        $this->addItemToCart();
    }   
}
		