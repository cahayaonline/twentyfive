<?php
prado::using ('Application.pages.m.products.MainPageProduct');
class Categories extends MainPageProduct {
	public function onLoad($param) {		
		parent::onLoad($param);				
        $this->showCategoryProduct=true;
		if (!$this->IsPostBack&&!$this->IsCallBack) {          
            $this->populateData();
		}
	}   
    private function populateData () {
        $categories=$this->product->getCategories(0);       
		$this->RepeaterS->DataSource= $categories;
		$this->RepeaterS->dataBind();	
    }
    public function addProcess ($sender,$param) { 
        $this->idProcess='add';                
        $this->cmbParentCategory->DataSource=$this->product->getListCategoryProduct('dropdownlist');
        $this->cmbParentCategory->dataBind();
    }
    public function checkCategoryName($sender,$param) {						
        $this->idProcess=$sender->getId()=='checkeditcategoryname'?'edit':'add';
        $name=$param->Value;		
        if ($name != '') {
            try {   
                if ($this->hiddencategoryname->Value!=$name) {
                    if ($this->DB->checkRecordIsExist('name','category_description',$name)) {                                
                        throw new Exception ("Category name ($name) has not available");		
                    }                               
                }
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }						
    }        
    public function saveData ($sender,$param) {            
        if ($this->IsValid) {
            $name= ucwords(addslashes($this->txtCategoryName->Text));           
            $date_add=date('Y-m-d H:m:s');
            $date_modified=$date_add;
            $parent_id=$this->cmbParentCategory->Text =='none'?0:$this->cmbParentCategory->Text;            
            $images='test';            
            $top=($this->chkTOP->Checked==1&&$parent_id==0)?1:0;
            $str = "INSERT INTO category (category_id,images,parent_id,top,sort_order,date_added,date_modified) VALUES (NULL,'$images','$parent_id',$top,LAST_INSERT_ID(),'$date_add','$date_modified')";            
            $this->DB->query ('BEGIN');
            if ($this->DB->insertRecord($str)) {
                $desc=addslashes($this->txtDesc->Text);
                $metakeyword=addslashes($this->txtMetaTagKeyword->Text);
                $metadesc=addslashes($this->txtMetaTagDesc->Text);
                $str = "INSERT INTO category_description (category_id,language_id,name,description,meta_description,meta_keyword) VALUES (LAST_INSERT_ID(),1,'$name','$desc','$metadesc','$metakeyword')";            
                $this->DB->insertRecord($str);
                $this->DB->query ('COMMIT');
            }else{
                $this->DB->query ('ROLLBACK');
            }            
            $this->product->redirect('m.products.Categories');
        }
    }
    public function editRecord ($sender,$param) { 
        $this->idProcess='edit';
        $id=$this->getDataKeyField($sender, $this->RepeaterS);
        $str = "SELECT parent_id,name,description,meta_description,meta_keyword,top FROM category c,category_description cd WHERE c.category_id=cd.category_id AND c.category_id='$id'";
        $this->DB->setFieldTable (array('parent_id','name','description','meta_description','meta_keyword','top'));
        $r=$this->DB->getRecord($str);
        $this->hiddencategoryid->Value=$id;
        $this->txtEditCategoryName->Text=$r[1]['name'];
        $this->txtEditDesc->Text=$r[1]['description'];
        $this->txtEditMetaTagDesc->Text=$r[1]['meta_description'];
        $this->txtEditMetaTagKeyword->Text=$r[1]['meta_keyword'];
        $listcategory=$this->product->removeIdFromArray($this->product->getListCategoryProduct('dropdownlist'),$id);
        $this->cmbEditParentCategory->DataSource=$listcategory;
        $this->cmbEditParentCategory->Text=$r[1]['parent_id'];
        $this->cmbEditParentCategory->dataBind();
        $this->chkEditTOP->Checked=$r[1]['top'];
        $this->hiddencategoryname->Value=$r[1]['name'];
    }
    public function updateData ($sender,$param) {            
        if ($this->IsValid) {
            $id=$this->hiddencategoryid->Value;
            $name= ucwords(addslashes($this->txtEditCategoryName->Text));                       
            $date_modified=date('Y-m-d H:m:s');;            
            $parent_id=$this->cmbEditParentCategory->Text =='none'?0:$this->cmbEditParentCategory->Text;                        
            $top=($this->chkEditTOP->Checked==1&&$parent_id==0)?1:0;
            $str = "UPDATE category SET parent_id=$parent_id,date_modified='$date_modified',top=$top WHERE category_id='$id'";            
            $this->DB->query ('BEGIN');
            if ($this->DB->updateRecord($str)) {
                $desc=addslashes($this->txtEditDesc->Text);
                $metakeyword=addslashes($this->txtEditMetaTagKeyword->Text);
                $metadesc=addslashes($this->txtEditMetaTagDesc->Text);
                $str = "UPDATE category_description SET name='$name',description='$desc',meta_description='$metadesc',meta_keyword='$metakeyword' WHERE category_id='$id'";            
                $this->DB->updateRecord($str);
                $this->DB->query ('COMMIT');
            }else{
                $this->DB->query ('ROLLBACK');
            } 
            $this->product->redirect('m.products.Categories');              
        }
    }
    public function deleteRecord ($sender,$param) {            
        $id=$this->getDataKeyField($sender, $this->RepeaterS);
        $this->DB->deleteRecord("category WHERE category_id=$id OR parent_id=$id");       
        $this->populateData();
    }
}
