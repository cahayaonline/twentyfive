<div class="pagination pagination-large text-center">
    <com:TActivePager ID="pager" OnCallBack="Page.renderCallback" ControlToPaginate="RepeaterS" Mode="Numeric" OnPageIndexChanged="Page.Page_Changed" PrevPageText="&laquo; Previous" NextPageText="Next &raquo;" PageButtonCount="10" FirstPageText="First" LastPageText="Last" CssClass="pagination">	
        <prop:ClientSide.OnPreDispatch>
            $('loadingpager').show();
        </prop:ClientSide.OnPreDispatch>					
        <prop:ClientSide.onComplete>						
            $('loadingpager').hide();
        </prop:ClientSide.OnComplete>					
    </com:TActivePager>
    <span ID="loadingpager" style="display:none;"> loading...</span>
</div>