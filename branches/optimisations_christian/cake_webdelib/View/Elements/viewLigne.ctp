<?php

/*
  Affiche une ligne dans les vues détaillées view contenant de 1 à 3 éléments max
  Paramètres :
  array $ligne : array(iElement=>array('libelle'=>, 'valeur'=>))
  boolean $altrow : (défaut = false)
  string $valeurDefaut : (défaut = '-')
 */
// Initialisations
$altrow = isset($altrow) ? $altrow : false;
$valeurDefaut = isset($valeurDefaut) ? $valeurDefaut : '-';

// Nombre d'éléments de la ligne
$nbElements = count($ligne);

// Nombre d'éléments par ligne max = 3
if ($nbElements == 0 || $nbElements > 3)
    return;

// Affichage des éléments de la ligne
foreach ($ligne as $element) {
    if ($nbElements == 2)
        echo "<div class='demi'>";
    elseif ($nbElements == 3)
        echo "<div class='tiers'>";
    echo '<dt' . ($altrow ? " class='altrow'" : "") . '>' . $element['libelle'] . '</dt>';

    // affichage de dd
    if (!array_key_exists('ddClasse', $element))
        $element['ddClasse'] = '';
    $classe = ($altrow ? 'altrow' : '') . ($altrow && !empty($element['ddClasse']) ? ' ' : '') . $element['ddClasse'];
    if (empty($element['valeur']))
        $valeurAffichee = $valeurDefaut;
    else
        $valeurAffichee = $element['valeur'];
    echo '<dd' . (empty($classe) ? '' : ' class=\'' . $classe . '\'') . '>';
    echo $this->Html->tag('div', $valeurAffichee);
    echo '</dd>';
    if ($nbElements > 1)
        echo "</div>";
}
if ($nbElements > 1)
    echo "<div class='spacer'></div>"
?>
