<?php
/**
 * Librairie de parsage XML de fichier ODF
 * Author: Florian Ajir <florian.ajir@adullact.org>
 * Date: 15/11/13
 */

class odfTool{

    /**
     * @var string contenu xml du fichier content.xml
     */
    private $content;

    /**
     * @var DOMDocument arbre xml
     */
    private $dom;

    /**
     * @var array dictionnaire de correspondance des caractères spéciaux
     */
    private $dict = array(
        '_20_' => ' ',
        '_3d_' => '='
    );

    /**
     * Initialisation
     * @param string $file chemin du fichier sur le disque
     */
    public function odfTool($file){
        //Dézipage de l'odf
        $zip = zip_open($file);
        // find entry
        do {
            $entry = zip_read($zip);
        } while ($entry && zip_entry_name($entry) != "content.xml");
        // open entry
        zip_entry_open($zip, $entry, "r");
        // read entry (Chargement du fichier xml/descripteur odf)
        $this->content = zip_entry_read($entry, zip_entry_filesize($entry));
        // close entry
        zip_entry_close($entry);
        // close zip
        zip_close($zip);
        //Initialisation de DOMDocument
        $this->dom = new DOMDocument;
        //Chargement du contenu dans DOMDocument
        $this->dom->loadXML($this->content);
    }

    /**
     * Récupère sous forme d'un tableau toutes les valeurs des attributs
     * @param string $tag nom des tags dans lesquels chercher les valeurs des attributs
     * @param string $attr nom de l'attribut à chercher
     * @return array liste d'attributs
     */
    private function _getAttributes($tag, $attr){
        $elems = array();
        foreach ($this->dom->getElementsByTagNameNS('*', $tag) as $element) {
            if ($element->hasAttribute($attr))
                $elems[] = $element->getAttribute($attr);
        }
        return $elems;
    }

    /**
     * Récupère les noms des styles du document
     * @return array
     */
    public function getStyles(){
        $styles = array();
        $allStyles = $this->_getAttributes('style', 'style:parent-style-name');
        foreach ($allStyles as $style){
            //Caractères spéciaux
            $style = $this->_decodeEntities($style);
            //Si pas encore dans le tableau
            if (!in_array($style, $styles))
                $styles[] = $style;
        }
        return $styles;
    }

    /**
     * Récupère les noms des variables utilisateur du document
     * @return array
     */
    public function getUserFields(){
        return $this->_getAttributes('user-field-get', 'text:name');
    }

    /**
     * Récupère les noms des variables déclarées
     * Attention : celles-ci ne sont pas des variables "user-field"!!
     * @return array
     */
    public function getVariablesDecl(){
        return $this->_getAttributes('variable-decl', 'text:name');
    }

    /**
     * Récupère les noms des sections du document
     * @return array
     */
    public function getSections(){
        return $this->_getAttributes('section', 'text:name');
    }

    /**
     * Récupère les noms des variables utilisateur déclarées dans le document (utilisées ou non)
     * @return array
     */
    public function getUserFieldsDecl(){
        return $this->_getAttributes('user-field-decl', 'text:name');
    }

    /**
     * Récupère les conditions présentes dans le document
     * @return array
     */
    public function getConditions(){
        $conditions = $this->_getAttributes('*', 'text:condition');
        return str_replace('ooow:', '', $conditions);
    }

    /**
     * Le fichier contient-il la variable utilisateur désignée par son nom en argument
     * @param string $var nom de la variable
     * @return bool variable true si présente dans le document, false dans le cas contraire
     */
    public function hasUserField($var){
        $vars = $this->getUserFields();
        return in_array($var,$vars);
    }

    /**
     * Le fichier contient-il la section désignée par son nom en argument
     * @param string $section nom de la variable
     * @return bool section true si présente dans le document, false dans le cas contraire
     */
    public function hasSection($section){
        $sections = $this->getSections();
        return in_array($section,$sections);
    }

    /**
     * Le fichier contient-il la condition désignée en argument
     * @param string $condition nom de la condition
     * @return bool true si présente dans le document, false dans le cas contraire
     */
    public function hasCondition($condition){
        $conditions = $this->getConditions();
        return in_array($condition, $conditions);
    }

    /**
     * Le fichier contient-il le style désignée en argument
     * @param string $style
     * @return bool true si présent dans le document, false dans le cas contraire
     */
    public function hasStyle($style){
        $styles = $this->getStyles();
        return in_array($style, $styles);
    }

    /**
     * Récupère les noms des variables utilisateur déclarées dans le document mais non utilisées
     * @return array
     */
    public function getUserFieldsNotUsed(){
        return array_diff($this->getUserFieldsDecl(), $this->getUserFields());
    }

    /**
     * Retourne le contenu (brut) du fichier XML
     * @return string
     */
    public function getXmlContent(){
        return $this->content;
    }

    /**
     * @param string $string to decode
     * @return string decoded
     */
    private function _decodeEntities($string){
        return str_replace(array_keys($this->dict), array_values($this->dict), $string);
    }

}
