<?php
class CatalogList extends MainPageStore {    
    private $category_id;
	public function onLoad($param) {		        
		parent::onLoad($param);        
        $this->createObjProduct();
        $this->createObjFinance();
		if (!$this->IsPostBack&&!$this->IsCallBack) {
            $this->category_id = addslashes(trim($this->request['id']));            
            if (!isset($_SESSION['currentPageCatalogList'])||$_SESSION['currentPageCatalogList']['page_name']!='CatalogList') {                                
                $datacategory=$this->product->getDataCategory($this->category_id);
                $datacategory['parent_id']=$datacategory['parent_id']==0?$datacategory['category_id']:$datacategory['parent_id'];
				$_SESSION['currentPageCatalogList']=array('page_name'=>'CatalogList','page_num'=>0,'categoryData'=>$datacategory);												                
			}elseif ($_SESSION['currentPageCatalogList']['categoryData']['category_id']!=$this->category_id) {
                $datacategory=$this->product->getDataCategory($this->category_id);
                $datacategory['parent_id']=$datacategory['parent_id']==0?$datacategory['category_id']:$datacategory['parent_id'];
                $_SESSION['currentPageCatalogList']['categoryData']=$datacategory;
            }                     
            $this->populateSubCategories($_SESSION['currentPageCatalogList']['categoryData']['parent_id']);
            $this->populateData();     
            $this->populateTopCart();           
            
		}
	}   
    public function renderCallback ($sender,$param) {
		$this->RepeaterProduct->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageCatalogList']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageCatalogList']['search']);
	}
    public function populateData () {        
        $datacategory=$_SESSION['currentPageCatalogList']['categoryData'];
        $clausa_category=$datacategory['parent_id']==$datacategory['category_id']?" AND c.parent_id='{$datacategory['parent_id']}'":" AND ptc.category_id='{$datacategory['category_id']}'";
        $str = "SELECT p.product_id,ptc.category_id,c.parent_id,p.product_name,p.model,p.description,p.price,p.default_omset FROM product_to_category ptc LEFT JOIN product p ON (p.product_id=ptc.product_id) LEFT JOIN category c ON (ptc.category_id=c.category_id) WHERE status=1$clausa_category";        
        $jumlah_baris=$this->DB->getCountRowsOfTable ("product_to_category ptc LEFT JOIN product p ON (p.product_id=ptc.product_id) LEFT JOIN category c ON (ptc.category_id=c.category_id) WHERE status=1$clausa_category",'p.product_id');
        $this->RepeaterProduct->CurrentPageIndex=$_SESSION['currentPageCatalogList']['page_num'];				
		$this->RepeaterProduct->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterProduct->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterProduct->PageSize;		
		$itemcount=$this->RepeaterProduct->VirtualItemCount;
		$limit=$this->RepeaterProduct->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageCatalogList']['page_num']=0;}
		$str = "$str $str_order LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('product_id','category_id','parent_id','product_name','model','description','price','default_omset'));
		$r=$this->DB->getRecord($str,$offset+1);        
        $result = array();
        $str = "SELECT product_image_id,dirgambar,tipegambar FROM product_image";
        $this->DB->setFieldTable(array('product_image_id','dirgambar','tipegambar'));
        while (list($k,$v)=each($r)) {
            $image=$this->DB->getRecord("$str WHERE product_id={$v['product_id']} ORDER BY sort_order ASC LIMIT 1");
            $v['image']=isset($image[1])?$this->setup->getImagesProduct($image[1]['dirgambar'].'/'.$image[1]['product_image_id'].'_thumb_store.'.$image[1]['tipegambar']):$this->setup->getImagesProduct('noproduct.jpg');
            $result[$k]=$v;            
        }
        $start=$offset+1;
        $akhir=$offset+count($result);
        $this->paginationInfo->Text="Displaying $start to $akhir (of $jumlah_baris products)";
		$this->RepeaterProduct->DataSource=$result;
		$this->RepeaterProduct->dataBind();
    }
    public function addToCart ($sender,$param) {        
        $product_id=$this->getDataKeyField($sender, $this->RepeaterProduct);
        $str = "SELECT product_image_id,dirgambar,tipegambar FROM product_image WHERE product_id=$product_id ORDER BY sort_order ASC LIMIT 1";
        $this->DB->setFieldTable(array('product_image_id','dirgambar','tipegambar'));
        $r=$this->DB->getRecord($str);        
        $image=isset($r[1])?$this->setup->getImagesProduct($r[1]['dirgambar'].$r[1]['product_image_id'].'_thumb_store.'.$r[1]['tipegambar']):$this->setup->getImagesProduct('noproduct.jpg');        
        $this->product->setProductID($product_id);
        $dataProduct=$this->product->getProduct(2);
        $this->dataProduct=$dataProduct;        
        $index=$this->getIndexFromList($_SESSION['currentMainPageStore']['cart'],'product_id',$product_id);        
        $this->dataProduct['quantity']=$_SESSION['currentMainPageStore']['cart'][$index]['quantity']+1;        
        $this->dataProduct['image']=$image;        
        $this->addItemToCart();        
    }
}
		