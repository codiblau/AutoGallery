<?php

$tempPath = filter_input(INPUT_POST, 'tempPath', FILTER_SANITIZE_SPECIAL_CHARS);
$fotos = filter_input(INPUT_POST, 'fotos', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);

//$tempPath = filter_input(INPUT_GET, 'tempPath', FILTER_SANITIZE_SPECIAL_CHARS);
//$fotos = filter_input(INPUT_GET, 'fotos', FILTER_SANITIZE_SPECIAL_CHARS);
//$fotos = json_decode($fotos);
//echo 'fotos';
//print_r($fotos);

//ZIP
$zip = new ZipArchive();
$filenamezip = $tempPath . "fotos_videos_infantil.zip";

if ($zip->open($filenamezip, ZipArchive::CREATE) !== TRUE) {
    exit("cannot open <$filenamezip>\n");
}

foreach ($fotos as $foto) {
    echo $foto.'<br>';
    $zip->addFile($foto, basename($foto));
}

$zip->close();


//DOWNLOAD
$file_name = basename($filenamezip);

ob_clean();

header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=$file_name");
header("Content-Length: " . filesize($filenamezip));

/* afegit per poder baixar per AJAX */
//ob_clean();
//flush();
/* ----- */
//ob_end_clean();
//ob_end_flush();

readfile($filenamezip);
exit;

