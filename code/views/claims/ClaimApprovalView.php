<div id="sbg-claim-entry" style="width:99%;margin: 5px 5px 5px 5px;">
	<div id="claim-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#claim-header">General</a></li>
			<li><a href="#claim-items">Items</a></li>
			<span class="sbg-tab-title">Claim Approval</span>
		</ul>
		<div id="claim-header" style="height:auto;text-align:left">
			<table>
				<tr>
					<td style="text-align:right;width:150px;"><span>ID : </span></td>
					<td><span id="lblClaimID"></span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><span id="lblDescription"></span>
					</td>
					<td style="text-align:right;"><span>Type : </span></td>
					<td><span id="lblType"></span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Date : </span></td>
					<td><span id="lblClaimDate"></span>
					</td>
					<td style="text-align:right">Emp. Name : </td>
					<td><span id="lblEmpName"></span></td>		
				</tr>
				<tr>
					<td style="text-align:right;"><span>Travel Plan : </span></td>
					<td><span id="lblTravelPlan"></span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Amount : </span></td>
					<td><span id="lblAmount"></span>
					</td>
					<td style="text-align:right;"><span>Approved Amount : </span></td>
					<td><span id="lblApprovedAmount"></span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Status : </span></td>
					<td><span id="lblStatus"></span>
					</td>
				</tr>
			</table>
			
		</div>
		<div id="claim-items" style="height:auto;text-align:left">
			<table id="item-list" class="sbg-table-list">
			</table>
		</div>
		
		<div class="ui-widget-content ui-corner-all" style="height:50px;margin-top:2px;" id='claimApprovalControlButton'>
			<div class="sbg-entry-error" style="width:300px;">
				<span id="claim_err_mesg" class="sbg-error"></span>
			</div>
			<div class="sbg-entry-command">
				<input id="btnClaimUpdate" type="button" value="Update"></input>
				<input id="btnClaimCancel" type="button" value="Cancel"></input>
			</div>
		</div>
		
	</div>
</div>
<input type="hidden" id="claim-group-view-as" value="<?php print $_SESSION[SE_USERID]; ?>" /> 


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
			<td style="width: 75px;">Type</td>
			<td style="width: 75px;">Date</td>
			<td style="width: 75px;">Claim By</td>
			<td style="width: 75px;">Status</td>
			<td style="width: 50px;">Amount</td>
			<td style="width: 50px;">Aprvd Amount</td>
			<td style="width: 100px;">Travelling Plan</td>
			</tr>
		</table>
	</div>
	<div id="sbg-claim-data" style="overflow: auto;">
		<table id="sbg-claim-table" cellspacing="0" cellpadding="5" class="data">
		<tr><td style="width:20px;height:1px"></td>
			<td style="width:120px"></td>
			<td style="width:75px"></td>
			<td style="width:75px"></td>
			<td style="width:75px"></td>
			<td style="width:75px"></td>
			<td style="width:50px"></td>
			<td style="width:50px"></td>
			<td style="width:100px"></td>
		</tr>
		</table>
	</div>
</div>

<script type="text/javascript">
var claim_approval_url = "<?php echo Util::convertLink("ClaimApproval") ; ?>" ;

<?php
	include (PATH_CODE . "js/claims/claimapproval.js") ;
?>
</script>