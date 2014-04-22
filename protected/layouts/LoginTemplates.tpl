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
	
	<link rel="shortcut icon" href="<%=$this->page->theme->baseUrl%>/img/favicon.png">
	<script src="<%=$this->page->theme->baseUrl%>/assets/modernizr/modernizr-2.6.2.min.js"></script>
</com:THead>
<body class="login-page">
<!--[if lt IE 7]>
    <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->
<div class="login-wrapper">     
<com:TForm>    
<com:TContentPlaceHolder ID="content" />
</com:TForm>
</div>
</body>
</html>