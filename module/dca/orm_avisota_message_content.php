<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-message-element-gallery
 * @license    LGPL-3.0+
 * @filesource
 */


/**
 * Table orm_avisota_message_content
 * Entity Avisota\Contao:MessageContent
 */
$GLOBALS['TL_DCA']['orm_avisota_message_content']['metapalettes']['gallery']       = array
(
	'type'     => array('type', 'cell', 'headline'),
	'source'   => array('multiSRC'),
	'image'    => array('size', 'imagemargin', 'perRow', 'sortBy'),
	'expert'   => array(':hide', 'cssID', 'space'),
);


$GLOBALS['TL_DCA']['orm_avisota_message_content']['fields']['multiSRC'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['multiSRC'],
	'exclude'   => true,
	'inputType' => 'fileTree',
	'eval'      => array('fieldType' => 'checkbox', 'files' => true, 'mandatory' => true)
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
	'options'   => array('name_asc', 'name_desc', 'date_asc', 'date_desc', 'meta', 'random'),
	'reference' => &$GLOBALS['TL_LANG']['orm_avisota_message_content'],
	'eval'      => array('tl_class' => 'w50')
);
