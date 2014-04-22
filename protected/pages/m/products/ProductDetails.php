<?php
prado::using ('Application.pages.m.products.MainPageProduct');
class ProductDetails extends MainPageProduct {
    /**
     * 
     * @param view general
     */
    public $showViewGeneral=false;
    /**
     * 
     * @param view stock
     */
    public $showViewStock=false;
    /**
     * 
     * @param view stock
     */
    public $showViewImages=false;
	public function onLoad($param) {		
		parent::onLoad($param);			      
		$this->showProductDetails=true;                
        if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (!isset($_SESSION['currentPageProductDetails'])||$_SESSION['currentPageProductDetails']['page_name']!='m.sales.ProductDetails') {
                $_SESSION['currentPageProductDetails']=array('page_name'=>'m.sales.ProductDetails','dataProduct'=>array(),'process'=>'general');												
            }                        
            $this->processDetails();
        }        
	} 
    public function processDetails() {   
        try {
            $product_id=addslashes(trim($this->request['id']));
            $_SESSION['currentPageProductDetails']['process']=addslashes(trim($this->request['p']));            
            if ($_SESSION['currentPageProductDetails']['dataProduct']['product_id']!=$product_id) {                        
                $str = "SELECT product_id,product_name,model,price,status FROM product WHERE product_id='$product_id'";
                $this->DB->setFieldTable(array('product_id','product_name','model','price','status'));
                $r=$this->DB->getRecord($str);                                   
                if (isset($r[1])){           
                    $_SESSION['currentPageProductDetails']['dataProduct']=$r[1];                                        
                }else {
                    throw new Exception ('No Product');
                }               
            }
            $this->dataProduct=$_SESSION['currentPageProductDetails']['dataProduct'];                                                
            switch ($_SESSION['currentPageProductDetails']['process']){
                case 'general' :
                    $this->idProcess='add';   
                    $this->showViewGeneral=true;
                    $this->populateData();
                break;
                case 'stock' :
                    $this->idProcess='add';
                    $this->showViewStock=true;
                    $this->populateDataStock();
                break;
                case 'images' :                    
                    $this->idProcess='add';
                    $this->showViewImages=true;                                        
                    $this->populateDataImages();
                break;
                default :
                    throw new Exception ('Process ID undefined !!!');
            }
        }catch (Exception $e) {                                       
            $_SESSION['currentPageProductDetails']['dataProduct']=array('product_name'=>$e->getMessage());
        }  
    } 
    private function populateData () {
       
    }
    private function populateDataStock () {        
        $product_id=$_SESSION['currentPageProductDetails']['dataProduct']['product_id'];
        $str = "SELECT idproduct_qty,qty,date_added,date_modified FROM product_to_qty WHERE product_id=$product_id ORDER BY date_modified DESC";
        $this->DB->setFieldTable(array('idproduct_qty','qty','date_added','date_modified'));
        $r=$this->DB->getRecord($str);
        $this->RepeaterStock->DataSource=$r;
        $this->RepeaterStock->dataBind();
    }
    public function itemCreated($sender,$param){
        $item=$param->Item;
        if($item->ItemType==='EditItem') {
            // set column width of textboxes            
            $item->QuantityColumn->TextBox->CssClass='input-mini';            
            $item->QuantityColumn->TextBox->Attributes->OnKeyUp='formatangka(this,true)';
        }
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='EditItem') {
            // add an aleart dialog to delete buttons
            $item->DeleteColumn->Button->Attributes->onclick='if(!confirm(\'Are you sure?\')) return false;';
        }
    }
    public function editItem($sender,$param) {
        $this->idProcess='add';
        $this->showViewStock=true;
        $this->RepeaterStock->EditItemIndex=$param->Item->ItemIndex;
        $this->populateDataStock ();        
    }
    public function cancelItem($sender,$param) {
        $this->idProcess='add';
        $this->showViewStock=true;
        $this->RepeaterStock->EditItemIndex=-1;
        $this->populateDataStock ();
    }
    public function saveItem($sender,$param) {
        $this->idProcess='add';
        $this->showViewStock=true;
        $item=$param->Item;
        $id=$this->RepeaterStock->DataKeys[$item->ItemIndex];
        $qty=$item->QuantityColumn->TextBox->Text;
        $this->DB->updateRecord("UPDATE product_to_qty SET qty='$qty' WHERE idproduct_qty=$id");
        $this->RepeaterStock->EditItemIndex=-1;
        $this->populateDataStock ();
    }
    public function deleteItem($sender,$param) {
        $this->idProcess='add';
        $this->showViewStock=true;
        $id=$this->RepeaterStock->DataKeys[$param->Item->ItemIndex];
        $this->DB->deleteRecord("product_to_qty WHERE idproduct_qty=$id");        
        $this->RepeaterStock->EditItemIndex=-1;
        $this->populateDataStock ();
    }
    public function addNewQTY($sender,$param) {
        $this->idProcess='add';
        $this->showViewStock=true;
        if ($this->IsValid) {            
            $product_id=$_SESSION['currentPageProductDetails']['dataProduct']['product_id'];
            $qty=$this->txtQTY->Text;
            $str = "INSERT INTO product_to_qty (product_id,qty,date_added,date_modified) VALUES ($product_id,$qty,NOW(),NOW())";
            $this->DB->insertRecord($str);
            $this->populateDataStock ();
        }
    }
    //images
    private function populateDataImages () {      
       $product_id=$_SESSION['currentPageProductDetails']['dataProduct']['product_id'];
       $str = "SELECT product_image_id,dirgambar,tipegambar,keterangan,sort_order FROM product_image WHERE product_id=$product_id ORDER BY sort_order ASC";
       $this->DB->setFieldTable(array('product_image_id','dirgambar','tipegambar','keterangan','sort_order'));
       $r=$this->DB->getRecord($str);              
       $this->RepeaterProductImages->DataSource=$r;
       $this->RepeaterProductImages->dataBind();
    }
    public function fileUploaded($sender,$param) {
        $this->idProcess='add';
        $this->showViewImages=true;
        if($sender->HasFile) {
            $product_id=$_SESSION['currentPageProductDetails']['dataProduct']['product_id'];
            $idimage=$this->DB->getMaxOfRecord('product_image_id','product_image')+1;
            $type=$this->setup->getImageFileType ($sender->FileName);
            $dirgambar='';                        
            $filename="$idimage.$type";            
            $filename_thumb="{$idimage}_thumb.$type";
            $filename_thumb_store="{$idimage}_thumb_store.$type";
            $folder=$this->setup->getDirImagesProduct();            
            $sender->saveAs($folder.$filename); 
            $this->setup->load($folder.$filename);
            $this->setup->resize(230,173);
            $this->setup->save($folder.$filename_thumb);
            $this->setup->resize(200,200);
            $this->setup->save($folder.$filename_thumb_store);
            $this->setup->destroyImage();
            $str = "INSERT INTO product_image (product_image_id,product_id,dirgambar,tipegambar,sort_order) VALUES ($idimage,$product_id,'$dirgambar','$type',0)";
            $this->DB->insertRecord($str);
            $this->redirect('m.products.ProductDetails',array('id'=>$product_id,'p'=>'images'));            
        }
    }
    public function deleteImage($sender,$param) {        
        $product_id=$_SESSION['currentPageProductDetails']['dataProduct']['product_id'];
        $id=$this->getDataKeyField($sender,$this->RepeaterProductImages);
        $fileimage=explode('_',$sender->CommandParameter);             
        $filename=$this->setup->getDirImagesProduct().$fileimage[1].$id.'.'.$fileimage[0];
        $this->DB->deleteRecord("product_image WHERE product_image_id='$id'");
        $this->setup->removeImage($filename);        
        $this->redirect('m.products.ProductDetails',array('id'=>$product_id,'p'=>'images'));
    }
}