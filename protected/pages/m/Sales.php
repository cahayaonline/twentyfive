<?php
prado::using ('Application.pages.m.sales.MainPageSales');
class Sales extends MainPageSales {    
	public function onLoad($param) {		
		parent::onLoad($param);				
        $this->showSalesHome=true;
        $this->createObjFinance();
        $this->createObjProduct();        
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (isset($this->session['currentPageSales']['datamember']['member_id'])){
                $this->dataMember=$this->session['currentPageSales']['datamember'];
                $this->processDetails();
            }elseif (isset($this->session['currentPageSales']['dataorder']['order_id'])){
                $this->processDetailOrder();
            }else{
                if (!isset($_SESSION['currentPageSales'])||$_SESSION['currentPageSales']['page_name']!='m.Sales') {
                    $_SESSION['currentPageSales']=array('page_name'=>'m.Sales','datamember'=>array(),'cart'=>array(),'page_num'=>0,'order_status_id'=>'none','search'=>false,'periodeawal'=>date('Y-m-d'),'periodeakhir'=>date('Y-m-d'),'dataorder'=>array());												
                }		
                $_SESSION['currentPageSales']['search']=false;                
                $this->cmbPeriodAkhir->Text=$this->TGL->tanggal('d-m-Y',$_SESSION['currentPageOmset']['periodeakhir']);
                $this->cmbPeriodAwal->Text=$this->TGL->tanggal('d-m-Y',$_SESSION['currentPageOmset']['periodeawal']);
                $order_status=$this->product->getStatusOrder();
                $order_status['none']='All';
                $this->cmbStatusOrder->DataSource=$order_status;
                $this->cmbStatusOrder->Text=$_SESSION['currentPageSales']['order_status_id'];
                $this->cmbStatusOrder->dataBind();
                $this->populateData();
            }                   
		}
	}
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageSales']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageSales']['search']);
	}    
    public function searchOrder ($sender,$param) {
		$_SESSION['currentPageSales']['search']=true;
        $this->populateData($_SESSION['currentPageSales']['search']);
	}    
    private function populateData ($search=false) {
        $order_status_id=$_SESSION['currentPageSales']['order_status_id'];
        $str_order=$order_status_id =='none'?'none':"WHERE o.order_status_id=$order_status_id";
        $str = $str_order=='none'?"`order` o LEFT JOIN order_status os ON(o.order_status_id=os.order_status_id)":"`order` o LEFT JOIN order_status os ON(o.order_status_id=os.order_status_id) $str_order";
        $str_jumlah_baris=$str_order=='none'?"`order` o":"`order` o $str_order";
        if ($search){
            $withdate=$this->chkWithDate->Checked;
            $membername=$this->membername->Text;
            $orderid=$this->txtOrderid->Text;    
            if ($withdate) {                     
                if ($this->cmbPeriodAwal->TimeStamp <= $this->cmbPeriodAkhir->TimeStamp) {
                    $_SESSION['currentPageOmset']['periodeawal']=date('Y-m-d',$this->cmbPeriodAwal->TimeStamp);
                    $_SESSION['currentPageOmset']['periodeakhir']=date('Y-m-d',$this->cmbPeriodAkhir->TimeStamp);
                    $awal=$this->cmbPeriodAwal->Text;
                    $akhir=$this->cmbPeriodAkhir->Text;
                    $awal=$this->TGL->tukarTanggal($awal);
                    $akhir=$this->TGL->tukarTanggal($akhir);
                    $str=$str_order =='none'?"$str WHERE CAST(date_modified AS DATE) BETWEEN CAST('$awal' AS DATE) AND CAST('$akhir' AS DATE)":"$str AND CAST(date_modified AS DATE) BETWEEN CAST('$awal' AS DATE) AND CAST('$akhir' AS DATE)";
                    $str_jumlah_baris=$str_order =='none'?"$str_jumlah_baris WHERE CAST(date_modified AS DATE) BETWEEN CAST('$awal' AS DATE) AND CAST('$akhir' AS DATE)":"$str_jumlah_baris AND CAST(date_modified AS DATE) BETWEEN CAST('$awal' AS DATE) AND CAST('$akhir' AS DATE)";                                
                    if ($membername != '') {                
                        $str="$str AND member_name LIKE '%$membername%'"; 
                        $str_jumlah_baris="$str_jumlah_baris AND member_name LIKE '%$membername%'";
                    }elseif ($orderid != '') {
                        $str="$str AND o.order_id='$orderid'";       
                        $str_jumlah_baris="$str_jumlah_baris AND o.order_id='$orderid'";
                    } 
                }
            }elseif ($membername != '') {                
                $str=$str_order =='none'?"$str WHERE member_name LIKE '%$membername%'":"$str AND member_name LIKE '%$membername%'"; 
                $str_jumlah_baris=$str_order =='none'?"$str_jumlah_baris WHERE member_name LIKE '%$membername%'":"$str_jumlah_baris AND member_name LIKE '%$membername%'";
            }elseif ($orderid != '') {
                $str=$str_order =='none'?"$str WHERE o.order_id='$orderid'":"$str AND o.order_id='$orderid'";       
                $str_jumlah_baris=$str_order =='none'?"$str_jumlah_baris WHERE o.order_id='$orderid'":"$str_jumlah_baris AND o.order_id='$orderid'";
            }            
        }  
        $jumlah_baris=$this->DB->getCountRowsOfTable ($str_jumlah_baris,'o.order_id');
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageSales']['page_num'];   
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageSales']['page_num']=0;}
        $this->lblDaftarOrder->Text=$this->finance->toRupiah($this->DB->getSumRowsOfTable('totalprice',"$str"),false);
		$str = "SELECT order_id,member_name,totalunitprice,totalomset,totalprice,o.order_status_id,name,date_added,date_modified FROM $str ORDER BY date_modified DESC LIMIT $offset,$limit ";
		$this->DB->setFieldTable(array('order_id','member_name','totalunitprice','totalomset','totalprice','order_status_id','name','date_added','date_modified'));
		$r=$this->DB->getRecord($str,$offset+1);		
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();		
    }
    public function changeOrderStatus ($sender,$param) { 
        $_SESSION['currentPageSales']['order_status_id']=$this->cmbStatusOrder->Text;
        $this->populateData();
    }    
    public function editRecord ($sender,$param) { 
        $this->idProcess='edit';
        $id=$this->getDataKeyField($sender, $this->RepeaterS);        
        $str = "SELECT order_id,o.member_id,o.member_name,o.ibo,order_status_id,idpayment_method,totalomset,totalprice FROM members m,`order` o WHERE m.member_id=o.member_id AND o.order_id=$id";
        $this->DB->setFieldTable(array('order_id','member_id','member_name','ibo','order_status_id','idpayment_method','totalomset','totalprice'));
        $result=$this->DB->getRecord($str);
        $_SESSION['currentPageSales']['datamember']=$result[1];        
        
        $str = "SELECT order_product_id,order_id,product_id,name,model,quantity,price,default_omset,total,reward FROM order_product WHERE order_id=$id";
        $this->DB->setFieldTable(array('order_product_id','order_id','product_id','name','model','quantity','price','default_omset','total','reward'));
        $r=$this->DB->getRecord($str);
        while (list($k,$v)=each($r)) {
            $product_id=$v['product_id'];
            $product_name=$v['name'];
            $model=$v['model'];
            $qty=$v['quantity'];
            $unitprice=$v['price'];
            $default_omset=$v['default_omset'];
            $laba=$v['reward'];            
            $totalunitprice=$v['total'];
            $omset=$laba > 0 ? (($laba/100)*$unitprice)*$qty:$default_omset*$qty;
            $totalprice=$totalunitprice;
            if ($laba > 0) {
                $totalprice+=$omset;
            }
            $_SESSION['currentPageSales']['cart'][]=array('product_id'=>$product_id,
                                                                     'product_name'=>$product_name,
                                                                     'model'=>$model,
                                                                     'quantity'=>$qty,
                                                                     'price'=>$this->finance->toRupiah($unitprice),
                                                                     'laba'=>$laba,'totalunitprice'=>$this->finance->toRupiah($totalunitprice),
                                                                     'omset'=>$this->finance->toRupiah($omset),
                                                                     'default_omset'=>$default_omset,
                                                                     'totalprice'=>$this->finance->toRupiah($totalprice));               
        }       
        $this->finance->redirect('m.Sales');
    }
    private function processDetails() {
        $this->idProcess='edit'; 
        $this->btnAddProduct->Enabled=false;        
        $this->cmbOrderStatus->DataSource=$this->product->getStatusOrder();        
        $this->cmbOrderStatus->Enabled=$this->dataMember['order_status_id']==5?false:true;
        $this->cmbOrderStatus->Text=$this->dataMember['order_status_id'];
        $this->cmbOrderStatus->dataBind();
        $this->cmbPaymentMethod->DataSource=$this->product->getList('payment_method',array('idpayment_method','payment_name'),null,null,5);
        $this->cmbPaymentMethod->Text=$this->dataMember['idpayment_method'];
        $this->cmbPaymentMethod->dataBind();
        $this->hiddentotalprice->Value=$this->getTotalFromCart('totalprice');
        if ($this->dataMember['order_status_id']==5) {
            $this->cmbOrderStatus->Enabled=false;
            $this->txtProductName->Enabled=false;
            $this->txtLaba->Enabled=false;
            $this->txtQTY->Enabled=false;
            $this->cmbPaymentMethod->Enabled="false";
            $this->btnSave->Enabled=false;            
        }        
        $this->populateCart();
    }       
    private function populateCart () {
        $totalprice=$this->getTotalFromCart('totalprice');
        if ($totalprice > 0 ) $this->hiddentotalprice->Value=$totalprice;
        $this->gridCart->DataSource=$this->session['currentPageSales']['cart'];
        $this->gridCart->DataBind();
    }
    public function itemCreated($sender,$param){
        $item=$param->Item;
        if($item->ItemType==='EditItem') {                
            // set column width of textboxes            
            $item->QuantityColumn->TextBox->Columns=3;
            $item->LabaColumn->TextBox->Columns=3;            
        }
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='EditItem')  {            
            $item->EditColumn->Enabled=$this->session['currentPageSales']['datamember']['order_status_id']==5?false:true;
            // add an aleart dialog to delete buttons
            $item->DeleteColumn->Button->Enabled=$this->session['currentPageSales']['datamember']['order_status_id']==5?false:true;
            $item->DeleteColumn->Button->Attributes->onclick='if(!confirm(\'Are you sure?\')) return false;';
        }        
    }
    public function editItem($sender,$param) {
        $this->idProcess='edit';
        $this->gridCart->EditItemIndex=$param->Item->ItemIndex;
        $this->populateCart ();        
    }
    public function deleteItem($sender,$param) {
        $this->idProcess='edit';
        $product_id=$this->gridCart->DataKeys[$param->Item->ItemIndex];
        $deleteIndex=$this->getIndexFromList($_SESSION['currentPageSales']['cart'],'product_id',$product_id);        
        if($deleteIndex>=0) {            
            unset($_SESSION['currentPageSales']['cart'][$deleteIndex]);            
        }
        $this->gridCart->EditItemIndex=-1;
        $this->populateCart (); 
    }
    public function saveItem($sender,$param) {
        $this->idProcess='edit';
        $item=$param->Item;
        $id=$this->gridCart->DataKeys[$item->ItemIndex];
        $qty=$item->QuantityColumn->TextBox->Text;
        $laba=$item->LabaColumn->TextBox->Text;
        $index=$this->getIndexFromList($_SESSION['currentPageSales']['cart'],'product_id',$id);        
        if($index>=0) {            
            $_SESSION['currentPageSales']['cart'][$index]['quantity']=$qty;            
            $_SESSION['currentPageSales']['cart'][$index]['laba']=$laba;
            $unitprice=$this->finance->toInteger($_SESSION['currentPageSales']['cart'][$index]['price']);
            $totalunitprice=$unitprice*$qty;
            $omset=$laba > 0 ? (($laba/100)*$unitprice)*$qty:$_SESSION['currentPageSales']['cart'][$index]['default_omset']*$qty;
            $totalprice=$totalunitprice;
            if ($laba > 0) {
                $totalprice+=$omset;
            }
            $_SESSION['currentPageSales']['cart'][$index]['totalunitprice']=$this->finance->toRupiah($totalunitprice);
            $_SESSION['currentPageSales']['cart'][$index]['omset']=$this->finance->toRupiah($omset);
            $_SESSION['currentPageSales']['cart'][$index]['totalprice']=$this->finance->toRupiah($totalprice);
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
    public function cancelItem($sender,$param) {
        $this->idProcess='edit';
        $this->gridCart->EditItemIndex=-1;
        $this->populateCart ();        
    }
    public function suggestProduct($sender,$param) {
        $this->idProcess='edit';        
        $id=$param->getToken();
        $str = "SELECT product_id,CONCAT (product_name,' ',model) AS product_name FROM product WHERE status=1 AND product_name LIKE '$id%'";
        $this->DB->setFieldTable(array('product_id','product_name'));
        $r=$this->DB->getRecord($str);            
        $sender->DataSource=$r;
        $sender->dataBind(); 
    }
    public function productSuggetionSelected($sender,$param) {
        $this->idProcess='edit';
        $id=$sender->Suggestions->DataKeys[$param->selectedIndex];
        $this->hiddenproductid->Value=$id;        
        $this->product->setProductID($id);
        $this->hiddenstock->Value=$this->product->getStock ();
        $this->btnAddProduct->Enabled=true;
    }
    public function checkQTY($sender,$param) {
        $this->idProcess='edit';
        $product_id=$this->hiddenproductid->Value;
        $product_name=$this->txtProductName->Text;
        $qty=$param->Value;
        $stock=$this->hiddenstock->Value;
        $index=$this->getIndexFromList($_SESSION['currentPageSales']['cart'],'product_id',$product_id);        
        if($index>=0) $qty+=$_SESSION['currentPageSales']['cart'][$index]['quantity'];
        if ($qty>$stock) {
            $param->IsValid=false;
            $sender->ErrorMessage="Stock Product $product_name kurang dari permintaan";
        }        
    }
    public function addProduct($sender,$param) {
        $this->idProcess='edit';
        if ($this->IsValid) {            
            $product_id=$this->hiddenproductid->Value;
            $this->product->setProductID($product_id);
            $r=$this->product->getProduct();            
            $qty=$this->txtQTY->Text;       
            $laba=$this->txtLaba->Text;
            $index=$this->getIndexFromList($_SESSION['currentPageSales']['cart'],'product_id',$product_id);        
            if($index>=0) {
                $unitprice=$this->finance->toInteger($_SESSION['currentPageSales']['cart'][$index]['price']);
                $_SESSION['currentPageSales']['cart'][$index]['quantity']+=$qty;
                $laba=$_SESSION['currentPageSales']['cart'][$index]['laba'];
                $totalunitprice=$_SESSION['currentPageSales']['cart'][$index]['quantity']*$unitprice;                                
                $omset=$laba > 0 ? (($laba/100)*$unitprice)*$qty:$_SESSION['currentPageSales']['cart'][$index]['default_omset']*$qty;                
                $totalprice=$totalunitprice;
                if ($laba > 0) {
                    $totalprice+=$omset;
                }
                $_SESSION['currentPageSales']['cart'][$index]['totalunitprice']=$this->finance->toRupiah($totalunitprice);
                $_SESSION['currentPageSales']['cart'][$index]['omset']=$this->finance->toRupiah($omset);
                $_SESSION['currentPageSales']['cart'][$index]['totalprice']=$this->finance->toRupiah($totalprice);
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
                $_SESSION['currentPageSales']['cart'][]=array('product_id'=>$product_id,
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
    public function checkNewOrder($sender,$param) {						
        $this->idProcess='edit';
        $payment_method=$param->Value;		        
        try {   
            if ($payment_method == '3') {
                $totalprice=$this->getTotalFromCart ('totalprice');
                if ($totalprice > 0) {
                    $member_id=$this->session['currentPageSales']['datamember']['member_id'];
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
    public function saveOrder ($sender,$param) {            
        if ($this->IsValid) {
            $this->idProcess='edit';
            $total=count($this->session['currentPageSales']['cart']);
            $id=$this->session['currentPageSales']['datamember']['order_id'];            
            if ($total >= 1) {                
                $member_id=$this->session['currentPageSales']['datamember']['member_id'];                
                $orderstatus=$this->cmbOrderStatus->Text;
                $totalunitprice=$this->getTotalFromCart ('totalunitprice');
                $totalomset=$this->getTotalFromCart ('omset');
                $totalprice=$this->getTotalFromCart ('totalprice');    
                $idpayment_method=$this->cmbPaymentMethod->Text;
                $this->DB->query ('BEGIN');
                $str = "UPDATE `order` SET order_status_id=$orderstatus,totalunitprice=$totalunitprice,totalomset=$totalomset,totalprice=$totalprice,idpayment_method=$idpayment_method,date_modified=NOW() WHERE order_id=$id";
                if ($this->DB->updateRecord($str) ) {       
                    $this->DB->deleteRecord("order_product WHERE order_id=$id");
                    $countField = count($this->session['currentPageSales']['cart']);
                    if ($countField <= 1) {
                        $v=$this->session['currentPageSales']['cart'][0];
                        $price=$this->finance->toInteger($v['price']);
                        $default_omset=$this->finance->toInteger($v['default_omset']);
                        $total=$this->finance->toInteger($v['totalunitprice']);
                        $values="($id,{$v['product_id']},'{$v['product_name']}','{$v['model']}','{$v['quantity']}',$price,$default_omset,$total,{$v['laba']})";
                    }else {
                        for ($i=0;$i<$countField;$i++) {
                            $v=$this->session['currentPageSales']['cart'][$i];
                            $price=$this->finance->toInteger($v['price']);
                            $default_omset=$this->finance->toInteger($v['default_omset']);
                            $total=$this->finance->toInteger($v['totalunitprice']);
                            if ($countField > $i+1) {
                                $values=$values."($id,{$v['product_id']},'{$v['product_name']}','{$v['model']}','{$v['quantity']}',$price,$default_omset,$total,{$v['laba']}),";
                            }else {
                                $values=$values."($id,{$v['product_id']},'{$v['product_name']}','{$v['model']}','{$v['quantity']}',$price,$default_omset,$total,{$v['laba']})";
                            }
                        }
                    }
                    $str="INSERT INTO order_product (order_id,product_id,name,model,quantity,price,default_omset,total,reward) VALUES $values";
                    $this->DB->insertRecord($str);                    
                    if ($this->session['currentPageSales']['datamember']['order_status_id'] != 5 && $orderstatus == 5 && $idpayment_method==3) {                                                  
                        $this->finance->setMemberID($member_id,true,3);
                        $this->finance->insertNewOmset($id,$totalomset);             
                        $accountbalance=$this->finance->getAccountBalance('all');
                        $deposit_akhir=$accountbalance['saldo_deposit']-$totalprice;
                        $bonus_terakhir=$accountbalance['sisa_bonus']-$totalprice;
                        $sisa_bonus=$bonus_terakhir>0 ?$bonus_terakhir:0;
                        $strdeposit = "INSERT INTO deposit (member_id,jenis,orders,sisa_bonus,saldo_deposit) VALUES($member_id,'order',$totalprice,$sisa_bonus,$deposit_akhir)";
                        $this->DB->insertRecord($strdeposit);       
                        $totalprice2=$this->finance->toRupiah($totalprice);
                        $aktivitas="Membayar order sebesar $totalprice2 dengan id $id";                                               
                        $strmutasi = "INSERT INTO mutasi (member_id,process,reference,aktivitas,debit,accountbalance,date_activity) VALUES ($member_id,'ordercomplete',$id,'$aktivitas',$totalprice,$deposit_akhir,NOW())";
                        $this->DB->insertRecord($strmutasi);   
                    }elseif ($this->session['currentPageSales']['datamember']['order_status_id'] != 5 && $orderstatus == 5){
                        $this->finance->setMemberID($member_id,true,3);
                        $this->finance->insertNewOmset($id,$totalomset);
                    }elseif ($orderstatus==5) {                                                                                                         
                        $this->finance->setMemberID($member_id,true,3);
                        $this->finance->insertNewOmset($id,$totalomset);         
                    }    
                    $this->DB->query ('COMMIT');
                }else {
                    $this->DB->query ('ROLLBACK');
                }
            }else {
                $this->DB->deleteRecord("`order` WHERE order_id=$id");
            }
            unset($_SESSION['currentPageSales']);
            $this->finance->redirect('m.Sales');
        }
    }        
    public function getTotalFromCart($field) {
        $total=count($this->session['currentPageSales']['cart']);
        $totalprice=0;
        if ($total >= 1) {            
            foreach ($this->session['currentPageSales']['cart'] as $v) {
                $total=$this->finance->toInteger($v[$field]);
                $totalprice+=$total;
            }            
        }
        return $totalprice;
    }
    public function showOrderDetails ($sender,$param) {
        $id=$this->getDataKeyField($sender, $this->RepeaterS);
        $str = "SELECT order_id,invoice_no,invoice_prefix,member_id,member_name,ibo,totalunitprice,totalomset,totalprice,pm.payment_name,o.order_status_id,os.name AS order_status,date_added,date_modified FROM `order` o,order_status os,payment_method pm WHERE os.order_status_id=o.order_status_id AND pm.idpayment_method AND order_id=$id";
        $this->DB->setFieldTable(array('order_id','invoice_no','invoice_prefix','member_id','member_name','ibo','totalunitprice','totalomset','totalprice','payment_name','order_status_id','order_status','date_added','date_modified'));
        $r=$this->DB->getRecord($str);
        $_SESSION['currentPageSales']['dataorder']=$r[1];        
        $str = "SELECT name,model,quantity,price,default_omset,total,reward FROM order_product WHERE order_id=$id";
        $this->DB->setFieldTable(array('name','model','quantity','price','default_omset','total','reward'));
        $r=$this->DB->getRecord($str);        
        $result=array();
        while (list($k,$v)=each($r)) {            
            $qty=$v['quantity'];
            $unitprice=$v['price'];
            $default_omset=$v['default_omset'];
            $laba=$v['reward'];            
            $totalunitprice=$v['total'];
            $omset=$laba > 0 ? (($laba/100)*$unitprice)*$qty:$default_omset*$qty;
            $totalprice=$totalunitprice;
            if ($laba > 0) {
                $totalprice+=$omset;
            }                        
            $v['omset']=$omset;
            $v['totalprice']=$totalprice;
            $result[$k]=$v;
        }
        $_SESSION['currentPageSales']['dataorder']['products']=$result;
        $this->finance->redirect('m.Sales');
    }
    public function processDetailOrder () {
        $this->idProcess='view';
        $this->dataOrder=$_SESSION['currentPageSales']['dataorder'];         
        if ($this->dataOrder['order_status_id'] == 5) {
            $this->btnDelete->CssClass='button_grey';           
        }
        $this->RepeaterProduct->DataSource=$_SESSION['currentPageSales']['dataorder']['products'];
        $this->RepeaterProduct->dataBind();
    }    
    public function closeViewOrder($sender,$param) {        
        unset($_SESSION['currentPageSales']);
        $this->redirect('m.Sales');        
    } 
    public function deleteRecord ($sender,$param) {                    
        if ($_SESSION['currentPageSales']['dataorder']['order_status_id']!=5) {
            $id=$_SESSION['currentPageSales']['dataorder']['order_id'];
            $this->DB->deleteRecord("`order` WHERE order_id=$id");       
            unset($_SESSION['currentPageSales']);
            $this->redirect('m.Sales');
        }
    }
}
