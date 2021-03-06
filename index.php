<?php require_once('Imatge.php'); ?>
<?php require_once('UtilServei.php'); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="lib/lightbox2-master/dist/css/lightbox.min.css">
        <style>
            .lb-data .lb-number{
                color: white;
                font-size: 14pt;
            }
        </style>
    </head>
    <body>



        <?php
        /** settings * */
        define("TMP_PATH", "/tmp/");

        /** params * */
        $images_dir = filter_input(INPUT_GET, 'p',FILTER_DEFAULT);
        if (!$images_dir) {
            $images_dir = 'content';
        }

        /** Seguretat - No poder accedir a directoris superiors o absoluts, només relatiu s */
        if (UtilServei::startsWith($images_dir, '..') || UtilServei::startsWith($images_dir, '/')) {
            exit();
        }

        //Calculem el retorn

        $images_dir_array = explode('/', $images_dir);

        //Si només té un element vol dir que ja és a l'arrel
        if (sizeof($images_dir_array) == 1) {
            $images_dir_anterior = false;
        } else {
            $images_dir_anterior = '';
            for ($i = 0; $i < sizeof($images_dir_array) - 1; $i++) {
                $images_dir_anterior .= $images_dir_array[$i];

                if ($images_dir_array[count($images_dir_array) - 2] !== $images_dir_array[$i]) {
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

        //LOGO
        echo '<div class="col-12 centrat">';
        echo '<img src="img/LogoLiceu.png" alt="logo">';
        echo '</div>';

        //MENÚ PRINCIPAL
        echo '<nav class="col-12">';
        echo '<ul>';
        if ($images_dir_anterior !== FALSE) {
            echo '<li class="col-2">';
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
                echo '<li class="col-2">';
                echo '<a href="index.php?p=' . $images_dir . DIRECTORY_SEPARATOR . $f . '">';
                echo '<img src="img/folder2.png" alt="folder">';
                echo '<br>';
                echo $f;
                echo '</a>';
                echo '</li>';
            }
        }

//        echo '<li id="list_selecciona">';
//        echo '<a onclick="selecciona();">';
//        echo '<img src="img/desa.png" alt="save">';
//        echo '<br>';
//        echo 'Descarregar';
//        echo '</a>';
//        echo '</li>';

        echo '</ul>';
        echo '</nav>';
        echo '</div>';
        echo '</header>';


        echo '<main class="col-12">';
        echo '<section class="row">';
        
        //FOTOS
        $comptadorFotos = 0;
        foreach ($fotos as $f) {
            //Si és un fitxer...
            if (!UtilServei::startsWith($f, '.') && Imatge::isImatge($images_dir . DIRECTORY_SEPARATOR . $f)) {

                //Si no existeix el thumbnail el creem
                if (!file_exists($images_dir_thumbs . DIRECTORY_SEPARATOR . $f)) {
                    $imatge = new Imatge($images_dir . DIRECTORY_SEPARATOR . $f, mime_content_type($images_dir . DIRECTORY_SEPARATOR . $f));
                    if (!$imatge->isError()) {
                        //Si no existeix el thumbnail
                        $imatge->resizeImage(300, 300, "crop");
                        $imatge->saveImage($images_dir_thumbs . DIRECTORY_SEPARATOR . $f);
                    }
                }
                
                if($comptadorFotos == 0){
                    echo '<h1 class="col-12 centrat">Fotos</h1>';
                }

                echo '<div class="col-2 centrat foto">';
                echo '<a href="' . $images_dir . DIRECTORY_SEPARATOR . $f . '" data-lightbox="infantil">';
                echo '<img src="' . $images_dir_thumbs . DIRECTORY_SEPARATOR . $f . '" alt="' . $f . '" class="imatge">';
                echo '</a>';
                echo '<div class="selecciona" style="display:none;" onclick="selectitem(\'esliceu_' . sha1($images_dir_thumbs . DIRECTORY_SEPARATOR . $f) . '\')">';
                echo '<input type="checkbox" id="esliceu_' . sha1($images_dir_thumbs . DIRECTORY_SEPARATOR . $f) . '" value="' . $images_dir . DIRECTORY_SEPARATOR . $f . '" class="checkbox"> <label for="esliceu_' . sha1($images_dir_thumbs . DIRECTORY_SEPARATOR . $f) . '">Selecciona</label>';
                echo '</div>';
                echo '<a href="' . $images_dir . DIRECTORY_SEPARATOR . $f . '" class="centrat" target="_blank">Descarrega foto</a>';
                echo '</div>';

                $comptadorFotos++;
            }
        }
        
        echo '</section>';


        echo '<section class="row">';
        
        //VIDEOS
        $comptadorVideos = 0;
        foreach ($fotos as $f) {
            if (!UtilServei::startsWith($f, '.') && Imatge::isVideo($images_dir . DIRECTORY_SEPARATOR . $f)) {
                
                if($comptadorVideos == 0){
                    echo '<h1 class="col-12 centrat">Vídeos</h1>';
                }
                
                echo '<div class="col-6 centrat video">';
                if (1==2 && strtolower(UtilServei::endsWith($f, '.mp4'))) {
                    echo '<video controls class="imatge">';
                    echo '<source src="' . $images_dir . DIRECTORY_SEPARATOR . $f . '" type="video/mp4">';
                    echo 'El teu navegador no suporta vídeos';
                    echo '</video>';
                } else {
                    echo '<a href="' . $images_dir . DIRECTORY_SEPARATOR . $f . '" class="centrat" target="_blank"><img src="img/download.png" alt="video"></a>';
                }
                echo '<br>';
                //echo '<a onclick="downloadVideo(\''.$images_dir . DIRECTORY_SEPARATOR . $f.'\');" class="centrat">Descarrega vídeo</a>';
                echo '<a href="' . $images_dir . DIRECTORY_SEPARATOR . $f . '" class="centrat" target="_blank">Descarrega vídeo ('.$f.')</a>';
                echo '</div>';

                $comptadorVideos++;
            }
        }
        echo '</section>';
        echo '</main>';

        /* Aside */
        echo '<aside class="col-3" style="display:none;">';
        echo '<h1 class="centrat"><span id="selecteditems">0</span> ítems</h1>';
        echo '<div id="items_download"></div>';
        echo '<form method="POST" action="download.php">';
        echo '<input type="hidden" value="' . TMP_PATH . '" name="tempPath">';
        echo '<input type="hidden" value="" name="fotos" id="formfotos">';
        echo '<input type="submit" value="Descarrega fotos i vídeos" >';
        echo '</form>';
        echo '</aside>';
        ?>
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script src="lib/lightbox2-master/dist/js/lightbox.min.js"></script>
        <script type="text/javascript">
            lightbox.option({
                'albumLabel': "Imatge %1 de %2",
                'maxWidth': 800,
                'maxHeight': 600,
                'wrapAround': true,
                'resizeDuration': 100,
                'fadeDuration': 100
            });

            function selecciona() {
                $(".selecciona").toggle();

                $("main").toggleClass('col-12').toggleClass('col-9');
                $("aside").toggle();

                //Desseleecionem els checkbox
                $('.checkbox').prop('checked', false); // Unchecks it

                var storeFotos = localStorage.getItem("fotos");

                if (storeFotos) {
                    var arrayFotos = JSON.parse(storeFotos);

                    var checkboxes = $("input:checkbox");

                    for (var i = 0; i < checkboxes.length; i++) {
                        for (var j = 0; j < arrayFotos.length; j++) {
                            if ((checkboxes[i]).value === arrayFotos[j]) {
                                $('#' + checkboxes[i].id).prop('checked', true);
                            }
                        }
                    }


                    //LLista de descàrrega
                    var seleccionats = 0;
                    var llistaDescarrega = '<ul>';
                    for (var j = 0; j < arrayFotos.length; j++) {
                        seleccionats++;

                        llistaDescarrega += '<li>';
                        llistaDescarrega += '<img src="' + arrayFotos[j] + '" alt="foto" class="col-3">';
                        llistaDescarrega += '<span class="col-9">' + arrayFotos[j] + '</span>';
                        llistaDescarrega += '</li>';
                    }
                    llistaDescarrega += '</ul>';

                    $("#selecteditems").html(seleccionats);

                    $("#items_download").html(llistaDescarrega);
                    $("#formfotos").val(storeFotos);
                }
            }

            function selectitem(item) {
                var storeFotos = localStorage.getItem("fotos");

                if (storeFotos === 'undefined' || storeFotos === null) {
                    localStorage.setItem("fotos", JSON.stringify([]));
                    storeFotos = localStorage.getItem("fotos");
                }

                var it = $("#" + item);

                //Mirem si és una inserció o una eliminació
                if (it.prop('checked') === true) {
                    //INSERIM ITEM
                    var arrayFotos = JSON.parse(storeFotos);
                    arrayFotos.push(it.val());
                    localStorage.setItem("fotos", JSON.stringify(arrayFotos));

                    $("#selecteditems").html(arrayFotos.length);
                    $("#formfotos").val(storeFotos);
                } else {
                    //ESBORREM ITEM
                    var arrayFotos = JSON.parse(storeFotos);
                    var removeItem = it.val();

                    arrayFotos = $.grep(arrayFotos, function (value) {
                        return value !== removeItem;
                    });

                    localStorage.setItem("fotos", JSON.stringify(arrayFotos));

                    $("#selecteditems").html(arrayFotos.length);
                    $("#formfotos").val(storeFotos);
                }



                //LLista de descàrrega
                var arrayFotos = JSON.parse(storeFotos);
                var llistaDescarrega = '<ul>';
                for (var j = 0; j < arrayFotos.length; j++) {
                    llistaDescarrega += '<li>';
                    llistaDescarrega += '<img src="' + arrayFotos[j] + '" alt="foto" class="col-3">';
                    llistaDescarrega += '<span class="col-9">' + arrayFotos[j] + '</span>';
                    llistaDescarrega += '</li>';
                }
                llistaDescarrega += '</ul>';
                $("#items_download").html(llistaDescarrega);
            }

            function downloadFile(path) {
                window.location = 'downloadFile.php?path=' + path;
            }
        </script>
    </body>
</html>

