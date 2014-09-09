<head>
<script type="text/javascript" src="js/addItemClaim.js"></script>
</head>

<div class="form-horizontal">
	<input type="hidden" id="txtClaimId" value="<?php echo $claim_id?>">

	<div class="form-group">
		<div class="col-sm-8">
			<button id="btnSaveAll" style="float: right" class="btn btn-primary btn-lg" onclick="saveAllItem()">Save All</button>
		</div>
	</div>
	
	<div class="form-group">
		<label for="txtClaimItemDesc" class="col-sm-2 control-label">Description</label>
		<div class="col-sm-6">
			<textarea type="text" class="form-control" id="txtClaimItemDesc"> </textarea>
		</div>
		<span id="claim_item_err_desc" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<label for="cobExpenseItem" class="col-sm-2 control-label">Item</label>
		<div class="col-sm-3">
			<select class="form-control" id="cobExpenseItem">
				<?php 
					echo $this->getExpenseItem();
				?>
			</select>
		</div>
		<span id="claim_item_err_expense_item" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<label for="txtClaimItemAmount" class="col-sm-2 control-label">Amount</label>
		<div class="col-sm-6">
			<input type="text" class="form-control" id="txtClaimItemAmount">
		</div>
		<span id="claim_item_err_amount" style="color:red;display:none;padding-left:5px">*</span>
	</div>
	
	<div class="form-group">
		<label for="cobCurrency" class="col-sm-2 control-label">Currency</label>
		<div class="col-sm-3">
			<select class="form-control" id="cobCurrency">
				<?php 
					echo $this->getCurrency();
				?>
			</select>
		</div>
		<span id="claim_item_err_currency" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	<div class="form-group">
		<label for="txtClaimItemDocumentId" class="col-sm-2 control-label">Doc. ID</label>
		<div class="col-sm-6">
			<input type="text" class="form-control" id="txtClaimItemDocumentId">
		</div>
		<span id="claim_item_err_doc_id" style="color:red;display:none;padding-left:5px">*</span>
	</div>

	
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button id="btnAddItem" class="btn btn-primary" onclick="addItem()">Add</button>
		</div>
	</div>
</form>


<table class="table table-striped" id="tblItems" style="width: 80%;">
	<thead>
		<tr>
			<th width="5%">#</th>
			<th width="30%">Description</th>
			<th width="20%">Item</th>
			<th width="10%">Amount</th>
			<th width="15%">Curr.</th>
			<th width="15%">Doc. ID</th>
			<th width="5%">&nbsp</th>
		</tr>
	</thead>
	<tbody>
		
	</tbody>
</table>




