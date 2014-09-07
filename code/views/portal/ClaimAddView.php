<head>
<style>
	label {
		font-weight: normal !important;
	}

</style>
<script type="text/javascript" src="js/addClaim.js"></script>
</head>


<div class="form-horizontal">
	<div class="form-group">
		<label for="claim_id" class="col-sm-2 control-label">ID</label>
		<div class="col-sm-6">
			<input type="text" class="form-control" id="claim_id"
				placeholder="Auto" disabled="disabled">
		</div>
	</div>

	<div class="form-group">
		<label for="txtClaimDesc" class="col-sm-2 control-label">Description</label>
		<div class="col-sm-6">
			<textarea type="text" class="form-control" id="txtClaimDesc"> </textarea>
		</div>
		<span id="claim_err_desc" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<label for="cobClaimType" class="col-sm-2 control-label">Type</label>
		<div class="col-sm-3">
			<select class="form-control" id="cobClaimType">
				<option value="">-- Select a Claim Type --</option>
				<option value="0">Personal</option>
				<option value="1">Business</option>
			</select>
		</div>
		<span id="claim_err_type" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<label for="txtClaimDate" class="col-sm-2 control-label">Date</label>
		<div class="col-sm-3">
			<input type="text" class="form-control" id="txtClaimDate">
		</div>
		<span id="claim_err_date" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<label for="cobEmpId" class="col-sm-2 control-label">Department</label>
		<div class="col-sm-3">
			<select class="form-control" id="cobEmpId">
				<?php echo $this->getDepartment();?>
			</select>
		</div>
		<span id="claim_by_err" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<label for="cobTravelPlan" class="col-sm-2 control-label">Travel
			Plan</label>
		<div class="col-sm-3">
			<select class="form-control" id="cobTravelPlan">
				<?php echo $this->getTravelPlan() ; ?>
			</select>
		</div>
		<span id="travel_plan_err" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button id="btnClaimAdd" class="btn btn-primary">Save</button>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<span id="claim_err_mesg" style="color:red;padding-left:5px"></span>
		</div>
	</div>
</form>


<script>
	$('#txtClaimDate').datepicker({
	    format: "dd/mm/yyyy"
	});
</script>