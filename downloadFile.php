<?php header("Content-type: text/html; charset=utf-8"); ?>
<?php

$path = filter_input(INPUT_GET, "path", FILTER_SANITIZE_SPECIAL_CHARS);

$patharray = explode("/",$path);
$filename = end($patharray);

$size = filesize($path);
$mimetype = mime_content_type($path);

ob_clean();
header("Content-length: $size");
header("Content-type: $mimetype");
header("Content-Disposition: attachment; filename=".$filename);
readfile($path);

exit;

?>