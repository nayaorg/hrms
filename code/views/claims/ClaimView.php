<div id="sbg-claim-entry" style="width:750px;margin: 5px 5px 5px 5px;">
	<div id="claim-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#claim-header">General</a></li>
			<li><a href="#claim-documents">Documents</a></li>
			<li><a href="#claim-items">Items</a></li>
			<span class="sbg-tab-title">Claim</span>
		</ul>
		
		<div id="claim-header" style="height:auto;text-align:left">
			<input type="hidden" id="txtClaimAmount" value="0.00" />
			<input type="hidden" id="txtClaimStatus" value="0" />
			<input type="hidden" id="txtClaimApprovedAmount" value="0.00" />
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>ID : </span></td>
					<td style="width:470px" colspan='3'>
						<input type="text" maxlength="10" size="10" id="txtClaimId" value="Auto" disabled="disabled" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td colspan='3'>
						<input type="text" size="50" id="txtClaimDesc" />
						<span id="claim_err_desc" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Type : </span></td>
					<td colspan='3'>
						<select id="cobClaimType">
							<option value="">-- Select a Claim Type --</option>
							<option value="0">Personal</option>
							<option value="1">Business</option>
						</select><span id="claim_err_type" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Date : </span></td>
					<td colspan='3'>
						<input type="text" maxlength="30" size="15" id="txtClaimDate" value="" />
						<span id="claim_err_date" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Dept. : </td>
					<td>
						<select id="cobDept"><?php echo $this->getDeptGroup() ; ?></select>
							&nbsp;&nbsp;&nbsp; Emp. : 
						<select id="cobEmpId"></select>
						<span id="claim_by_err" style="color:red;padding-left:5px">*</span>
					</td>		
				</tr>
				<tr>
					<td style="text-align:right;"><span>Travel Plan : </span></td>
					<td colspan='3'>
						<select id="cobTravelPlan"><?php echo $this->getTravelPlan() ; ?></select>
						<span id="travel_plan_err" style="color:red;padding-left:5px">*</span>
					</td>
				</tr>
			</table>
		</div>
		
		<div id="claim-items">
			<table>
				<!--<tr>
					<td style="text-align:right;"><span>Claim Item Line No. : </span></td>
					<td>
						<input type="hidden" id="txtClaimItemClaimId" value="" />
						<input type="text" maxlength="200" size="10" id="txtClaimItemLineNo" disabled="disabled" />
					</td>
				</tr>-->
				<tr>
					<td style="text-align:right;"><span>Desc. : </span></td>
					<td colspan='3'>
						<input type="text" maxlength="200" size="40" id="txtClaimItemDesc" />
						<span id="claim_item_err_desc" style="color:red;display:none;padding-left:5px">*</span>
					</td>
					<td style="text-align:right;width:auto"><span>Item : </span></td>
					<td><select id="cobExpenseItem"><?php echo $this->getExpenseItem() ; ?></select>
						<span id="claim_item_err_expense_item" style="color:red;display:none;padding-left:5px">*</span>
					</td>
					<td style="width:40px;text-align:center" rowspan='2'><a href="javascript:" onclick="addItem()"><img src="image/add.png" title="Add Limit" style="width:24px;height:24px"></img></a></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Amount : </span></td>
					<td>
						<input type="text" maxlength="200" size="10" id="txtClaimItemAmount" />
						<span id="claim_item_err_amount" style="color:red;display:none;padding-left:5px">*</span>
					</td>
					<td style="text-align:right;"><span>Curr. : </span></td>
					<td><select id="cobCurrency"><?php echo $this->getCurrency() ; ?></select>
						<span id="claim_item_err_currency" style="color:red;display:none;padding-left:5px">*</span>
					</td>
					<td style="text-align:right;"><span>Doc. ID : </span></td>
					<td>
						<input type="text" maxlength="200" size="20" id="txtClaimItemDocumentId" />
						<span id="claim_item_err_doc_id" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<th style="width:30px"></th>
					<th style="width:100px">Desc.</th>
					<th style="width:100px">Item</th>
					<th style="width:100px">Amount</th>
					<th style="width:100px">Curr.</th>
					<th style="width:100px">Doc. ID</th>
				</tr>
			</table>
			
			
			<div style="overflow: auto;height:auto;">
				<table id="tblItems" class="sbg-table-list" cellspacing="0">
					
				</table>
			</div>
			
		</div>
		
		<div id="claim-documents">
			<table>
				<tr>
					<td style="text-align:right;display:none"><span>ID : </span></td>
					<td style="display:none">
						<input type="text" maxlength="200" size="10" id="txtClaimDocId" value="Auto" disabled="disabled" />
						<input type="hidden" id="txtClaimDocClaimId" value="" />
					</td>
					<td style="text-align:right;"><span>Ref. No. : </span></td>
					<td>
						<input type="text" maxlength="200" size="20" id="txtClaimDocRef" />
						<span id="claim_doc_err_ref" style="color:red;display:none;padding-left:5px">*</span>
					</td>
					<td style="text-align:right;"><span>Desc. : </span></td>
					<td>
						<input type="text" maxlength="200" size="30" id="txtClaimDocDesc" />
						<span id="claim_doc_err_desc" style="color:red;display:none;padding-left:5px">*</span>
					</td>
					<td style="width:40px;text-align:center" rowspan='2'><a href="javascript:" onclick="addDocs()"><img src="image/add.png" title="Add Limit" style="width:24px;height:24px"></img></a></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>File : </span></td>
					<td colspan=4>
						<input type="file" size="50" id="fileClaimDoc" name="fileClaimDoc"/>
						<input type="hidden" id="txtClaimDocPath" />
						
						<span id="claim_doc_err_file" style="color:red;display:none;padding-left:5px">*</span>
						
						<div id="claim_doc_upload" style="margin-top:10px;display:none;">
							<div><span id="claim_doc_upload_mesg">Uploading file .....</span></div>
							<div style="margin-top:10px"><img src="image/uploading.gif"></div>
						</div>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<th style="width:30px"></th>
					<th style="width:100px">Ref. No.</th>
					<th style="width:250px">Desc</th>
					<th style="width:250px">File</th>
					<th style="width:10px;display:none">Claim ID</th>
				</tr>
			</table>
			
			
			<div style="overflow: auto;height:auto;">
				<table id="tblDocs" class="sbg-table-list" cellspacing="0">
					
				</table>
			</div>
		</div>
		
		<div class="ui-widget-content ui-corner-all" style="height:50px;margin-top:2px;">
			<div class="sbg-entry-error" style="width:300px;">
				<span id="claim_err_mesg" class="sbg-error"></span>
			</div>
			<div class="sbg-entry-command">
				<input id="btnClaimAdd" type="button" value="Add"></input>
				<input id="btnClaimUpdate" type="button" value="Update"></input>
				<input id="btnClaimClear" type="button" value="Clear"></input>
				<input id="btnClaimPrint" type="button" value="Print"></input>
			</div>
		</div>
	</div>
</div>

<table height="5px"></table>
<div id="claim-filter" class="ui-widget-content ui-corner-all" style="width: 99%; margin-left: 5px;">
	<table cellspacing="3">
		<tr>
			<td>Filter: </td>
			<td>
				<select id="claim-filter-by">
					<option value="">All</option>
					<option value="CLAIM_STATUS">Approval</option>
					<option value="CLAIM_TYPE">Type</option>
				</select>
			</td>
			<td>
				<select id="claim-filter-option">
					<option value="">-</option>
				</select>
			</td>
			<td>
				<input id="btnClaimFilter" type="button" value="Filter">
			</td>
	</table>
</div>

<div id="claim-header-content" class="sbg-table">
	<div class="ui-widget-header">
		<table id="sbg-claim-header" class="header" cellspacing="0" cellpadding="5">
			<tr class="ui-widget-header">
			<td style="width: 20px;">ID</td>
			<td style="width: 120px;">Description</td>
			<td style="width: 50px;">Type</td>
			<td style="width: 75px;">Date</td>
			<td style="width: 75px;">Claim By</td>
			<td style="width: 50px;">Status</td>
			<td style="width: 50px;">Amount</td>
			<td style="width: 50px;">Apprvd Amount</td>
			<td style="width: 100px;">Travelling Plan</td>
			</tr>
		</table>
	</div>
	<div id="sbg-claim-data" style="overflow: auto;">
		<table id="sbg-claim-table" cellspacing="0" cellpadding="5" class="data">
		<tr><td style="width:20px;height:1px"></td>
			<td style="width:120px"></td>
			<td style="width:50px"></td>
			<td style="width:75px"></td>
			<td style="width:75px"></td>
			<td style="width:50px"></td>
			<td style="width:50px"></td>
			<td style="width:50px"></td>
			<td style="width:100px"></td>
		</tr>
		</table>
	</div>
</div>
\
<script type="text/javascript">
var claim_url = "<?php echo Util::convertLink("Claim") ; ?>" ;

<?php include (PATH_CODE . "js/claims/claim.js") ;?>

var claim_doc_url = "<?php echo Util::convertLink("ClaimDocument") ; ?>" ;

<?php include (PATH_CODE . "js/claims/claimdocument.js") ; ?>
</script>