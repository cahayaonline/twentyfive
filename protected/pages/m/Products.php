<?php
prado::using ('Application.pages.m.products.MainPageProduct');
class Products extends MainPageProduct {    
	public function onLoad($param) {		
		parent::onLoad($param);				
		$this->showProductHome=true;      
        $this->createObjFinance();
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageProduct'])||$_SESSION['currentPageProduct']['page_name']!='m.Products') {
				$_SESSION['currentPageProduct']=array('page_name'=>'m.Products','page_num'=>0,'search'=>false,'category_id'=>'none','pagesize'=>10);												
			}            
            
//            $category=$this->product->getListCategoryProduct('dropdownlist');
//            $category[0]='All Category';
//            $this->cmbCategory->DataSource=$category;
//            $this->cmbCategory->Text=$_SESSION['currentPageProduct']['category_id'];
//            $this->cmbCategory->dataBind();
//            $_SESSION['currentPageProduct']['search']=false;
            $this->cmbPageSize->Text=$_SESSION['currentPageProduct']['pagesize'];
			$this->populateData();
		}
	}
    public function changeCategory ($sender,$param) {
		$_SESSION['currentPageProduct']['category_id']=$this->cmbCategory->Text;
        $this->populateData($_SESSION['currentPageProduct']['search']);
	}	
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageProduct']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageProduct']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageProduct']['search']=true;
        $this->populateData($_SESSION['currentPageProduct']['search']);
	}
    public function changePageSize ($sender,$param) {
        $_SESSION['currentPageProduct']['page_num']=0;
		$_SESSION['currentPageProduct']['pagesize']=$this->cmbPageSize->Text;
        $this->populateData($_SESSION['currentPageProduct']['search']);
	}
    private function populateData ($search=false) {
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageProduct']['page_num'];		
        $category_id=$_SESSION['currentPageProduct']['category_id'];
        $str_category_id=$category_id==0?'':"LEFT JOIN product_to_category ptc ON (ptc.product_id=p.product_id) WHERE category_id=$category_id";
        $str = "SELECT p.product_id,p.sku,product_name,model,price,default_omset,status FROM product p $str_category_id";
        if ($search){ 
            $kriteria=$this->txtKriteria->Text;            
            switch ($this->cmbKriteria->Text){
                case 'product_id' :
                    $cluasa=$category_id==0?"WHERE product_id='$kriteria'":"AND product_id='$kriteria'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("product p $str_category_id $cluasa",'p.product_id');
                    $str = "$str $cluasa";
                break;
                case 'product_name' :
                    $cluasa=$category_id==0?"WHERE product_name LIKE '%$kriteria%'":"AND product_name LIKE '%$kriteria%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("product p $str_category_id $cluasa",'p.product_id');
                    $str = "$str $cluasa";
                break;
            }           
            
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("product p $str_category_id",'p.product_id');
        }
        if ($_SESSION['currentPageProduct']['pagesize'] == 'all') {
            $str = "$str ORDER BY date_modified DESC";
        }else{
            $this->RepeaterS->PageSize=$_SESSION['currentPageProduct']['pagesize'];
            $this->RepeaterS->VirtualItemCount=$jumlah_baris;
            $currentPage=$this->RepeaterS->CurrentPageIndex;
            $offset=$currentPage*$this->RepeaterS->PageSize;		
            $itemcount=$this->RepeaterS->VirtualItemCount;
            $limit=$this->RepeaterS->PageSize;
            if (($offset+$limit)>$itemcount) {
                $limit=$itemcount-$offset;
            }
            if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageProduct']['page_num']=0;}
            $str = "$str ORDER BY date_modified DESC LIMIT $offset,$limit";
        }
		$this->DB->setFieldTable(array('product_id','sku','product_name','model','price','default_omset','status'));
		$r=$this->DB->getRecord($str,$offset+1);
        $result=array();                     
		while (list($k,$v)=each($r)) {            
            $this->product->setProductID($v['product_id']);            
            $v['sku']=$v['sku']==''?'-':$v['sku'];
            $v['stock']=$this->product->getStock();
            $result[$k]=$v;
        }        
		$this->RepeaterS->DataSource=$result;
		$this->RepeaterS->dataBind();		
    }
    public function addProcess ($sender,$param) {
        $this->idProcess='add';
    }
    public function deleteRecord($sender,$param) {
        $id=$this->getDataKeyField($sender,$this->RepeaterS);
        $this->DB->deleteRecord("product WHERE product_id='$id'");
        $this->populateData();
    }
}