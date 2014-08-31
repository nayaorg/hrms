<div id="sbg-resetpwd-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="resetpwd-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#resetpwd-general">General</a></li>
			<span class="sbg-tab-title">Reset Password</span>
		</ul>
		<div id="resetpwd-general">
			<table>
				<tr style="height:30px">
					<td style="text-align:right;width:140px"><span>User ID : </span></td>
					<td style="width:460px"><span id="lblResetPwdId"></span></td>
				</tr>
				<tr style="height:30px">
					<td style="text-align:right"><span>User Name : </span></td>
					<td><span id="lblResetPwdName"></span></td>
				</tr>
				<tr style="height:30px">
					<td style="text-align:right"><span>Full Name : </span></td>
					<td><span id="lblResetPwdFull"></span></td>
				</tr>
				<tr style="height:30px">
					<td style="text-align:right"><span>Last Login : </span></td>
					<td><span id="lblResetPwdLogin"></span></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="resetpwd_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnResetPwd" type="button" value="Reset"></input>
			<input id="btnResetPwdClear" type="button" value="Clear"></input>
		</div>
	</div>
</div>
<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-resetpwd-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 100px;">Name</td>
                <td style="width: 300px;">Full Name</td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-resetpwd-data" style="overflow: auto;">
            <table id="sbg-resetpwd-table" cellspacing="0" cellpadding="5" class="data">
				<tr>
				<td style="width:50px;height:1px"></td>
				<td style="width:100px"></td>
				<td style="width:300px"></td>
				<td style="width:25px"></td>
				</tr>
				<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var resetpwd_url = "<?php echo Util::convertLink("ResetPwd") ; ?>" ;
<?php include (PATH_CODE . "js/admin/resetpwd.min.js") ; ?>
</script>