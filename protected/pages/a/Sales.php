<?php
prado::using ('Application.pages.a.sales.MainPageSales');
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
                if (!isset($_SESSION['currentPageSales'])||$_SESSION['currentPageSales']['page_name']!='a.Sales') {
                    $_SESSION['currentPageSales']=array('page_name'=>'a.Sales','datamember'=>array(),'cart'=>array(),'page_num'=>0,'order_status_id'=>'none','search'=>false,'periodeawal'=>date('Y-m-d'),'periodeakhir'=>date('Y-m-d'),'dataorder'=>array());												
                }		
                $_SESSION['currentPageSales']['search']=false;                
                $this->cmbPeriodAkhir->Text=$this->TGL->tanggal('d-m-Y',$_SESSION['currentPageOmset']['periodeakhir']);
                $this->cmbPeriodAwal->Text=$this->TGL->tanggal('d-m-Y',$_SESSION['currentPageOmset']['periodeawal']);               
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
        $member_id=$this->Pengguna->getDataUser('member_id');                
        $str = "`order` o LEFT JOIN order_status os ON(o.order_status_id=os.order_status_id) WHERE member_id=$member_id";
        $str_jumlah_baris="`order` o WHERE member_id=$member_id";
        if ($search){
            $withdate=$this->chkWithDate->Checked;            
            $orderid=$this->txtOrderid->Text;    
            if ($withdate) {                     
                if ($this->cmbPeriodAwal->TimeStamp <= $this->cmbPeriodAkhir->TimeStamp) {
                    $_SESSION['currentPageOmset']['periodeawal']=date('Y-m-d',$this->cmbPeriodAwal->TimeStamp);
                    $_SESSION['currentPageOmset']['periodeakhir']=date('Y-m-d',$this->cmbPeriodAkhir->TimeStamp);
                    $awal=$this->cmbPeriodAwal->Text;
                    $akhir=$this->cmbPeriodAkhir->Text;
                    $awal=$this->TGL->tukarTanggal($awal);
                    $akhir=$this->TGL->tukarTanggal($akhir);
                    $str="$str AND CAST(date_modified AS DATE) BETWEEN CAST('$awal' AS DATE) AND CAST('$akhir' AS DATE)";
                    $str_jumlah_baris="$str_jumlah_baris AND CAST(date_modified AS DATE) BETWEEN CAST('$awal' AS DATE) AND CAST('$akhir' AS DATE)";                                
                    if ($orderid != '') {
                        $str="$str AND o.order_id='$orderid'";       
                        $str_jumlah_baris="$str_jumlah_baris AND o.order_id='$orderid'";
                    } 
                }
            }if ($orderid != '') {
                $str="$str AND o.order_id='$orderid'";       
                $str_jumlah_baris="$str_jumlah_baris AND o.order_id='$orderid'";
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
        $this->finance->redirect('a.Sales');
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
        $this->redirect('a.Sales');        
    } 
    public function deleteRecord ($sender,$param) {                    
        if ($_SESSION['currentPageSales']['dataorder']['order_status_id']!=5) {
            $id=$_SESSION['currentPageSales']['dataorder']['order_id'];
            $this->DB->deleteRecord("`order` WHERE order_id=$id");       
            unset($_SESSION['currentPageSales']);
            $this->redirect('a.Sales');
        }
    }
}
