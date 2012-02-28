<?php

    class ProgressComponent extends Object {
    
        function start ($gauche,$haut,$largeur,$bord_col,$txt_col) {
            $tailletxt=30-10;
            echo '<div id="contTemp" style="position:absolute;top:0;left:0px;';
            echo 'background-image:url(../../../img/wavedelib.png);';
            echo 'width:1000px; height:250px; margin-left: 0px; padding: 0px;">';

            echo '<div id="pourcentage" style="position:absolute;top:'.($haut +1 );
            echo ';left: '.($gauche + $largeur ).'px';
            echo ';width:'.$largeur.'px';
            echo ';height: 22px;border:0px solid '.$bord_col.';font-family:Tahoma;font-weight:bold';
            echo ';font-size:'.$tailletxt.'px;color:'.$txt_col.';z-index:1;text-align:center;">0%</div>';

            echo '<div id="progrbar" style="position:absolute;top:'.($haut+1); //+1
            echo ';left:'.($gauche+1); //+1
            echo ';width:0px';
            echo ';height: 22px';
            echo ';background-image:url(../../../img/pbar-ani.gif) ;z-index:0;"></div>';

//            echo ';background-color:'.$bg_col.';z-index:0;"></div>';

            echo '<div id="affiche" style="position:absolute;top:'.($haut+22+15);
            echo ';left:'.($gauche+1);
            echo ';width:'.($largeur*2).'px;';
            echo 'height: 30px; font-size: 10px;';
            echo 'z-index:0;"></div>';
            echo '</div>';
        }
         
        function at ($indice, $affiche) {
            echo "<script>";
             // affiche l'avancement en %
            echo "document.getElementById(\"pourcentage\").innerHTML='".round($indice)."%';";
             // affiche le message sous la barre de progression ($affiche)
            echo "document.getElementById(\"affiche\").innerHTML='".$affiche."';";
             // La barre elle-meme
            echo "document.getElementById('progrbar').style.width=".($indice*2).";";
            echo "</script>";
            flush();
        }
 
        function end($redirect) {
            echo ('<script>');
            echo ('    document.getElementById("pourcentage").style.display="none"; ');
            echo ('    document.getElementById("progrbar").style.display="none";');
            echo ('    document.getElementById("affiche").style.display="none";');
            echo ('    document.getElementById("contTemp").style.display="none";');
            echo ('</script>');
            echo '<script type="text/javascript">';
            echo "window.location = \"$redirect\"";
            echo '</script>';
        }

        function endPopup($url) {
            echo '<script type="text/javascript">';
            echo ("window.open('".$url."'); ");
            echo ('</script>');
        }

    }
?>
