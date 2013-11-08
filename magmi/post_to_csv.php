<?php
print_r($_POST['file_content']);
file_put_contents('state/magmi_import.csv', $_POST['file_content']);
return;
?>