<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message-element-article
 * @license    LGPL-3.0+
 * @filesource
 */

use Avisota\Contao\Message\Element\Gallery\DataContainer\OrderSRCField;
use Avisota\Contao\Message\Element\Gallery\DefaultRenderer;

return array(
    new DefaultRenderer(),
    new OrderSRCField()
);
