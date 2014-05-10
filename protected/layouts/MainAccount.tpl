<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<com:THead>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">	
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">
	<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
	
	<!--base css styles-->
	<link rel="stylesheet" href="<%=$this->page->theme->baseUrl%>/assets/bootstrap/bootstrap.min.css">     
	<link rel="stylesheet" href="<%=$this->page->theme->baseUrl%>/assets/bootstrap/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="<%=$this->page->theme->baseUrl%>/assets/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="<%=$this->page->theme->baseUrl%>/assets/normalize/normalize.css">
	
	<!--flaty css styles-->
	<link rel="stylesheet" href="<%=$this->page->theme->baseUrl%>/css/flaty.css">
	<link rel="stylesheet" href="<%=$this->page->theme->baseUrl%>/css/flaty-responsive.css">
    <link rel="stylesheet" href="<%=$this->page->theme->baseUrl%>/css/mystyle.css">
	<com:TContentPlaceHolder ID="csscontent" />
	<link rel="shortcut icon" href="<%=$this->page->theme->baseUrl%>/img/favicon.png">
	<script src="<%=$this->page->theme->baseUrl%>/assets/modernizr/modernizr-2.6.2.min.js"></script>
</com:THead>    
<body>
<!--[if lt IE 7]>
    <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->
<com:TForm Attributes.class="form-horizontal">
<div id="loading" style="display:none">
    Please wait while process your request !!!
</div>
<script src="<%=$this->page->setup->getAddress()%>/resources/system.js" type="text/javascript"></script>
<!-- BEGIN Navbar -->
<div id="navbar" class="navbar">
    <div class="navbar-inner">
        <div class="container-fluid">
            <!-- BEGIN Brand -->
            <a href="#" class="brand">
                <small>
                    <i class="icon-desktop"></i>
                    Selamat Berbelanja
                </small>
            </a>
            <!-- END Brand -->
				<!-- BEGIN Responsive Sidebar Collapse -->
					<a href="#" class="btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
                <i class="icon-reorder">
					</i>
				</a>
				<!-- END Responsive Sidebar Collapse -->
            <!-- BEGIN Navbar Buttons -->
				<ul class="nav flaty-nav pull-right">
			<a href="#" class="brand">
				<small>
					Deposit Bonus Rp. <com:TLiteral ID="literalDepositBonus" />,-  Saldo Deposit Rp. <com:TLiteral ID="literalSaldoDeposit" />,-
				</small>
			</a>
			<!-- BEGIN Button Tasks -->
                <li class="hidden-phone">
                    </li>
            <!-- END Button Messages -->    
                <li class="user-profile">
                     <a data-toggle="dropdown" href="#" class="user-menu dropdown-toggle">
                        <img class="nav-user-photo" src="<%=$this->Page->setup->getUrlPhotoUser().$this->Page->Pengguna->getDataUser('userid')%>_70x68.jpg" onerror="no_photo(this,'<%=$this->Page->setup->getUrlPhotoUser().'no_photo.jpg'%>')" alt="photo <%=$this->page->Pengguna->getUsername()%>" />
                        <span class="hidden-phone" id="user_info">
                            <%=$this->Page->Pengguna->getUsername()%>
                        </span>
                        <i class="icon-caret-down"></i>
                    </a>
                    <!-- BEGIN User Dropdown -->
                    <ul class="dropdown-menu dropdown-navbar" id="user_menu">
                        <li class="nav-header">
                            <i class="icon-time"></i>
                            Logined From 20:45
                        </li>
                        <li>
                            <a href="#">
                                <i class="icon-cog"></i>
                                Account Settings
                            </a>
                        </li>
                        <li>
                            <a href="<%=$this->Service->constructUrl('a.members.EditMember')%>">
                                <i class="icon-user"></i>
                                Edit Profile
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="icon-comment"></i>
                                Messages
                            </a>
						</li>
                        <li class="divider"></li>
                        <li>
                            <a href="<%=$this->Service->constructUrl('a.Logout')%>">
                                <i class="icon-off"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                    <!-- END User Dropdown -->
                </li>
            </ul>
            <!-- END Navbar Buttons -->
        </div><!--/.container-fluid-->
    </div><!--/.navbar-inner-->
</div>
<!-- END Navbar -->
<!-- BEGIN Container -->
<div class="container-fluid" id="main-container">
     <!-- BEGIN Sidebar -->
    <div id="sidebar" class="nav-collapse">
        <!-- BEGIN Navlist -->
        <ul class="nav nav-list">
            <li>&nbsp;</li>
            <li<%=$this->Page->showDashboard==true?' class="active"':''%>>
                <a href="<%=$this->Service->constructUrl('a.Home')%>">
                    <i class="icon-dashboard"></i>
                    <span>Informasi Member</span>
                </a>
            </li>
            <li<%=$this->Page->showMembers==true?' class="active"':''%>>
                <a href="#" class="dropdown-toggle">
                    <i class="icon-group"></i>
                    <span>Member</span>
                    <b class="arrow icon-angle-right"></b>
                </a>
                <!-- BEGIN Submenu -->
                <ul class="submenu">                                        
                    <li<%=$this->Page->showAddNewMember==true?' class="active"':''%>><a href="<%=$this->Service->constructUrl('a.members.AddNewMember')%>">Tambah Member</a></li>
                    <li<%=$this->Page->showDeposit==true?' class="active"':''%>><a href="<%=$this->Service->constructUrl('a.members.Deposit')%>">Mutasi Deposit</a></li>                               
                </ul>
            </li>    
            <li<%=$this->Page->showSales==true?' class="active"':''%>>
                <a href="#" class="dropdown-toggle">
                    <i class="icon-shopping-cart"></i>
                    <span>Keranjang Belanja</span>
                    <b class="arrow icon-angle-right"></b>
                </a>
                <!-- BEGIN Submenu -->
                <ul class="submenu">                    
                    <li<%=$this->Page->showNewOrder==true?' class="active"':''%>><a href="<%=$this->Service->constructUrl('a.sales.NewOrder')%>">Transaksi Baru</a></li>
                    <li<%=$this->Page->showSalesHome==true?' class="active"':''%>><a href="<%=$this->Service->constructUrl('a.Sales')%>">Daftar Transaksi</a></li>                                                        
                </ul>
            </li>
            <li<%=$this->Page->showKonfirmasiDeposit==true?' class="active"':''%>>
                <a href="<%=$this->Service->constructUrl('a.KonfirmasiDeposit')%>">
                    <i class="icon-money"></i>
                    <span>Konfirmasi Transaksi</span>
                </a>
            </li>
        </ul>
		<!-- END Navlist -->
        <!-- BEGIN Sidebar Collapse Button -->
        <div id="sidebar-collapse" class="visible-desktop">
            <i class="icon-double-angle-left"></i>
        </div>
        <!-- END Sidebar Collapse Button -->
    </div>
    <!-- END Sidebar -->

    <!-- BEGIN Content -->
    <div id="main-content">	
        <!-- BEGIN Page Title -->
        <div class="page-title">
            <div>
                <h1><com:TContentPlaceHolder ID="titlecontent" /></h1>
                <h4><com:TContentPlaceHolder ID="titledesccontent" /></h4>
            </div>
        </div>
        <!-- END Page Title -->
        <com:TContentPlaceHolder ID="maincontent"/> 
        <footer>
            <p>2013 © Cahaya Online Backend System by Yacanet.com.</p>
            <com:TJavascriptLogger/>
        </footer>
        <a id="btn-scrollup" class="btn btn-circle btn-large" href="#"><i class="icon-chevron-up"></i></a>              
    </div>
     <!-- END Content -->
     
</div>
<!-- END Container -->
</com:TForm>
<!--basic scripts-->
<script src="<%=$this->page->theme->baseUrl%>/assets/jquery/jquery-1.10.1.min.js"></script>
<script language="javascript">
    jQuery.noConflict();
</script>
<script src="<%=$this->page->theme->baseUrl%>/assets/bootstrap/bootstrap.min.js"></script>
<script src="<%=$this->page->theme->baseUrl%>/assets/nicescroll/jquery.nicescroll.min.js"></script>
<!--page specific plugin scripts-->
<com:TContentPlaceHolder ID="jscontent"/>
<!--flaty scripts-->
<script src="<%=$this->page->theme->baseUrl%>/js/flaty.js"></script>
</body>
</html>   
        