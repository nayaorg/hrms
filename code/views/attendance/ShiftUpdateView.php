<div id="sbg-shift-update-entry" style="width:660px;margin: 5px 5px 5px 5px;">
	<div id="shift-update-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#shift-update-general">General</a></li>
			<span class="sbg-tab-title">Shift Update Master</span>
		</ul>
		<div id="shift-update-general" style="height:auto;">
			<table>
				<table style="border:1px solid #9d9d9d; padding:3px; width:100%;">
				<tr>
					<td style="width:480px;">
						<input type="radio" name="rdoType" id="rdoType" value="C"/>Collective &nbsp;
						<input type="radio" name="rdoType" id="rdoType" value="I"/>Individual
					</td>
					<td style="width:480px;">
						MONTH :
						<select id="cobMonth">
						<?php
						for($i=1; $i<=12; $i++){
							$month=str_pad($i, "2", "0", STR_PAD_LEFT);
							echo "<option value='".$month."' ".(($month==date('m'))?"selected":"").">".$month."</option>";
						}
						?>
						</select>
						&nbsp;
						YEAR :
						<select id="cobYear">
						<?php
						for($i=date('Y'); $i<=(date('Y')+5); $i++)
							echo "<option value='".$i."'>".$i."</option>";
						?>
						</select>
					</td>
				</tr>
				</table>
				
				<table style="border:1px solid #9d9d9d; padding:3px; width:100%; height:70px;margin:5px 0px;" id="shiftID">
				<tr class='shiftCollective'>
					<td style="text-align:right;">Shift Type :</td> 
					<td>
						<input type="radio" maxlength="10" size="10" name="rdoShiftUpdateType" id="rdoShiftUpdateType" value="0"/>Daily &nbsp;
						<input type="radio" maxlength="10" size="10" name="rdoShiftUpdateType" id="rdoShiftUpdateType" value="1"/>Weekly
					</td>
				</tr>
				<tr class='shiftCollective'>
					<td style="text-align:right;">Shift Group :</td> 
					<td>
						<select id="cobShiftGroup"><?php echo $this->getShiftGroup() ; ?></select>
						
						<span id="sg_err_group" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
					
				<tr class='shiftIndividual'>
					<td style="text-align:right;"><span>Dept : </span></td>
					<td>
						<select id="cobDept"><?php echo $this->getDepartment() ; ?></select>
					</td>
				</tr>
					
				<tr class='shiftIndividual'>
					<td style="text-align:right;"><span>Emp. : </span></td>
					<td>
						<select id="cobEmpId"></select>
					</td>
				</tr>
				</table>
				
				<table style="border:1px solid #9d9d9d; padding:3px; width:100%; margin:5px 0px;" id="shiftDaily">
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
				<table style="border:1px solid #9d9d9d; padding:3px; width:100%; margin:5px 0px;"  id="shiftWeekly">
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
						<select id="cobShift<?=$day[1]?>" class='cobShift'><?php 
																	echo $this->getTimeCard() ; 
																	?></select>
						
						</td>
						<?php } ?>
					</tr>
				</table>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="shift_update_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnShiftUpdateUpdate" type="button" value="Update"></input>
			<input id="btnShiftUpdateClear" type="button" value="Clear"></input>
		</div>
	</div>
</div>


<script type="text/javascript">
var shift_update_url = "<?php echo Util::convertLink("ShiftUpdate") ; ?>" ;
<?php 
	include (PATH_CODE . "js/attendance/shiftupdate.min.js") ; 
	
?>
</script>