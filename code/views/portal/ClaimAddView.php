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
		<label for="txtClaimId" class="col-sm-2 control-label">ID</label>
		<div class="col-sm-6">
			<input type="text" value="<?php if (isset($row)) echo $row[ClaimHeaderTable::C_ID];?>" class="form-control" id="txtClaimId"
				placeholder="Auto" disabled="disabled">
		</div>
	</div>

	<div class="form-group">
		<label for="txtClaimDesc" class="col-sm-2 control-label">Description</label>
		<div class="col-sm-6">
			<textarea type="text" class="form-control" id="txtClaimDesc"> 
				<?php if (isset($row)) echo $row[ClaimHeaderTable::C_DESC];?>
			</textarea>
		</div>
		<span id="claim_err_desc" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<label for="cobClaimType" class="col-sm-2 control-label">Type</label>
		<div class="col-sm-3">
			<select class="form-control" id="cobClaimType">
				<option value="">-- Select a Claim Type --</option>
				<option value="0" 
					<?php if (isset($row)) { if ($row[ClaimHeaderTable::C_TYPE] == "0") echo "selected=\"selected\""; }?> > Personal
				</option>
				<option value="1"
					<?php if (isset($row)) { if ($row[ClaimHeaderTable::C_TYPE] == "1") echo "selected=\"selected\""; }?> > Business
				</option>
			</select>
		</div>
		<span id="claim_err_type" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	
	<div class="form-group">
		<label for="txtClaimDate" class="col-sm-2 control-label">Date</label>
		<div class="col-sm-3">
			<input type="text" class="form-control" id="txtClaimDate" 
			value="<?php if (isset($row)) echo date_format(date_create($row[ClaimHeaderTable::C_DATE]), 'd/m/Y');?>">
		</div>
		<span id="claim_err_date" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<label for="cobEmpId" class="col-sm-2 control-label">Department</label>
		<div class="col-sm-3">
			<select class="form-control" id="cobEmpId">
				<?php 
				if(isset($row))
					echo $this->getDepartment($row[ClaimHeaderTable::C_EMP]);
				else 
					echo $this->getDepartment();
				?>
			</select>
		</div>
		<span id="claim_by_err" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<label for="cobTravelPlan" class="col-sm-2 control-label">Travel
			Plan</label>
		<div class="col-sm-3">
			<select class="form-control" id="cobTravelPlan">
				<?php 
				if(isset($row))
					echo $this->getTravelPlan($row[ClaimHeaderTable::C_TRAVEL]);
				else 
					echo $this->getTravelPlan();
				?>
			</select>
		</div>
		<span id="travel_plan_err" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button 
				<?php 
					if(isset($row)) 
						echo "id=\"btnClaimEdit\"";
					else 
						echo "id=\"btnClaimAdd\"" ?>
			   class="btn btn-primary">Save
		    </button>
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