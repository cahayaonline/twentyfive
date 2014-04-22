<?php
prado::using ('Application.pages.m.products.MainPageProduct');
class EditProduct extends MainPageProduct {
	public function onLoad($param) {		
		parent::onLoad($param);				
        $this->product_id=addslashes(trim($this->request['id']));
		$this->showEditNewProduct=true;        
		if (!$this->IsPostBack&&!$this->IsCallBack) {			           
            if (!isset($_SESSION['currentPageEditProduct'])||$_SESSION['currentPageEditProduct']['page_name']!='m.products.EditProduct') {
                $_SESSION['currentPageEditProduct']=array('page_name'=>'m.products.EditProduct','dataproduct'=>array());												
            }
			$this->populateData();
		}
        $this->dataProduct=$_SESSION['currentPageEditProduct']['dataproduct'];
	}    
    private function populateData () {
        $product_id=$this->product_id;
        $str = "SELECT product_id,product_name,model,description,description_details,sku,price,default_omset,status FROM product WHERE product_id='$product_id'";
        $this->DB->setFieldTable(array('product_id','product_name','model','description','decription_details','sku','price','default_omset','status'));
        $r=$this->DB->getRecord($str);
        if (isset($r[1])){                        
            $_SESSION['currentPageEditProduct']['dataproduct']=$r[1];            
            $this->listCategories->DataSource=$this->product->getListCategoryProduct('listbox');
            $this->listCategories->dataBind();
            $str = "SELECT category_id FROM product_to_category WHERE product_id='$product_id'";
            $this->DB->setFieldTable(array('category_id'));
            $category=$this->DB->getRecord($str);
            if (isset($category[1])) {
                $newcategory=array();
                foreach ($category as $k=>$v) {
                    $newcategory[$v['category_id']]=$v['category_id'];                    
                }                
                $items=$this->listCategories->Items;                
                foreach ($items as $item) {
                    $item->Selected = (in_array($item->Value,$newcategory));
                }               
            }           
        }else {
            $this->idProcess='view';
        }
    }
    public function checkProductID($sender,$param) {
        $product_id=$param->Value;
        if ($product_id != '') {
            try {						
                if ($this->hiddenProductid->Value!=$product_id) {
                    if ($this->DB->checkRecordIsExist('product_id','product',$product_id))
                        throw new Exception ("Product ID ($product_id) has not available");														
                }
			}catch (Exception $e) {
				$param->IsValid=false;
				$sender->Text=$e->getMessage();
			}
        }
    }   
    public function checkSKU($sender,$param) {
        $sku=trim($param->Value);
        if ($sku != '') {
            try {						
                if ($this->hiddenProductid->Value!=$sku) {
                    if ($this->DB->checkRecordIsExist('product_id','product',$sku))
                        throw new Exception ("SKU ($sku) has not available");														
                }
			}catch (Exception $e) {
				$param->IsValid=false;
				$sender->Text=$e->getMessage();
			}
        }
    }
    public function saveData ($sender,$param) {
        if ($this->IsValid) {            
            $product_id=$this->product_id;
            $product_name=ucwords(addslashes($this->txtProductName->Text));
            $model=addslashes($this->txtModel->Text);            
            $sku=addslashes($this->txtSKU->Text);
            $price=$this->finance->toInteger(addslashes($this->txtPrice->Text));            
            $defaultomset=$this->finance->toInteger(addslashes($this->txtDefaultOmset->Text));
            $status=$this->cmbStatus->Text;
            $description=  addslashes($this->txtDesc->Text);
            $description_details=  addslashes($this->txtDescDetails->Text);
            $dateadded=date('Y-m-d H:m:s');
            $datemodified=$dateadded;           
            $str = "UPDATE product SET product_name='$product_name',model='$model',description='$description',description_details='$description_details',sku='$sku',price='$price',default_omset='$defaultomset',status='$status',date_modified='$datemodified' WHERE product_id=$product_id";
            $this->DB->query('BEGIN');
            if ($this->DB->updateRecord($str)) {
                $this->DB->deleteRecord("product_to_category WHERE product_id=$product_id");
                $indices=$this->listCategories->SelectedIndices;
                foreach ($indices as $index) {
                    $item=$this->listCategories->Items[$index];
                    $value=$item->Value;
                    $this->DB->insertRecord("INSERT INTO product_to_category (product_id,category_id) VALUES ($product_id,$value)");
                }
                $this->DB->query('COMMIT');
            }else{
                $this->DB->query('ROLLBACK');
            }
            $this->product->redirect('m.Products');
        } 
    }
    
}