<?php
	$success = false;
	if($_REQUEST['type'] == 'add'){
		$fn = $_REQUEST['n'] ;
		$claim_id = $_REQUEST['i'] ;
		$limitsize = 500 * 1024;	//500kb
		if (!empty($_FILES)) {
			$size = $_FILES[$fn]['size'];
			if($size > 0){
				if ($size < $limitsize) {
					$docfile =  basename($_FILES[$fn]['name']);
					$pi = pathinfo($docfile);
					$ext = $pi['extension'];
					$exts = array("png","jpg","jpeg","doc","docx","pdf") ;
					if (Util::validFileExt($ext,$exts)) {
						$directory = PATH_CLAIMS . 'temp/';
						$newfile = $directory . "claim_doc_" . time() . "_" . $claim_id . "." . $ext;
						if (!file_exists($directory)) {
							mkdir($directory, 1777, true);
						}
						if (move_uploaded_file($_FILES[$fn]['tmp_name'], $newfile)) {
							$success = true ;
							$err = "" ;
						} else {
							$err = "Error in processing uploaded file." ;
						}
					} else {
						$err = "Unsupported upload file type." ;
					}
				} else {
					$err = "Upload file over limit. Upload file size must be less than 500KB." ;
				}
			} else {
				$err = "Missing upload file.";
			}
		} else {
			$err = "No upload file found." ;
		}
	} else if ($_REQUEST['type'] == 'del'){
		$temp = $_REQUEST['claim_id'] != -1 ? $_REQUEST['claim_id'] : 'temp';
		if(unlink(PATH_CLAIMS . $temp . '/' . $_REQUEST['n'])){
			$success = true;
		} else {
		}
	}
	if($success){
		if($_REQUEST['type'] == 'add'){
			echo Status::Ok . "|" . $_REQUEST['type'] . "|" . basename($newfile);
		} else if($_REQUEST['type'] == 'del'){
			echo Status::Ok . "|" . $_REQUEST['type'] . "|" . $_REQUEST['i'];
		}
	}else{
		echo Status::Error . "|" . $_REQUEST['type'] . "|" . $err ;
	}
	
?>