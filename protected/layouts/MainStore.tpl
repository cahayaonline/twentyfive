<!DOCTYPE html>
<html>
<com:THead>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width">    
    <link rel="shortcut icon" href="<%=$this->Page->setup->getStoreProduct('favicon.ico')%>">
    <link href="<%=$this->Page->Theme->baseUrl%>/css/style.css" media="screen" rel="stylesheet" type="text/css">
    <com:TContentPlaceHolder ID="csscontent" />
</com:THead>
<body>
<com:TForm>
<script src="<%=$this->page->setup->getAddress()%>/resources/system.js" type="text/javascript"></script>
<div id="loading" style="display:none">
    Please wait while process your request !!!
</div>
<div class="container_12">
    <div id="top">
        <div class="grid_3 ">
            <div class="phone_top">
                <span>Call Us <com:TOutputCache><%=$this->Page->setup->getSettingValue('config_telephone')%></com:TOutputCache></span>
            </div><!-- .phone_top -->
        </div><!-- .grid_3 -->
        <div class="grid_6">
            <div class="welcome">
                Welcome visitor you can <a href="<%=$this->Service->constructUrl('a.Login')%>">login</a> or <a href="<%=$this->Service->constructUrl('SignUp')%>">create an account</a>.
            </div><!-- .welcome -->
        </div><!-- .grid_6 -->
        <div class="grid_3">            
        </div><!-- .grid_3 -->
    </div><!-- end top -->
    <div class="clear"></div>
    <header id="branding">
        <div class="grid_3">
            <hgroup>
                <h1 id="site_logo" ><a href="<%=$this->Service->constructUrl('Home')%>" title=""><img src="<%=$this->Page->setup->getStoreProduct('logo.png')%>" alt="Online Store Theme Logo"/></a></h1>
                <h2 id="site_description">Sensasi belanja online serasa di Supermarket</h2>
            </hgroup>
        </div><!-- .grid_3 -->
        <div class="grid_3">
            <div class="search">
                 <com:TTextBox ID="search" CssClass="entry_form" Attributes.placeholder="Search entire store here..." />
             </div>
        </div><!-- .grid_3 -->
        <div class="grid_6">
            <ul id="cart_nav">
                <li>
                    <com:TContentPlaceHolder ID="contentTopShoppingCart" />                       
                </li>
            </ul>
            <nav class="private">
                <ul>
                    <li><a href="#">My Account</a></li>
                    <li class="separator">|</li>
                    <li><a href="#">My Wishlist</a></li>
                    <li class="separator">|</li>
                    <li><a href="<%=$this->Service->constructUrl('a.Login')%>">Log In</a></li>
                    <li class="separator">|</li>
                    <li><a href="<%=$this->Service->constructUrl('SignUp')%>">Sign Up</a></li>
                </ul>
            </nav>
        </div><!-- .grid_6 -->
    </header><!-- end header [branding] -->    
</div><!-- end container_12 -->    
<div class="clear"></div>
<div id="block_nav_primary">
    <div class="container_12">
        <div class="grid_12">
            <nav class="primary">
                <a class="menu-select" href="#">Catalog</a>
                <com:TLiteral ID="TopMenu" />                
            </nav><!-- .nav -->
        </div><!-- .grid_12 -->
    </div><!-- end container_12 -->    
</div><!-- end block_nav_primary -->
<div class="clear"></div>
<com:TContentPlaceHolder ID="maincontent" />
<div class="clear"></div>
<footer>
    <div class="f_navigation">
        <div class="container_12">
            <div class="grid_3">
                <h3>Contact Us</h3>
                <ul class="f_contact">
                    <li><com:TOutputCache><%=$this->Page->setup->getSettingValue('config_address')%></com:TOutputCache></li>
                    <li><com:TOutputCache><%=$this->Page->setup->getSettingValue('config_telephone')%></com:TOutputCache></li>
                    <li><com:TOutputCache><%=$this->Page->setup->getSettingValue('config_email')%></com:TOutputCache></li>
                </ul><!-- .f_contact -->
            </div><!-- .grid_3 -->
            <div class="grid_3">
                <h3>Information</h3>
                <nav class="f_menu">
                    <ul>
                        <li><a href="#">Tentang Kami</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Secure payment</a></li>
                    </ul>
                </nav><!-- .private -->
            </div><!-- .grid_3 -->
            <div class="grid_3">
                <h3>Costumer Servise</h3>
                <nav class="f_menu">
                    <ul>
                        <li><a href="<com:TOutputCache><%=$this->Service->constructUrl('ContactUs')%></com:TOutputCache>">Contact As</a></li>
                        <li><a href="#">Return</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Site Map</a></li>
                    </ul>
                </nav><!-- .private -->
            </div><!-- .grid_3 -->
            <div class="grid_3">
                <h3>My Account</h3>
                <nav class="f_menu">
                    <ul>
                        <li><a href="#">My Account</a></li>
                        <li><a href="#">Order History</a></li>
                        <li><a href="#">Wish List</a></li>
                        <li><a href="#">Newsletter</a></li>
                    </ul>
                </nav><!-- .private -->
            </div><!-- .grid_3 -->
            <div class="clear"></div>
        </div><!-- end container_12 -->    
    </div><!-- end f_navigation -->
    <div class="f_info">
        <div class="container_12">
            <div class="grid_6">
                <p class="copyright">Â© Breeze Store Theme, 2012</p>
            </div><!-- .grid_6 -->
            <div class="grid_6">
                <div class="soc">
                    <a class="google" href="#"></a>
                    <a class="twitter" href="#"></a>
                    <a class="facebook" href="#"></a>
                </div><!-- .soc -->
            </div><!-- .grid_6 -->
            <div class="clear"></div>
            <com:TJavascriptLogger />
        </div><!-- .container_12 -->
    </div><!-- .f_info -->
</footer>
</com:TForm>
<script src="<%=$this->Page->Theme->baseUrl%>/js/jquery-1.7.2.min.js"></script> 
<script language="javascript">
    jQuery.noConflict();
</script>
<script src="<%=$this->Page->Theme->baseUrl%>/js/html5.js"></script>
<script src="<%=$this->Page->Theme->baseUrl%>/js/main.js"></script>
<script src="<%=$this->Page->Theme->baseUrl%>/js/jquery.carouFredSel-6.2.0-packed.js"></script>
<script src="<%=$this->Page->Theme->baseUrl%>/js/jquery.touchSwipe.min.js"></script>
<script src="<%=$this->Page->Theme->baseUrl%>/js/checkbox.js"></script>
<script src="<%=$this->Page->Theme->baseUrl%>/js/radio.js"></script>
<script src="<%=$this->Page->Theme->baseUrl%>/js/selectBox.js"></script>
<com:TContentPlaceHolder ID="jscontent" />
</body>
</html>