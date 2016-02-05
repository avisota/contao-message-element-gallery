<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message-element-gallery
 * @license    LGPL-3.0+
 * @filesource
 */

/**
 * Table orm_avisota_message_content
 * Entity Avisota\Contao:MessageContent
 */
$GLOBALS['TL_DCA']['orm_avisota_message_content']['metapalettes']['gallery'] = array
(
    'type'      => array('cell', 'type', 'headline'),
    'source'    => array('imageSources', 'orderSRC',),
    'image'     => array('imageSize', 'imageMargin', 'perRow', 'sortBy'),
    'expert'    => array(':hide', 'cssID', 'space'),
    'published' => array('invisible'),
);

$GLOBALS['TL_DCA']['orm_avisota_message_content']['fields']['imageSources'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['imageSources'],
    'exclude'   => true,
    'inputType' => 'fileTree',
    'eval'      => array(
        'fieldType'  => 'checkbox',
        'files'      => true,
        'mandatory'  => true,
        'multiple'   => true,
        'extensions' => Config::get('validImageTypes'),
        'orderField' => 'orderSRC',
        'isGallery'  => true,
    ),
);

$GLOBALS['TL_DCA']['orm_avisota_message_content']['fields']['orderSRC'] = array
(
    'inputType' => 'fileTreeOrder',
);

$GLOBALS['TL_DCA']['orm_avisota_message_content']['fields']['perRow'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['perRow'],
    'default'   => 4,
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
    'eval'      => array('tl_class' => 'w50')
);
$GLOBALS['TL_DCA']['orm_avisota_message_content']['fields']['sortBy'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['sortBy'],
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => array('custom', 'name_asc', 'name_desc', 'date_asc', 'date_desc', 'meta', 'random'),
    'reference' => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['sortBy'],
    'eval'      => array('tl_class' => 'w50')
);
