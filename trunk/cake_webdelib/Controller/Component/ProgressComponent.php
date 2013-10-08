<?php

    class ProgressComponent extends Component {
        
        function start ($gauche,$haut,$largeur,$bord_col,$txt_col) {
            $tailletxt=30-10;
            echo '<script type="text/javascript" src="/theme/Bootstrap/js/libs/jquery.js"></script>';
            echo '<div id="contTemp" style="position:absolute;top:0;left:0px;';
            echo 'background-image:url(/theme/Bootstrap/img/grid-18px-masked.png);';
            echo 'width:100%; height:250px; margin-left: 0px; padding: 0px;">';
            echo '<img src="/img/webdelib_petit.png" />';

            echo '<div id="pourcentage" style="position:absolute;top:'.($haut +1 );
            echo ';left: '.($gauche + $largeur ).'px';
            echo ';width:'.$largeur.'px';
            echo ';height: 22px;border:0px solid '.$bord_col.';font-family:Tahoma;font-weight:bold';
            echo ';font-size:'.$tailletxt.'px;color:'.$txt_col.';z-index:1;text-align:center;">0%</div>';

            echo '<div id="progrbar" style="position:absolute;top:'.($haut+1); //+1
            echo ';left:'.($gauche+1); //+1
            echo ';width:0px';
            echo ';height: 22px';
            echo ';background-image:url(/img/pbar-ani.gif) ;z-index:0;"></div>';

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
            echo "$('#pourcentage').html('".round($indice)."%');";
             // affiche le message sous la barre de progression ($affiche)
            echo "$('#affiche').html('".$affiche."');";
             // La barre elle-meme
            echo "$('#progrbar').css('width','".($indice*2)."px');";
            echo "</script>";
            flush();
        }
 
        function end($redirect) {
            echo '<script>';
            echo '$("#pourcentage").hide();';
            echo '$("#progrbar").hide();';
            echo '$("#affiche").hide();';
            echo '$("#contTemp").hide();';
            echo '</script>';
            echo '<script type="text/javascript">';
            echo "window.location = \"$redirect\"";
            echo '</script>';
        }

        function endPopup($url) {
            echo '<script type="text/javascript">';
            echo "window.open('".$url."');";
            echo '</script>';
        }

    }
?>
