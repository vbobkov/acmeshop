<?php
// print_r($_GET);
// print_r($_POST);
$content = $_POST['file_content'];
// $content = '@C:\sites\acmedb\public_html\assets/uploads/magmi_import.csv';
print_r($content);
print_r('wtf');
return;
$file_handle = fopen($filename, 'w');
if(is_array($content) && sizeof($content) > 0) {
	fwrite($file_handle, $prepend);
	fwrite($file_handle, implode($delimiter, array_keys(reset($content))) . "\n");
	foreach($content as $row) {
		fwrite($file_handle, implode($delimiter, array_values($row)) . "\n");
	}
}
fclose($file_handle);
?>