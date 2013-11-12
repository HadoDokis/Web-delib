<?php
require_once('base.html');
require_once('verification.php');
?>
<div class="container">
<span id="top"></span>
<!-- Main hero unit for a primary marketing message or call to action -->
<div class="hero-unit">
    <h2>Webdelib check</h2>

    <p>Vérification de l'environement Webdelib.</p>

    <p>
        <a class="btn btn-primary btn-large" href="index.php">
            Relancer la vérification <i class="icon-white icon-repeat"></i>
        </a>
    </p>
</div>

<div class="row">
    <div class="span12">
        <div class="well well-small">
            <p><i class="icon-ok"></i> Versions</p>
            <?php verifVersions(); ?>
        </div>
    </div>
</div>
<br/>


<div class="row">
    <div class="span12">
        <div class="well well-small">
            <p><i class="icon-user"></i> Permissions</p>
            <?php d('Propriétaire du script courant : ' . get_current_user(), 'info'); ?>
            <?php d('Répertoire d\'installation de l\'application : ' . $appli_path, 'info'); ?>
        </div>
    </div>
</div>
<br/>

<div class="row">
    <div class="span12">
        <div class="well well-small">
            <p><i class="icon-th-large"></i> Modules apache</p>
            <?php apache_check_modules($mods_apache); ?>
        </div>
    </div>
</div>
<br/>

<div class="row">
    <div class="span12">
        <div class="well well-small">
            <p><i class="icon-th"></i> CakePHP</p>
            <?php verifConsoleCakePhp(); ?>
        </div>
    </div>
</div>
<br/>

<div class="row">
    <div class="span12">
        <div class="well well-small">
            <p><i class="icon-th-list"></i> Extensions PHP</p>
            <?php php_check_extensions($exts_php); ?>
        </div>
    </div>
</div>
<br/>

<div class="row">
    <div class="span12">
        <div class="well well-small">
            <p><i class="icon-code"></i> Librairies PHP</p>
            <?php php_check_librairies($libs_php); ?>
        </div>
    </div>
</div>
<br/>

<div class="row">
    <div class="span12">
        <div class="well well-small">
            <p><i class="icon-cog"></i> Programmes</p>
            <?php check_binaires($binaires); ?>
        </div>
    </div>
</div>
<br/>

<div class="row">
    <div class="span12">
        <div class="well well-small">
            <p><i class="icon-file"></i> Fichiers de configuration </p>
            <?php verifPresenceFichierIni(); ?>
        </div>
    </div>
</div>
<br/>

<div class="row">
    <div class="span12">
        <div class="well well-small">
            <p><i class="icon-file"></i> Modeles ODT</p>
            <?php verifPresenceModelesOdt(); ?>
        </div>
    </div>
</div>
<br/>

<div class="row">
    <div class="span12">
        <div class="well well-small">
            <p><i class="icon-tasks"></i> Base de données </p>
            <?php infoDataBase(); ?>
            <?php checkSchema(); ?>
        </div>
    </div>
</div>

<a name="smtp"> &nbsp;</a> <br/>

<div class="row">
    <div class="span12">
        <div class="well well-small"><p><i class="icon-envelope"></i> Mails</p>
            <?php infoMails(); ?>

        </div>
    </div>
</div>

<a name="génération des documents">&nbsp; </a>

<div class="row">
    <div class="span12">
        <div class="well well-small">
            <p><i class="icon-resize-small"></i> Outil de convertion</p> </a>
            <?php verifConversion(); ?>
        </div>
    </div>
</div>
<br/>

<div class="row">
    <div class="span12">
        <div class="well well-small">
            <i class="icon-edit"></i><a href="#top" rel="popover" title="GedOoo"
                                        data-content="GedOoo est un outl de fusion de documents PDF"> GedOoo</a></p>
            <?php testerOdfGedooo(); ?>
        </div>
    </div>
</div>
<?php
$use_s2low = Configure::read('USE_S2LOW');
if ($use_s2low) {
    ?>
    <a name="s2low"> <br/> </a> <br/>
    <div class="row">
        <div class="span12">
            <div class="well well-small">
                <i class="icon-edit"></i><a href="#top" rel="popover" title="S2LOW"
                                            data-content="Utilisation du Tdt S2LOW">S2LOW</a></p>
                <?php getClassification(); ?>
            </div>
        </div>
    </div>
    <br/>
<?php
}

$use_parapheur = Configure::read('USE_PARAPH');
if ($use_parapheur) {
    ?>

    <a name="iparapheur"> <br/> </a> <br/>
    <div class="row">
        <div class="span12">
            <div class="well well-small">
                <i class="icon-edit"></i><a href="#top" rel="popover" title="I-Parapheur"
                                            data-content="Utilisation du i-Parapheur électronique">i-Parapheur</a></p>
                <?php getCircuitsParapheur(); ?>
            </div>
        </div>
    </div>
    <br/>
<?php
}

$use_asalae = Configure::read('USE_ASALAE');
if ($use_asalae) {
    ?>

    <a name="as@lae"> <br/> </a> <br/>
    <div class="row">
        <div class="span12">
            <div class="well well-small">
                <i class="icon-edit"></i><a href="#top" rel="popover" title="ASALAE"
                                            data-content="Utilisation de ASALAE">ASALAE</a></p>
                <?php getVersionAsalae(); ?>
            </div>
        </div>
    </div>
    <br/>
<?php
}

$use_pastell = Configure::read('USE_PASTELL');
if ($use_pastell) {
?>
    <a name="pastell"> <br/> </a> <br/>
    <div class="row">
        <div class="span12">
            <div class="well well-small">
                <i class="icon-edit"></i><a href="#top" rel="popover" title="ASALAE"
                                            data-content="Utilisation de Pastell">PASTELL</a></p>
                <?php getPastellVersion(); ?>
            </div>
        </div>
    </div>
    <br/>
<?php
}
$useGED = Configure::read("USE_GED");
if ($useGED) {
    ?>
    <div class="row">
        <div class="span12">
            <div class="well well-small">
                <p><i class="icon-book"></i> Connecteur CMIS GED</p>
                <?php checkGED(); ?>
            </div>
        </div>
    </div>
    <br/>
<?php
}
$useOpenLdap = Configure::read("USE_OPENLDAP");
$useAD = Configure::read("USE_AD");
if ($useOpenLdap || $useAD) {
?>
    <div class="row">
        <div class="span12">
            <div class="well well-small">
                <p><i class="icon-group"></i>
                    Connecteur <?php if ($useOpenLdap) echo "openLDAP"; else echo "Active Directory"; ?></p>
                <?php checkLdap(); ?>
            </div>
        </div>
    </div>
    <br/>
<?php
}
?>
</div><!--row-->

</div><!--container-->
<?php
require_once('footer.html');
?>
