<a class="cart_li" href="<com:TOutputCache><%=$this->Service->constructUrl('ShoppingCart')%></com:TOutputCache>">Keranjang </a>
<com:TActiveRepeater ID="repeaterTopCart" DataKeyField="product_id">
    <prop:HeaderTemplate>
        <ul class="cart_cont">
            <li class="no_border"><p>Recently added item(s)</p></li>
    </prop:HeaderTemplate>
    <prop:ItemTemplate>
            <li>
                <a href="<%#$this->Service->constructUrl('Product',array('id'=>$this->DataItem['product_id']))%>" class="prev_cart"><div class="cart_vert"><img src="<%#$this->DataItem['image']%>" alt="" title="" /></div></a>
                <div class="cont_cart">
                    <h4><%#$this->DataItem['product_name']%> <%#$this->DataItem['model']%></h4>
                    <div class="price"><%#$this->DataItem['quantity']%> x <%#$this->page->finance->toRupiah($this->DataItem['price'],false)%></div>
                </div>
                <com:TActiveLinkButton Attributes.Title="close" CssClass="close" OnClick="page.deleteItemCart" />
                <div class="clear"></div>
            </li>
    </prop:ItemTemplate>
    <prop:FooterTemplate>
            <li class="no_border">
                <a href="<%=$this->Service->constructUrl('ShoppingCart')%>" class="view_cart">View shopping cart</a>
                <a href="<%=$this->Service->constructUrl('CheckOut')%>" class="checkout">Procced to Checkout</a>
            </li>           
        </ul>
    </prop:FooterTemplate>   
    <prop:EmptyTemplate>
        <ul class="cart_cont">
            <li class="no_border"><p>Recently added item(s)</p></li>
            <li>
                <div class="cont_cart">
                    <h4>Belum ada item barang</h4>                                        
                </div>
            </li>
        </ul>
    </prop:EmptyTemplate>
</com:TActiveRepeater> 