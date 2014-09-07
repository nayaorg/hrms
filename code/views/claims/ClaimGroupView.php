<div id="sbg-claim-group-entry" style="width:700px;margin: 5px 5px 5px 5px;">
	<div id="claim-group-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#claim-group-general">General</a></li>
			<li><a href="#claim-group-head">Manager</a></li>
			<li><a href="#claim-group-emp">Employee</a></li>
			<span class="sbg-tab-title">Claim Group</span>
		</ul>
		
		<div id="claim-group-general" style="height:200px;">
			<table>
				<tr>
					<td style="text-align:right;"><span>ID : </span></td>
					<td>
						<input type="text" id="txtClaimGroupId" size="10" value="Auto" disabled="disabled" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td>
						<input type="text" size="50" id="txtClaimGroupDesc" />
						<span id="claim_group_err_desc" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
			</table>
		</div>
		
		<div id="claim-group-head" style="overflow:auto; height:200px;">
			<table>
				<tr>
					<td style="width:200px">Dept. : <select id="cobDeptHead"><?php echo $this->getDeptGroup() ; ?></select></td>		
					<td style="width:200px">Emp. : <select id="cobEmpHead"></select></td>
					<td style="width:40px;text-align:center"><a href="javascript:" onclick="addHeadLimit()"><img src="image/add.png" title="Add Limit" style="width:24px;height:24px"></img></a></td>
				</tr>
			</table>
			<table>
				<tr>
					<td style="width:30px"></td>
					<td style="width:50px;display:none">Emp ID<input id="txtHeadId" style="display:none"></input></td>
					<td style="width:200px">Employee<input id="txtHead" style="display:none"></input></td>
				</tr>
			</table>
			<div style="overflow: auto;height:150px;">
				<table id="tblHeadLimit" class="sbg-table-list" cellspacing="0">
					
				</table>
			</div>
		</div>
		
		<div id="claim-group-emp" style="height:200px;">
		<table>
			<tr>
				<td style="width:200px">Dept. : <select id="cobDept"><?php echo $this->getDeptGroup() ; ?></select></td>		
				<td style="width:200px">Emp. : <select id="cobEmp"></select></td>
				<td style="width:40px;text-align:center"><a href="javascript:" onclick="addEmpLimit()"><img src="image/add.png" title="Add Limit" style="width:24px;height:24px"></img></a></td>
			</tr>
		</table>
			<table>
				<tr>
					<td style="width:30px"></td>
					<td style="width:50px;display:none">Emp. ID<input id="txtEmpId" style="display:none"></input></td>
					<td style="width:200px">Employee<input id="txtEmployee" style="display:none"></input></td>
				</tr>
			</table>
			<div style="overflow: auto;height:150px;">
				<table id="tblLimit" class="sbg-table-list" cellspacing="0">
					
				</table>
			</div>
		</div>
		
		<div class="ui-widget-content ui-corner-all" style="height:50px;margin-top:2px;">
			<div class="sbg-entry-error" style="width:300px;">
				<span id="claim_group_err_mesg" class="sbg-error"></span>
			</div>
			<div class="sbg-entry-command">
				<input id="btnClaimGroupAdd" type="button" value="Add"></input>
				<input id="btnClaimGroupUpdate" type="button" value="Update"></input>
				<input id="btnClaimGroupClear" type="button" value="Clear"></input>
			</div>
		</div>
	</div>
	
</div>

<div id="claim-group-general-content" class="sbg-table">
	<div class="ui-widget-header">
		<table id="sbg-claim-group-general" class="header" cellspacing="0" cellpadding="5">
			<tr class="ui-widget-header">
				<td style="width: 50px;">ID</td>
                <td style="width: 250px;">Description</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
			</tr>
		</table>
	</div>
	<div id="sbg-claim-group-data" style="overflow: auto;">
		<table id="sbg-claim-group-table" cellspacing="0" cellpadding="5" class="data">
		<tr><td style="width:50px;height:1px"></td>
			<td style="width:250px"></td>
			<td style="width:25px;"></td>
			<td style="width:25px;"></td>
		</tr>
		</table>
	</div>
</div> 

<script type="text/javascript">
	var claim_group_url = "<?php echo Util::convertLink("ClaimGroup") ; ?>" ;
	<?php include (PATH_CODE . "js/claims/claimgroup.js") ; ?>
</script>