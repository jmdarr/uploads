<?php

$limitSize = 52428800; //50mb
$uploadDir = "uploads/";
$downloadMethod = $_POST['download'];

if($downloadMethod == "local" || $downloadMethod == "remote") {
	if($_POST['download'] == "local") {
		$fileSize = $_FILES['file']['size'];
		$fileName = $_FILES['file']['name'];
		$fileName = str_replace(" ", "_", $fileName);
		$fileTmp = $_FILES['file']['tmp_name'];
	}
	else if($_POST['download'] == "remote") {
		$fileURL = $_POST['url'];
		$pathArray = explode('/', $fileURL);
		$numElements = count($pathArray);
		$fileName = $pathArray[$numElements - 1];
		$fileContents = file_get_contents($fileURL) or die("Could not get URL");
	
		if($fileContents != false) {
			$fileTmp = "$uploadDir"."file.tmp";
			$fh = fopen($fileTmp, "w") or die("Cannot open file $fileTmp for writing");
			fwrite($fh, $fileContents) or dir("Cannot write to $fileTmp");
			$fileSize = filesize($fileTmp);
		}
	}
	
	if($fileSize <= $limitSize) {
	        rename($fileTmp, $uploadDir.$fileName) or die("$fileTmp // $uploadDir$fileName");
	        chmod($uploadDir.$fileName, 0644);
	}
	else {
		unlink($fileTmp);
		die("Your file's too large, you're going to rip me in two!");
	}
}
else {
	die("Temporarily inactive during development. <a href='upload.html'>Go back</a>.\n\n".print_r($_POST));
}

header("Location: http://www.solarsquid.com/upload.html");

?>
