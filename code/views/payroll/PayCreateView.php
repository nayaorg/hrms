<div class="ui-widget-content ui-corner_all" style="width:500px;margin: 5px 5px 5px 5px;">
	<div class="ui-widget-header" style="font-size:1.5em;height:30px;padding-left:10px">Create Pay Slip</div>
	<table>
		<tr style="height:30px;">
			<td style="width:80px;padding-left:10px"><span style="font-size:1.2em">Pay Date :</span></td>
			<td>
				<select id="cobPayCreateMonth"><?php echo Util::getMonthOption() ; ?></select>&nbsp;-&nbsp;
				<select id="cobPayCreateYear">><?php echo Util::getYearOption() ; ?></select>
			</td>
		</tr>
		<tr style="height:30px;">
			<td style="padding-left:10px;"><span style="font-size:1.2em">Company :</span></td>
			<td><select id="cobPayCreateCoy"><?php echo $this->getCompany() ; ?></select></td>
		</tr>
		<tr style="height:40px">
			<td></td>
			<td><input id="btnPayCreate" type="button" value="Create"></input></td>
		</tr>
	</table>
	<div class="sbg-entry-error">
		<span id="paycreate_err_mesg" class="sbg-error"></span>
	</div>
</div> 
<script type="text/javascript">
var paycreate_url = "<?php echo Util::convertLink("PayCreate") ; ?>" ;
<?php include (PATH_CODE . "js/payroll/paycreate.min.js") ; ?>
</script>