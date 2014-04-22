<com:NModalPanel ID="modalLookupMember" DefaultButton="btnSearch">
    <div class="modal" role="dialog" style="width:800px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" OnClick="new Modal.Box('<%=$this->modalLookupMember->ClientID%>').hide()"><i class="icon-remove"></i></button>
            <h3 id="myModalLabel">Lookup Members</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid">
                <div class="span12">
                    <div class="box">
                        <div class="box-title">
                            <h3>Search / Filter</h3>
                        </div>
                        <div class="box-content">
                            <div class="row-fluid">
                                <div class="span6">
                                    <div class="control-group">
                                        <com:TLabel ForControl="noiboFilter" CssClass="control-label" Text="No. IBO" />                                        
                                        <div class="controls">
                                            <com:TTextBox ID="noiboFilter" CssClass="input-medium" Attributes.OnKeyUp="formatangka(this,true)" />
                                        </div>
                                    </div> 
                                    <div class="control-group">
                                        <com:TLabel ForControl="membernameFilter" CssClass="control-label" Text="Nama Member" />                                        
                                        <div class="controls">
                                            <com:TTextBox Columns="30" ID="membernameFilter" CssClass="input-large" />
                                        </div>
                                    </div>
                                </div>
                                <div class="span6">
                                    <div class="control-group">
                                        <com:TLabel ForControl="cmbStatusFilter" CssClass="control-label" Text="Status" />                                        
                                        <div class="controls">
                                            <com:TDropDownList ID="cmbStatusFilter" AutoPostBack="false" CssClass="input-small">                    
                                                <com:TListItem Value="1" Text="Active"/>
                                                <com:TListItem Value="2" Text="Inactive"/>
                                                <com:TListItem Value="3" Text="Closed"/>
                                            </com:TDropDownList>
                                        </div>
                                    </div> 
                                    <div class="control-group">
                                        <com:TLabel ForControl="emailFilter" CssClass="control-label" Text="Email" />                                        
                                        <div class="controls">
                                            <com:TTextBox ID="emailFilter" CssClass="input-medium" />
                                        </div>
                                    </div>
                                </div>  
                            </div>
                            <div class="form-actions">
                                <com:TActiveLinkButton ID="btnSearch" Text="<i class='icon-search'></i> Search" OnClick="searchClient" CssClass="btn btn-primary">
                                    <prop:ClientSide.OnPreDispatch>
                                        $('loading').show();                                        
                                        $('<%=$this->btnSearch->ClientId%>').addClassName('disabled');						
                                    </prop:ClientSide.OnPreDispatch>
                                    <prop:ClientSide.OnLoading>
                                        $('<%=$this->btnSearch->ClientId%>').disabled='disabled';						
                                    </prop:ClientSide.OnLoading>
                                    <prop:ClientSide.OnComplete>																	
                                        $('<%=$this->btnSearch->ClientId%>').disabled='';						
                                        $('<%=$this->btnSearch->ClientId%>').removeClassName('disabled');                                        												
                                        $('loading').hide();
                                    </prop:ClientSide.OnComplete>
                                </com:TActiveLinkButton>
                            </div>                      
                        </div>                        
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <div class="box">
                        <div class="box-title">
                            <h3>Daftar Member</h3>
                        </div>
                        <div class="box-content">
                            <com:TActiveRepeater ID="RepeaterS" DataKeyField="member_id" AllowPaging="true" PageSize="10" AllowCustomPaging="true">
                                <prop:HeaderTemplate>			
                                    <table class="table table-advance">	
                                        <thead>
                                            <tr>												
                                                <td width="90">IBO</th>
                                                <td>Member Name</th>				                            
                                                <th width="10">Downline</th>																																		
                                                <th width="100">Omset Pribadi</th>
                                                <th >Status</th>
                                                <td>Created</th>	
                                            </tr>	
                                        </thead>
                                        <tbody>
                                </prop:HeaderTemplate>
                                <prop:ItemTemplate>
                                    <tr>				
                                        <td><%#$this->DataItem['ibo']==''?'-':$this->DataItem['ibo']%></td>
                                        <td class="left">
                                            <com:TActiveLinkButton ID="btnSelect" OnClick="Page.setMemberUplineInformation" CommandParameter="<%#$this->DataItem['member_id']%>_<%#$this->DataItem['ibo']%>_<%#$this->DataItem['member_name']%>" Text="<%#$this->DataItem['member_name']%>">
                                                <prop:ClientSide.OnPreDispatch>
                                                    $('loadingselectmember').show();                                
                                                </prop:ClientSide.OnPreDispatch>
                                                <prop:ClientSide.OnLoading>
                                                    $('<%=$this->btnSelect->ClientId%>').disabled='disabled';						
                                                </prop:ClientSide.OnLoading>
                                                <prop:ClientSide.OnComplete>																	
                                                    $('<%=$this->btnSelect->ClientId%>').disabled='';						                                
                                                    $('loadingselectmember').hide();
                                                </prop:ClientSide.OnComplete>
                                            </com:TActiveLinkButton>
                                        </td>				                    
                                        <td><%#$this->DataItem['downline']%></td>											
                                        <td><%#$this->DataItem['totalomset']%></td>
                                        <td><%#$this->DataItem['enabled']%></td>
                                        <td><%#$this->Page->TGL->tanggal('j/m/Y',$this->DataItem['date_reg'])%></td>	
                                    </tr>
                                </prop:ItemTemplate>				
                                <prop:AlternatingItemTemplate>
                                    <tr>				
                                        <td><%#$this->DataItem['ibo']==''?'-':$this->DataItem['ibo']%></td>
                                        <td class="left">
                                            <com:TActiveLinkButton ID="btnSelect" OnClick="Page.setMemberUplineInformation" CommandParameter="<%#$this->DataItem['member_id']%>_<%#$this->DataItem['ibo']%>_<%#$this->DataItem['member_name']%>" Text="<%#$this->DataItem['member_name']%>">
                                                <prop:ClientSide.OnPreDispatch>
                                                    $('loadingselectmember').show();                                
                                                </prop:ClientSide.OnPreDispatch>
                                                <prop:ClientSide.OnLoading>
                                                    $('<%=$this->btnSelect->ClientId%>').disabled='disabled';						
                                                </prop:ClientSide.OnLoading>
                                                <prop:ClientSide.OnComplete>																	
                                                    $('<%=$this->btnSelect->ClientId%>').disabled='';						                                
                                                    $('loadingselectmember').hide();
                                                </prop:ClientSide.OnComplete>
                                            </com:TActiveLinkButton>
                                        </td>
                                        <td><%#$this->DataItem['downline']%></td>											
                                        <td><%#$this->DataItem['totalomset']%></td>
                                        <td><%#$this->DataItem['enabled']%></td>
                                        <td><%#$this->Page->TGL->tanggal('j/m/Y',$this->DataItem['date_reg'])%></td>															
                                    </tr>
                                </prop:AlternatingItemTemplate>
                                <prop:FooterTemplate>
                                    </table>
                                </prop:FooterTemplate>		
                            </com:TActiveRepeater>
                            <div class="pagination">                            
                                <com:TActiveCustomPager ID="pager" OnCallBack="Page.renderCallback" ControlToPaginate="RepeaterS" Mode="Numeric" OnPageIndexChanged="Page.Page_Changed" PrevPageText="&laquo; Previous" NextPageText="Next &raquo;" PageButtonCount="10" FirstPageText="First" LastPageText="Last" CssClass="pagination text-center">	
                                    <prop:ClientSide.OnPreDispatch>
                                        $('loading').show();
                                    </prop:ClientSide.OnPreDispatch>					
                                    <prop:ClientSide.onComplete>						
                                        $('loading').hide();
                                    </prop:ClientSide.OnComplete>					
                                </com:TActiveCustomPager>
                                <span ID="loadingpager" style="display:none;"> loading...</span>
                            </div>        
                            <div id="loadingselectmember" style="display:none">
                                Please wait while we are process your request... <img src="<%=$this->Page->Theme->baseUrl%>/css/images/ajax-loader-1.gif" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>          
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" OnClick="new Modal.Box('<%=$this->modalLookupMember->ClientID%>').hide()">Close</a>
        </div>
    </div>
   
</com:NModalPanel>