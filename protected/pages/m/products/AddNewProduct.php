<?php
prado::using ('Application.pages.m.products.MainPageProduct');
class AddNewProduct extends MainPageProduct {
    public function onLoad($param) {			
        parent::onLoad($param);				
        $this->showAddNewProduct=true;        
        if (!$this->IsPostBack&&!$this->IsCallBack) {									
            $this->listCategories->DataSource=$this->product->getListCategoryProduct('listbox');
            $this->listCategories->dataBind();
        }
    }
    public function checkProductID($sender,$param) {
        $product_id=$param->Value;
        if ($product_id != '') {
            try {						
				if ($this->DB->checkRecordIsExist('product_id','product',$product_id))
					throw new Exception ("Product ID ($product_id) has not available");														
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
				if ($this->DB->checkRecordIsExist('product_id','product',$sku))
					throw new Exception ("SKU ($sku) has not available");														
			}catch (Exception $e) {
				$param->IsValid=false;
				$sender->Text=$e->getMessage();
			}
        }
    } 
    public function saveData ($sender,$param) {
        if ($this->IsValid) {            
            $product_id=$this->txtProductID->Text;
            $product_name=ucwords(addslashes($this->txtProductName->Text));
            $model=addslashes($this->txtModel->Text);
            $sku=addslashes($this->txtSKU->Text);
            $qty=addslashes($this->txtQty->Text);            
            $price=$this->finance->toInteger(addslashes($this->txtPrice->Text));            
            $defaultomset=$this->finance->toInteger(addslashes($this->txtDefaultOmset->Text));
            $status=$this->cmbStatus->Text;
            $description=  addslashes($this->txtDesc->Text);
            $description_details=  addslashes($this->txtDescDetails->Text);
            $dateadded=date('Y-m-d H:m:s');
            $datemodified=$dateadded;           
            $str = "INSERT INTO product(product_id,product_name,model,description,description_details,sku,price,default_omset,status,date_added,date_modified) VALUES($product_id,'$product_name','$model','$description','$description_details','$sku','$price','$defaultomset','$status','$dateadded','$datemodified')";
            $this->DB->query('BEGIN');
            if ($this->DB->insertRecord($str)) {
                $this->DB->insertRecord("INSERT INTO product_to_qty VALUES (NULL,$product_id,'$qty','$dateadded','$datemodified')");
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
?>
