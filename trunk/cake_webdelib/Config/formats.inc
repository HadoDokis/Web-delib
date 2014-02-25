<?php
/**
 * Règles de validatotion des formats des fichiers envoyés
 * @see http://www.nationalarchives.gov.uk/PRONOM/Default.aspx
 */
Configure::write('DOC_TYPE', array(
    'application/pdf' => array(
        'formatname' => 'Acrobat PDF',
        'extension' => 'pdf',
        'joindre_fusion' => true,
        'joindre_ctrl_legalite' => true,
        'puid' => array(
            'fmt/17' => array(
                'version' => '1.3',
                'actif' => true
            ),
            'fmt/18' => array(
                'version' => '1.4',
                'actif' => true
            ),
            'fmt/19' => array(
                'version' => '1.5',
                'actif' => true
            ),
            'fmt/20' => array(
                'version' => '1.6',
                'actif' => true
            ),
            'fmt/276' => array(
                'version' => '1.7',
                'actif' => true
            ),
            'fmt/95' => array(
                'version' => '1a',
                'actif' => true
            )
        )
    ),
    'application/vnd.oasis.opendocument.text' => array(
        'formatname' => 'OpenDocument Text',
        'extension' => 'odt',
        'joindre_fusion' => true,
        'joindre_ctrl_legalite' => true,
        'puid' => array(
            'fmt/136' => array(
                'actif' => false,
                'version' => '1.0'
            ),
            'fmt/290' => array(
                'actif' => true,
                'version' => '1.1'
            ),
            'fmt/291' => array(
                'actif' => true,
                'version' => '1.2'
            )
        )
    ),
    'application/vnd.oasis.opendocument.spreadsheet' => array(
        'formatname' => 'OpenDocument Spreadsheet',
        'extension' => 'ods',
        'puid' => array(
            'fmt/137' => array(
                'actif' => false,
                'version' => '1.0'
            ),
            'fmt/294' => array(
                'actif' => true,
                'version' => '1.1'
            ),
            'fmt/295' => array(
                'actif' => true,
                'version' => '1.2'
            )
        ),
    ),
    'application/msword' => array(
        'formatname' => 'Microsoft Word for Windows',
        'extension' => 'doc',
        'joindre_fusion' => true,
        'joindre_ctrl_legalite' => true,
        'puid' => array(
            'fmt/39' => array(
                'actif' => true,
                'version' => '97-2003'
            ),
            'fmt/40' => array(
                'actif' => true,
                'version' => '6.0/95'
            ),
            'fmt/609' => array(
                'actif' => true,
                'version' => '6.0-2003'
            ),
        ),
    ),
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => array(
        'formatname' => 'Microsoft Word for Windows',
        'extension' => 'docx',
        'joindre_fusion' => true,
        'joindre_ctrl_legalite' => true,
        'puid' => array(
            'fmt/412' => array(
                'actif' => true
            ),
        ),
    ),
    'application/zip' => array(
        'formatname' => 'ZIP Format',
        'extension' => 'zip',
        'puid' => array(
            'x-fmt/263' => array(
                'actif' => false,
                'version' => '1.0'
            ),
        ),
    ),
    'image/png' => array(
        'formatname' => 'Portable Network Graphics',
        'puid' => array(
            'fmt/11' => array(
                'actif' => false,
                'version' => '1.0'
            ),
            'fmt/12' => array(
                'actif' => true,
                'version' => '1.1'
            ),
            'fmt/13' => array(
                'actif' => true,
                'version' => '1.2'
            )
        ),
        'joindre_ctrl_legalite' => true,
        'extension' => 'png',
    ),
    'image/jpeg' => array(
        'formatname' => 'Image JPEG',
        'joindre_ctrl_legalite' => true,
        'extension' => array('jpeg', 'jpg'),
        'puid' => array(
            'fmt/44' => array(
                'actif' => true,
                'version' => '1.02'
            ),
            'fmt/43' => array(
                'actif' => true,
                'version' => '1.01'
            ),
            'fmt/42' => array(
                'actif' => true,
                'version' => '1.00'
            ),
            'fmt/41' => array(
                'actif' => true,
            ),
            'x-fmt/398' => array(
                'actif' => true,
                'version' => '2.0'
            ),
            'x-fmt/391' => array(
                'actif' => true,
                'version' => '2.1'
            ),
            'x-fmt/390' => array(
                'actif' => true,
                'version' => '2.2'
            )
        )
    )
));