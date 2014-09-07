<div id="sbg-shift-detail-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="shift-detail-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#shift-detail-general">General</a></li>
			<span class="sbg-tab-title">Shift Detail Master</span>
		</ul>
		<div id="shift-detail-general" style="height:auto;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>ID : </span></td>
					<td style="width:480px"><input type="text" maxlength="10" size="10" id="txtShiftDetailId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;width:100px;"><span>Shift Type : </span></td>
					<td style="width:480px">
						<input type="radio" maxlength="10" size="10" name="rdoShiftDetailType" id="rdoShiftDetailType" value="0"/>Daily &nbsp;
						<input type="radio" maxlength="10" size="10" name="rdoShiftDetailType" id="rdoShiftDetailType" value="1"/>Weekly
					</td>
				</tr>
				<tr>
					<td style="text-align:right;width:100px;"><span>Shift Group : </span></td>
					<td style="width:480px">
						<select id="cobShiftGroup"><?php echo $this->getShiftGroup() ; ?></select>
						
						<span id="sg_err_group" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
							
				<table style="border-top:1px solid black;" id="shiftDaily">
						<?php 
						for($i=0; $i<5; $i++){
							echo "<tr>";
								for($j=(7*$i)+1; $j<=(7*$i)+7; $j++){ 
									if($j<=31){
										echo "<td style='text-align:center'>";
										echo str_pad($j,2,"0", STR_PAD_LEFT);
										echo "</td>";
									}
							 }
							echo "</tr>";
							echo "<tr>";
								for($j=(7*$i)+1; $j<=(7*$i)+7; $j++){ 
									if($j<=31){
										echo "<td style='text-align:center'>";
										echo "<select id='cobShift".str_pad($j,2,"0",STR_PAD_LEFT)."' class='cobShift'>";
										echo $this->getTimeCard();
										echo "</select>";
										echo "</td>";
									}
								}
							echo "</tr>";
						} ?>
					
				</table>
				<table style="border-top:1px solid black;"  id="shiftWeekly">
					<tr>
						<?php 
						$days=array(array('01','Mon'), array('02','Tue'), array('03','Wed'), array('04','Thu'), array('05','Fri'), array('06','Sat'), array('07','Sun'));
						foreach($days as $day){ ?>
						<td style="text-align:center">
						<?=$day[1];?>
						
						</td>
						<?php } ?>
					</tr>
					<tr>
						<?php 
						foreach($days as $day){ ?>
						<td style="text-align:center">
						<select id="cobShift<?=$day[1]?>" class='cobShift'><?php echo $this->getTimeCard() ; ?>></select>
						
						</td>
						<?php } ?>
					</tr>
				</table>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="shift_detail_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnShiftDetailAdd" type="button" value="Add"></input>
			<input id="btnShiftDetailClear" type="button" value="Clear"></input>
			<input id="btnShiftDetailUpdate" type="button" value="Update"></input>
			<input id="btnShiftDetailPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-shift-detail-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 100px;">Type</td>
                <td style="width: 400px;">Shift Group</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-shift-detail-data" style="overflow: auto;">
            <table id="sbg-shift-detail-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td><td style="width:100px"></td><td style="width:400px"></td><td style="width:25px"></td><td style="width:25px"></td></tr>
			<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var shift_detail_url = "<?php echo Util::convertLink("ShiftDetail") ; ?>" ;
<?php include (PATH_CODE . "js/attendance/shiftdetail.min.js") ; ?>
</script>