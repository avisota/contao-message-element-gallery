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
 * Message elements
 */
$GLOBALS['TL_MCE']['images'][] = 'gallery';

/**
 * Events
 */
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Avisota\Contao\Message\Element\Gallery\DefaultRenderer';
