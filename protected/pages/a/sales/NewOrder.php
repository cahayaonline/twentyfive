<?php
prado::using ('Application.pages.a.sales.MainPageSales');
class NewOrder extends MainPageSales {
	public function onLoad($param) {		
		parent::onLoad($param);				
        $this->showNewOrder=true;      
        $this->createObjProduct();
        $this->createObjFinance();
        if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (!isset($_SESSION['currentPageNewOrder'])||$_SESSION['currentPageNewOrder']['page_name']!='a.sales.NewOrder') {
                $_SESSION['currentPageNewOrder']=array('page_name'=>'a.sales.NewOrder','page_num'=>0,'datamember'=>$this->Pengguna->getDataUser(),'cart'=>array());												
            }
            $this->dataMember=$_SESSION['currentPageNewOrder']['datamember'];
            $this->processDetails();                    
        }
    }    
    public function createOrder($sender,$param) {
        $member_id=$this->hiddenmemberid->Value;
        if ($member_id=='') {
            $this->errorMessage->Text="<p id='error' class='info'><span class='info_inner'>Unknown Member</span></p>";
        }else {     
            $this->finance->setMemberID($member_id,true,4);
            $dataMember=array('member_id'=>$member_id,
                              'member_name'=>$this->txtMemberName->Text,
                              'ibo'=>$this->txtIBO->Text,
                              'address'=>$this->finance->dataMember['address'],
                              'city'=>$this->finance->dataMember['city'],
                              'postal_code'=>$this->finance->dataMember['postal_code']);            
            $_SESSION['currentPageNewOrder']['datamember']=$dataMember;
            $this->redirect('a.sales.NewOrder');
        }
    }
    public function cancelOrder($sender,$param) {        
        unset($_SESSION['currentPageNewOrder']);
        $this->redirect('a.sales.NewOrder');        
    }
    private function processDetails() {                            
        $this->btnAddProduct->Enabled=false;        
        $this->cmbPaymentMethod->DataSource=$this->product->removeIdFromArray($this->product->getList('payment_method',array('idpayment_method','payment_name'),null,null,1),'1');
        $this->cmbPaymentMethod->dataBind();
        $this->populateCart();
    }
    public function suggestProduct($sender,$param) {                
        $id=$param->getToken();
        $str = "SELECT product_id,CONCAT (product_name,' ',model) AS product_name FROM product WHERE status=1 AND product_name LIKE '$id%'";
        $this->DB->setFieldTable(array('product_id','product_name'));
        $r=$this->DB->getRecord($str);            
        $sender->DataSource=$r;
        $sender->dataBind(); 
    }
    public function productSuggetionSelected($sender,$param) {        
        $id=$sender->Suggestions->DataKeys[$param->selectedIndex];
        $this->hiddenproductid->Value=$id;        
        $this->product->setProductID($id);
        $this->hiddenstock->Value=$this->product->getStock ();
        $this->btnAddProduct->Enabled=true;
    }
    public function showListProduct ($sender,$param) {        
        $this->modalChooseProduct->show();
    }
    public function checkQTY($sender,$param) {        
        $product_id=$this->hiddenproductid->Value;
        $product_name=$this->txtProductName->Text;
        $qty=$param->Value;
        $stock=$this->hiddenstock->Value;
        $index=$this->getIndexFromList($_SESSION['currentPageNewOrder']['cart'],'product_id',$product_id);        
        if($index>=0) $qty+=$_SESSION['currentPageNewOrder']['cart'][$index]['quantity'];
        if ($qty>$stock) {
            $param->IsValid=false;
            $sender->ErrorMessage="Stock Product $product_name kurang dari permintaan";
        }        
    }
    public function addProduct($sender,$param) {        
        if ($this->IsValid) {            
            $product_id=$this->hiddenproductid->Value;
            $this->product->setProductID($product_id);
            $r=$this->product->getProduct();            
            $qty=$this->txtQTY->Text;       
            $laba=$this->finance->toInteger($this->txtLaba->Text);
            $index=$this->getIndexFromList($_SESSION['currentPageNewOrder']['cart'],'product_id',$product_id);        
            if($index>=0) {
                $unitprice=$this->finance->toInteger($_SESSION['currentPageNewOrder']['cart'][$index]['price']);
                $_SESSION['currentPageNewOrder']['cart'][$index]['quantity']+=$qty;
                $laba=$_SESSION['currentPageNewOrder']['cart'][$index]['laba'];
                $totalunitprice=$_SESSION['currentPageNewOrder']['cart'][$index]['quantity']*$unitprice;                                                
                $omset=$laba > 0 ? (($laba/100)*$unitprice)*$qty:$_SESSION['currentPageNewOrder']['cart'][$index]['default_omset']*$qty;                
                $totalprice=$totalunitprice;
                if ($laba > 0) {
                    $totalprice+=$omset;
                }
                $_SESSION['currentPageNewOrder']['cart'][$index]['totalunitprice']=$this->finance->toRupiah($totalunitprice);
                $_SESSION['currentPageNewOrder']['cart'][$index]['omset']=$this->finance->toRupiah($omset);
                $_SESSION['currentPageNewOrder']['cart'][$index]['totalprice']=$this->finance->toRupiah($totalprice);
            }else {                
                $unitprice=$r['price'];
                $model=$r['model'];
                $product_name=$r['product_name'];
                $totalunitprice=$qty*$unitprice;
                $default_omset=$r['default_omset'];
                $omset=$laba > 0 ? (($laba/100)*$unitprice)*$qty:$default_omset*$qty;                
                $totalprice=$totalunitprice;
                if ($laba > 0) {
                    $totalprice+=$omset;
                }
                $_SESSION['currentPageNewOrder']['cart'][]=array('product_id'=>$product_id,
                                                                 'product_name'=>$product_name,
                                                                 'model'=>$model,
                                                                 'quantity'=>$qty,
                                                                 'price'=>$this->finance->toRupiah($unitprice),
                                                                 'laba'=>$laba,'totalunitprice'=>$this->finance->toRupiah($totalunitprice),
                                                                 'omset'=>$this->finance->toRupiah($omset),
                                                                 'default_omset'=>$default_omset,
                                                                 'totalprice'=>$this->finance->toRupiah($totalprice));               
            }  
            $this->hiddentotalprice->Value=$totalprice;
            $this->populateCart();
            $this->clearFieldAddProduct();
        }
    }
    private function populateCart () {
        $totalprice=$this->getTotalFromCart('totalprice');
        if ($totalprice > 0 ) $this->hiddentotalprice->Value=$totalprice;
        $this->gridCart->DataSource=$_SESSION['currentPageNewOrder']['cart'];        
        $this->gridCart->DataBind();
    }
    public function itemCreated($sender,$param){
        $item=$param->Item;
        if($item->ItemType==='EditItem') {
            // set column width of textboxes            
            $item->QuantityColumn->TextBox->CssClass='input-mini';
            $item->LabaColumn->TextBox->CssClass='input-mini';            
            $item->LabaColumn->TextBox->Attributes->OnKeyUp='formatangka(this,false)';
        }
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='EditItem')  {
            // add an aleart dialog to delete buttons
            $item->DeleteColumn->Button->Attributes->onclick='if(!confirm(\'Are you sure?\')) return false;';
        }        
    }
    public function editItem($sender,$param) {        
        $this->gridCart->EditItemIndex=$param->Item->ItemIndex;
        $this->populateCart ();        
    }
    public function saveItem($sender,$param) {        
        $item=$param->Item;
        $id=$this->gridCart->DataKeys[$item->ItemIndex];
        $qty=$item->QuantityColumn->TextBox->Text;
        $laba=$this->finance->toInteger($item->LabaColumn->TextBox->Text);
        $index=$this->getIndexFromList($_SESSION['currentPageNewOrder']['cart'],'product_id',$id);        
        if($index>=0) {            
            $_SESSION['currentPageNewOrder']['cart'][$index]['quantity']=$qty;            
            $_SESSION['currentPageNewOrder']['cart'][$index]['laba']=$laba;
            $unitprice=$this->finance->toInteger($_SESSION['currentPageNewOrder']['cart'][$index]['price']);
            $totalunitprice=$unitprice*$qty;         
            $omset=$laba > 0 ? (($laba/100)*$unitprice)*$qty:$_SESSION['currentPageNewOrder']['cart'][$index]['default_omset']*$qty;                
            $totalprice=$totalunitprice;
            if ($laba > 0) {
                $totalprice+=$omset;
            }
            $_SESSION['currentPageNewOrder']['cart'][$index]['totalunitprice']=$this->finance->toRupiah($totalunitprice);
            $_SESSION['currentPageNewOrder']['cart'][$index]['omset']=$this->finance->toRupiah($omset);
            $_SESSION['currentPageNewOrder']['cart'][$index]['totalprice']=$this->finance->toRupiah($totalprice);
        }
        $this->gridCart->EditItemIndex=-1;
        $this->populateCart ();
    }
    public function cancelItem($sender,$param) {        
        $this->gridCart->EditItemIndex=-1;
        $this->populateCart ();        
    }
    public function deleteItem($sender,$param) {        
        $product_id=$this->gridCart->DataKeys[$param->Item->ItemIndex];
        $deleteIndex=$this->getIndexFromList($_SESSION['currentPageNewOrder']['cart'],'product_id',$product_id);        
        if($deleteIndex>=0) {            
            unset($_SESSION['currentPageNewOrder']['cart'][$deleteIndex]);            
        }
        $this->gridCart->EditItemIndex=-1;
        $this->populateCart (); 
    }
    private function clearFieldAddProduct() {
        $this->hiddenstock->Value='';
        $this->hiddenproductid->Value='';
        $this->txtProductName->Text='';
        $this->txtQTY->Text=1;
    }    
    public function checkNewOrder($sender,$param) {        
        $payment_method=$param->Value;		        
        try {   
            if ($payment_method == '3') {
                $totalprice=$this->getTotalFromCart ('totalprice');
                if ($totalprice > 0) {
                    $member_id=$_SESSION['currentPageNewOrder']['datamember']['member_id'];
                    $this->finance->setMemberID($member_id);
                    $accountbalance=$this->finance->getAccountBalance();                    
                    if ($totalprice > $accountbalance) 
                        throw new Exception ("Cash Deposit is not sufficient for this order.");
                }
            }            
        }catch (Exception $e) {
            $param->IsValid=false;
            $sender->ErrorMessage=$e->getMessage();
        }	        						
    }
    public function saveOrder($sender,$param) {
        if ($this->IsValid) {            
            $total=count($_SESSION['currentPageNewOrder']['cart']);
            if ($total >= 1) {
                $member_id=$_SESSION['currentPageNewOrder']['datamember']['member_id'];
                $member_name=  addslashes($_SESSION['currentPageNewOrder']['datamember']['member_name']);
                $ibo=$_SESSION['currentPageNewOrder']['datamember']['ibo'];
                $totalunitprice=$this->getTotalFromCart ('totalunitprice');
                $totalomset=$this->getTotalFromCart ('omset');
                $totalprice=$this->getTotalFromCart ('totalprice');
                $orderstatus=1;
                $idpayment_method=$this->cmbPaymentMethod->Text;
                $str = "INSERT INTO `order` (member_id,member_name,ibo,totalunitprice,totalomset,totalprice,order_status_id,idpayment_method,date_added,date_modified) VALUES($member_id,'$member_name','$ibo',$totalunitprice,$totalomset,$totalprice,$orderstatus,$idpayment_method,NOW(),NOW())";
                $this->DB->query('BEGIN');
                if ($this->DB->insertRecord($str)) {
                    $order_id=$this->DB->getLastInsertID();
                    $countField = count($_SESSION['currentPageNewOrder']['cart']);
                    if ($countField <= 1) {
                        $v=$_SESSION['currentPageNewOrder']['cart'][0];
                        $price=$this->finance->toInteger($v['price']);
                        $default_omset=$this->finance->toInteger($v['default_omset']);
                        $total=$this->finance->toInteger($v['totalunitprice']);
                        $values="(LAST_INSERT_ID(),{$v['product_id']},'{$v['product_name']}','{$v['model']}','{$v['quantity']}',$price,$default_omset,$total,{$v['laba']})";
                    }else {
                        for ($i=0;$i<$countField;$i++) {
                            $v=$_SESSION['currentPageNewOrder']['cart'][$i];
                            $price=$this->finance->toInteger($v['price']);
                            $default_omset=$this->finance->toInteger($v['default_omset']);
                            $total=$this->finance->toInteger($v['totalunitprice']);
                            if ($countField > $i+1) {
                                $values=$values."($order_id,{$v['product_id']},'{$v['product_name']}','{$v['model']}','{$v['quantity']}',$price,$default_omset,$total,{$v['laba']}),";
                            }else {
                                $values=$values."($order_id,{$v['product_id']},'{$v['product_name']}','{$v['model']}','{$v['quantity']}',$price,$default_omset,$total,{$v['laba']})";
                            }
                        }
                    }
                    $str="INSERT INTO order_product (order_id,product_id,name,model,quantity,price,default_omset,total,reward) VALUES $values";
                    $this->DB->insertRecord($str);
                    if ($orderstatus == 5 && $idpayment_method == 3) {                                        
                        $this->finance->setMemberID($member_id,true,3);
                        $this->finance->insertNewOmset($order_id,$totalomset);             
                        $accountbalance=$this->finance->getAccountBalance('all');
                        $deposit_akhir=$accountbalance['saldo_deposit']-$totalprice;
                        $bonus_terakhir=$accountbalance['sisa_bonus']-$totalprice;
                        $sisa_bonus=$bonus_terakhir>0 ?$bonus_terakhir:0;
                        $strdeposit = "INSERT INTO deposit (member_id,jenis,orders,sisa_bonus,saldo_deposit) VALUES($member_id,'order',$totalprice,$sisa_bonus,$deposit_akhir)";
                        $this->DB->insertRecord($strdeposit);       
                        $totalprice2=$this->finance->toRupiah($totalprice);
                        $aktivitas="Membayar order sebesar $totalprice2 dengan id $order_id";                                               
                        $strmutasi = "INSERT INTO mutasi (member_id,process,reference,aktivitas,debit,accountbalance,date_activity) VALUES ($member_id,'ordercomplete',$order_id,'$aktivitas',$totalprice,$deposit_akhir,NOW())";
                        $this->DB->insertRecord($strmutasi);                    
                    }elseif ($orderstatus == 5){
                        $this->finance->setMemberID($member_id,true,3);
                        $this->finance->insertNewOmset($order_id,$totalomset);
                    }
                    $this->DB->query('COMMIT');
                }else {
                    $this->DB->query('ROLLBACK');
                }
                unset($_SESSION['currentPageNewOrder']);
                $this->redirect('a.Sales');
            }
        }
    }   
    public function getTotalFromCart($field) {
        $total=count($_SESSION['currentPageNewOrder']['cart']);
        $totalprice=0;
        if ($total >= 1) {            
            foreach ($_SESSION['currentPageNewOrder']['cart'] as $v) {
                $total=$this->finance->toInteger($v[$field]);
                $totalprice+=$total;
            }
            
        }
        return $totalprice;
    }
}