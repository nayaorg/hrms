<div id="sbg-travel-plan-entry" style="width:500px;margin: 5px 5px 5px 5px;">
	<div id="travel-plan-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#travel-plan-header">General</a></li>
			<span class="sbg-tab-title">Travel Plan</span>
		</ul>
		
		<div id="travel-plan-header" style="height:auto;">
			<table>
				<tr>
					<td style="text-align:right;"><span>ID : </span></td>
					<td>
						<input type="text" id="txtTravelPlanId" size="10" value="Auto" disabled="disabled" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Title : </span></td>
					<td>
						<input type="text" maxlength="200" size="30" id="txtTravelPlanTitle" />
						<span id="travel_plan_err_title" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td>
						<input type="text" maxlength="4000" size="50" id="txtTravelPlanDesc" />
						<span id="travel_plan_err_desc" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Country : </span></td>
					<td><select id="cobTravelPlanCountry"><?php echo $this->getCountry() ; ?></select>
						<span id="travel_plan_err_country" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Start Date : </span></td>
					<td>
						<input type="text" maxlength="200" size="30" id="txtTravelDate" />
						<span id="travel_plan_err_start" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Expiry Date : </span></td>
					<td>
						<input type="text" maxlength="200" size="30" id="txtTravelExpiry" />
						<span id="travel_plan_err_expiry" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
			</table>
		</div>
		
		<div class="ui-widget-content ui-corner-all" style="height:50px;margin-top:2px;">
			<div class="sbg-entry-error" style="width:300px;">
				<span id="travel_plan_err_mesg" class="sbg-error"></span>
			</div>
			<div class="sbg-entry-command">
				<input id="btnTravelPlanAdd" type="button" value="Add"></input>
				<input id="btnTravelPlanUpdate" type="button" value="Update"></input>
				<input id="btnTravelPlanClear" type="button" value="Clear"></input>
				<input id="btnTravelPlanPrint" type="button" value="Print"></input>
			</div>
		</div>
	</div>
	
</div>

<div id="travel-plan-header-content" class="sbg-table">
	<div class="ui-widget-header">
		<table id="sbg-travel-plan-header" class="header" cellspacing="0" cellpadding="5">
			<tr class="ui-widget-header">
				<td style="width: 50px;">ID</td>
                <td style="width: 200px;">Title</td>
                <td style="width: 400px;">Description</td>
				<td style="width: 150px;">Country</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
			</tr>
		</table>
	</div>
	<div id="sbg-travel-plan-data" style="overflow: auto;">
		<table id="sbg-travel-plan-table" cellspacing="0" cellpadding="5" class="data">
		<tr><td style="width:50px;height:1px"></td>
			<td style="width:200px"></td>
			<td style="width:400px"></td>
			<td style="width:150px"></td>
			<td style="width:25px"></td>
			<td style="width:25px"></td>
		</tr>
		<?php echo $this->getList() ; ?>
		</table>
	</div>
</div> 

<script type="text/javascript">
	var travel_plan_url = "<?php echo Util::convertLink("TravelPlan") ; ?>" ;
	<?php include (PATH_CODE . "js/claims/travelplan.min.js") ; ?>
</script>