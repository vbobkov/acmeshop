<?php
print_r($_POST['file_content']);
file_put_content('state/magmi_import.csv', $_POST['file_content']);
return;
?>