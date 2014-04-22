<div class="grid_16">
<!-- TABS START -->
    <div id="tabs">
         <div class="container">
            <ul>
                <li><a href="<%=$this->Service->constructUrl('m.sales.NewOrder')%>" class="<%=$this->showNewOrder==true?'current':''%>"><span>New Order</span></a></li>
                <li><a href="<%=$this->Service->constructUrl('m.Sales')%>" class="<%=$this->showSalesHome==true?'current':''%>"><span>Orders</span></a></li>                                    
                <li><a href="<%=$this->Service->constructUrl('m.sales.Omset')%>" class="<%=$this->showOmset==true?'current':''%>"><span>Omset</span></a></li>                                    
                <li><a href="<%=$this->Service->constructUrl('m.sales.ProductReturns')%>" class="<%=$this->showReturnProduct==true?'current':''%>"><span>Product Returns</span></a></li>                                    
           </ul>
        </div>
    </div>
<!-- TABS END -->    
</div>
