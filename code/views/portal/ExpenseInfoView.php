<head>
<link href="css/expense.css" rel="stylesheet">
<script type="text/javascript" src="js/claim.js"></script>
<style type="text/css">
.table {
	width: 100%;
	max-width: 100%;
	margin-bottom: 20px;
}
button {
 	margin-left: 2px;
 	margin-right: 2px;
}
</style>
</head>

<a href="#addClaimHeaderView" role="button" class="btn btn-large btn-primary" data-toggle="modal">Launch Demo Modal</a>
 
<!-- Add Claim Header Modal HTML -->
<div id="addClaimHeaderView" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body">
                <p>Do you want to save changes you made to document before closing?</p>
                <p class="text-warning"><small>If you don't save, your changes will be lost.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
 
<div id="example" class="modal hide fade in" style="display: none; ">  
	<div class="modal-header">  
		<a class="close" data-dismiss="modal">×</a>  
		<h3>This is a Modal Heading</h3>  
	</div> 
	 
	<div class="modal-body">  
		<h4>Text in a modal</h4>  
		<p>You can add some text here.</p>                
	</div>  
	
	<div class="modal-footer">  
		<a href="#" class="btn btn-success">Call to action</a>  
		<a href="#" class="btn" data-dismiss="modal">Close</a>  
	</div>  
</div>  



<div class="col-md-11" style="margin-top: 20px">
	<div class="filter-unit">
		<input  style="height: 30px;"  type="text" placeholder="From Date"
			id="fromDate">
	</div>

	<div class="filter-unit">
		<input style="height: 30px;" type="text" placeholder="To Date"
			id="toDate">
	</div>
	
	<div class="filter-unit">
		<a href="#" onclick="return filter();" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-plus"></span> Filter</a>
	</div>
	
	<div class="add-claim-btn">
		<a href="<?php // echo $this->createMenuFunc("PortalClaim","Add Claim/Expense", PORTAL_CLAIM_ADD_VIEW)?>" onclick="return loadAddModal();"
		   class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-plus"></span> Add</a>
	</div>
</div>

<div class="col-md-11" id="errorShow"></div>

<div class="col-md-11" style="margin-top: 20px">
	<div class="box" id="testData">
		<table class="table table-striped" id="expense-table">
			<thead>
				<tr>
					<th width="30%">Description</th>
					<th width="10%">Type</th>
					<th width="10%">Amount</th>
					<th width="10%">Status</th>
					<th width="10%">Date</th>
					<th width="30">&nbsp</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$list = $this->getClaimList ("","");
				foreach ( $list as $expenseItem ) {
					?>
					<tr>
						<td><?php echo $expenseItem['desc'];?></td>
						<td><?php echo $expenseItem['type'];?></td>
						<td><?php echo $expenseItem['amount'];?></td>
						<td><?php echo $expenseItem['status'];?></td>
						<td><?php echo $expenseItem['date'];?></td>
						<td>
							<button onclick="return editHeader(<?php echo $expenseItem['id'];?>);" id="btnEdit" class="btn btn-primary">Edit Header</button>
							<button onclick="return uploadDoc(<?php echo $expenseItem['id'];?>);" id="btnUpload" class="btn btn-primary">Upload Doc</button>
							<button onclick="return addItem(<?php echo $expenseItem['id'];?>);" id="btnAdd" class="btn btn-primary">Add Item</button>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	</div>
</div>

<?php $arrNav = $this->getAddClaimNavigation();?>
<script>
	function changeMainHeaderAddClaim() {
		var navNameArr = <?php echo json_encode($arrNav[0])?>;
		var navURLArr  = <?php echo json_encode($arrNav[1])?>;
		var navMenuActiveLArr  = <?php echo json_encode($arrNav[2])?>;
		changeMainHeader(navNameArr, navURLArr, navMenuActiveLArr);
	}
</script>


