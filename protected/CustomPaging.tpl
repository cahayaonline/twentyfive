<com:TActiveCustomPager ID="pager" OnCallBack="Page.renderCallback" ControlToPaginate="RepeaterS" Mode="Numeric" OnPageIndexChanged="Page.Page_Changed" PrevPageText="&laquo; Previous" NextPageText="Next &raquo;" PageButtonCount="10" FirstPageText="First" LastPageText="Last" CssClass="pagination text-center">	
    <prop:ClientSide.OnPreDispatch>
        $('loading').show();
    </prop:ClientSide.OnPreDispatch>					
    <prop:ClientSide.onComplete>						
        $('loading').hide();
    </prop:ClientSide.OnComplete>					    
</com:TActiveCustomPager>