<?php

    class ProgressComponent extends Component {
        
        function start ($gauche,$haut,$largeur,$bord_col,$txt_col) {
            // Pour utilisation de HtmlHelper
            App::import('Helper', 'Html');
            App::uses('View', 'View');
            $this->View = new View($this->Controller);
            $html = new HtmlHelper($this->View);
		
            $tailletxt=30-10;
            echo $html->script('jquery');
            echo $html->css('progressbar');
            echo '<div id="contTemp">';
            echo $html->image('webdelib_petit.png', array('alt' => 'Webdelib'));
            echo '<div id="pourcentage" style="top:'.($haut +1 );
            echo ';left: '.($gauche + $largeur ).'px';
            echo ';width:'.$largeur.'px';
            echo ';border:0px solid '.$bord_col.'';
            echo ';font-size:'.$tailletxt.'px;color:'.$txt_col.';">0%</div>';
            echo '<div id="progrbar" style="top:'.($haut+1); //+1
            echo ';left:'.($gauche+1).';"></div>';//+1
            echo '<div id="affiche" style="top:'.($haut+22+15);
            echo ';left:'.($gauche+1);
            echo ';width:'.($largeur*2).'px;"></div>';
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
