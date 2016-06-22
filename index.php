<?php require_once('Imatge.php'); ?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="lib/lightbox2-master/dist/css/lightbox.min.css">
    </head>
    <body>



        <?php
        /** settings * */
        $images_dir = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_SPECIAL_CHARS);
        if (!$images_dir) {
            $images_dir = 'fotos';
        }

        //Calculem el retorn
        $images_dir_array = explode('/', $images_dir);

        if (sizeof($images_dir_array) == 1) {
            $images_dir_anterior = false;
        } else {
            $images_dir_anterior = '';
            for ($i = 0; $i < sizeof($images_dir_array) - 1; $i++) {
                $images_dir_anterior .= $images_dir_array[$i];

                if ($images_dir_array[count($images_dir_array)-2] !== $images_dir_array[$i]) {
                    $images_dir_anterior .= DIRECTORY_SEPARATOR;
                }
            }
        }

        $images_dir_thumbs = $images_dir . DIRECTORY_SEPARATOR . 'thumbs_esliceu';

        //Si no existeix, creem la carpeta de thumbnails
        if (!file_exists($images_dir_thumbs)) {
            mkdir($images_dir_thumbs, 0777, true);
        }


        $fotos = scandir($images_dir);


        echo '<header>';
        echo '<div class="row">';
        echo '<div class="col-3">';
        echo '<img src="img/LogoLiceu.png" alt="logo">';
        echo '</div>';
        echo '<nav class="col-9">';
        echo '<ul>';
        if ($images_dir_anterior !== FALSE) {
            echo '<li>';
            echo '<a href="index.php?p=' . $images_dir_anterior . '">';
            echo '<img src="img/back.png" alt="folder">';
            echo '<br>';
            echo 'Torna';
            echo '</a>';
            echo '</li>';
        }
        //CARPETES
        foreach ($fotos as $f) {
            if (!is_file($images_dir . DIRECTORY_SEPARATOR . $f) && $f !== '.' && $f !== '..' && $f !== 'thumbs_esliceu') {
                echo '<li>';
                echo '<a href="index.php?p=' . $images_dir . DIRECTORY_SEPARATOR . $f . '">';
                echo '<img src="img/folder2.png" alt="folder">';
                echo '<br>';
                echo $f;
                echo '</a>';
                echo '</li>';
            }
        }
        
        echo '</ul>';
        echo '</nav>';
        echo '</div>';
        echo '</header>';


        echo '<section class="row">';
        echo '<h1 class="col-12 centrat">Fotos</h1>';
        //FOTOS
        foreach ($fotos as $f) {
            //Si és un fitxer...
            if (Imatge::isImatge($images_dir . DIRECTORY_SEPARATOR . $f)) {

                //Si no existeix el thumbnail el creem
                if (!file_exists($images_dir_thumbs . DIRECTORY_SEPARATOR . $f)) {
                    $imatge = new Imatge($images_dir . DIRECTORY_SEPARATOR . $f, mime_content_type($images_dir . DIRECTORY_SEPARATOR . $f));
                    if (!$imatge->isError()) {
                        //Si no existeix el thumbnail
                        $imatge->resizeImage(300, 300, "crop");
                        $imatge->saveImage($images_dir_thumbs . DIRECTORY_SEPARATOR . $f);
                    }
                }

                echo '<div class="col-2 centrat">';
                echo '<a href="' . $images_dir . DIRECTORY_SEPARATOR . $f . '" data-lightbox="infantil">';
                echo '<img src="' . $images_dir_thumbs . DIRECTORY_SEPARATOR . $f . '" alt="' . $f . '" class="imatge">';
                echo '</a>';
                echo '</div>';
            }
        }
        echo '</section>';


        echo '<section class="row">';
        echo '<h1 class="col-12 centrat">Vídeos</h1>';
        //VIDEOS
        foreach ($fotos as $f) {
            if (Imatge::isVideo($images_dir . DIRECTORY_SEPARATOR . $f)) {
                echo '<div class="col-6 centrat">';
                echo '<video controls class="imatge">';
                echo '<source src="' . $images_dir . DIRECTORY_SEPARATOR . $f . '" type="video/mp4">';
                echo 'El teu navegador no suporta vídeos';
                echo '</video>';
                echo '</div>';
            }
        }
        echo '</section>';
        ?>
        <script src="lib/lightbox2-master/dist/js/lightbox-plus-jquery.min.js"></script>
        <script>
            lightbox.option({
                'albumLabel': "Imatge %1 de %2",
                'maxWidth': 800,
                'maxHeight': 600,
                'wrapAround': true
            })
        </script>
    </body>
</html>

