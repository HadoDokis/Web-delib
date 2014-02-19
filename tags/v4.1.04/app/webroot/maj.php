<?php

    @require_once('../config/core.php');
    echo ("<h2>Bienvenue dans la procédure de mise à jour</h2>");
    echo ("Vous êtes actuellement en version <i><u>".VERSION ."</i></u> de Webdelib");
    
    $pos =  strrpos ( getcwd(), 'app');
    $webdelib_path          = substr(getcwd(), 0, $pos);
    echo ("<h3>Répertoire d'installation de Webdelib <i><u>". $webdelib_path."</i></u></h3><br/>");
    
    echo ("Création d'une sauvegarde :");
    $app_path      =  trim($webdelib_path."app/");
    $back_app_path =  trim($webdelib_path."app_".time());
    $commande = "cp -R $app_path $back_app_path";
    system ($commande, $return);
 
    if (is_dir($back_app_path))
        echo ("OK! $back_app_path <br>");
    else
        die("KO!<br>");

    echo ("Récupération de l'archive :");
    getFile("http://webdelib.adullact.net/app.zip", $webdelib_path."app.zip");
    if (file_exists($webdelib_path."app.zip"))
        echo ("OK!". $webdelib_path."app.zip <br>");
    else
        die("KO!<br>");






function getFile($url, $filename) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);

        if (file_exists($filename))
            unlink($filename);

        $fp = fopen($filename, 'w+');
        fwrite($fp, $content);
        fclose($fp);
}



?>
