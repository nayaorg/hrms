<head>
<script type="text/javascript" src="js/addItemClaim.js"></script>
</head>

<div class="form-horizontal">
	<div class="form-group">
		<label for="txtItemDesc" class="col-sm-2 control-label">Description</label>
		<div class="col-sm-6">
			<textarea type="text" class="form-control" id="txtItemDesc"> </textarea>
		</div>
		<span id="claim_err_desc" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<label for="cobEmpId" class="col-sm-2 control-label">Item</label>
		<div class="col-sm-3">
			<select class="form-control" id="cobEmpId">
				<?php 
					echo $this->getExpenseItem();
				?>
			</select>
		</div>
		<span id="claim_by_err" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<label for="txtItemDesc" class="col-sm-2 control-label">Amount</label>
		<div class="col-sm-6">
			<input type="text" class="form-control" id="txtItemDesc">
		</div>
		<span id="claim_err_desc" style="color:red;display:none;padding-left:5px">*</span>
	</div>
	
	<div class="form-group">
		<label for="cobEmpId" class="col-sm-2 control-label">Currency</label>
		<div class="col-sm-3">
			<select class="form-control" id="cobEmpId">
				<?php 
					echo $this->getExpenseItem();
				?>
			</select>
		</div>
		<span id="claim_by_err" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<label for="txtItemDesc" class="col-sm-2 control-label">Doc. ID</label>
		<div class="col-sm-6">
			<input type="text" class="form-control" id="txtItemDesc">
		</div>
		<span id="claim_err_desc" style="color:red;display:none;padding-left:5px">*</span>
	</div>
	
	

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button id="btnClaimEdit" class="btn btn-primary">Save</button>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<span id="claim_err_mesg" style="color:red;padding-left:5px"></span>
		</div>
	</div>
</form>