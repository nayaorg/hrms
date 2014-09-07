<div id="sbg-setting-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="setting-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#setting-general">General</a></li>
			<li><a href="#setting-addr">Contact</a></li>
			<li><a href="#setting-logo">Logo</a></li>
			<span class="sbg-tab-title">System Setting</span>
		</ul>
		<div id="setting-general" style="height:150px;">
			<table>
				<tr>
					<td style="text-align:right;"><span>Organization Name : </span></td>
					<td><input type="text" maxlength="50" size="50" id="txtSettingName1" value="<?php echo $this->getName1() ;?>" />
					<span id="setting_err_name1" style="color:Red;display:none">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Organization Code : </span></td>
					<td><input type="text" maxlength="10" size="10" id="txtSettingCode" value="<?php echo $this->getCode() ;?>" />
					<span id="setting_err_code" style="color:Red;display:none">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Ref No : </span></td>
					<td><input type="text" maxlength="20" size="20" id="txtSettingRefNo" value="<?php echo $this->getRefNo() ;?>" /></td>
				</tr>
			</table>
		</div>
		<div id="setting-addr" style="height:150px;">
			<table>
				<tr>
					<td style="text-align:right;"><span>Address : </span></td>
					<td><input type="text" maxlength="40" size="50" id="txtSettingAddr1" value="<?php echo $this->getAddr1() ;?>" /></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="text" maxlength="40" size="50" id="txtSettingAddr2" value="<?php echo $this->getAddr2() ;?>" /></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="text" maxlength="40" size="50" id="txtSettingAddr3" value="<?php echo $this->getAddr3() ;?>" /></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="text" maxlength="40" size="50" id="txtSettingAddr4" value="<?php echo $this->getAddr4() ;?>" /></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="text" maxlength="40" size="50" id="txtSettingAddr5" value="<?php echo $this->getAddr5() ;?>" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Tel / Fax No : </span></td>
					<td>
						<input type="text" maxlength="20" size="20" id="txtSettingTelNo" value="<?php echo $this->getTelNo() ;?>" />&nbsp;&nbsp;/&nbsp;&nbsp;
						<input type="text" maxlength="20" size="20" id="txtSettingFaxNo" value="<?php echo $this->getFaxNo() ; ?>" />
					</td>
				</tr>
			</table>
		</div>
		<div id="setting-logo" style="height:150px;">
			<table>
				<tr>
					<td colspan="2">Logo File :
						<input type="file" id="fileLogo" name="fileLogo" size="60"></input>
					</td>
				</tr>
				<tr>
					<td style="width:400px;" id="imgSettingLogo">
						<div style="margin-top:5px"><img id="imgLogo" width="200px" height="100px" border="1" src="<?php echo $this->getLogo() ; ?>"></img></div>
						<div style="margin-top:5px">Size : 200(w) x 100(h). Format : png,jpg,jpeg</div>
					</td>
					<td style="width:200px;vertical-align:top">
						<div style="margin-top:10px;">
							<input id="btnSettingUpload" type="button" value="Upload">&nbsp;&nbsp;</input>
							<input id="btnSettingRemove" type="button" value="Remove"></input>
						</div>
						<div id="setting_upload" style="margin-top:10px;display:none;">
							<div><span id="setting_upload_mesg">Uploading file .....</span></div>
							<div style="margin-top:10px"><img src="image/uploading.gif"></div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="setting_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnSettingUpdate" type="button" value="Update"></input>
		</div>
	</div>
</div>

<script type="text/javascript">
var setting_url = "<?php echo Util::convertLink("Setting") ; ?>" ;
<?php include (PATH_CODE . "js/admin/setting.min.js") ; ?>
</script>