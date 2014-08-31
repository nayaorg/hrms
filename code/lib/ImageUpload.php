<?php
	$fn = $_REQUEST['n'] ;
	$imgUploaded = false ;
	$limitsize = 50 * 1024;	//50k
	if (!empty($_FILES)) {
		$size = $_FILES[$fn]['size'];
		if($size > 0){
			if ($size < $limitsize) {
				$imgfile =  basename($_FILES[$fn]['name']);
				$pi = pathinfo($imgfile);
				$ext = $pi['extension'];
				$exts = array("png","jpg","jpeg") ;
				if (Util::validFileExt($ext,$exts)) {
					$newfile = PATH_PICTURE . "/" . $_SESSION[SE_ORGID] . "/logo_" . time() . "." . $ext; 
					Util::removeLogoFile(PATH_PICTURE, $_SESSION[SE_ORGID]) ;
					if (move_uploaded_file($_FILES[$fn]['tmp_name'], $newfile)) {
						$imgUploaded = true ;
						//$tmpfile = PATH_PICTURE . "/" . $_SESSION[SE_ORGID] . "/" . basename($_FILES[$fn]['tmp_name']) ;
						//copy($newfile,$tmpfile) ;
						$err = "" ;
					} else {
						$err = "Error in processing uploaded file." ;
					}
				} else {
					$err = "Unsupported upload file type." ;
				}
			} else {
				$err = "Upload file over limit. Upload file size must be less than 50KB." ;
			}
		} else {
			$err = "Missing upload file.";
		}
	} else {
		$err = "No upload file found." ;
	}
	if($imgUploaded){
		echo Status::Ok . "|" . "picture/" . $_SESSION[SE_ORGID] . "/" . basename($newfile);
		//echo Status::Ok . "|" . $newfile ;
	}else{
		echo Status::Error . "|" . $err ;
	}
	
?>