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

namespace Avisota\Contao\Message\Element\Gallery\DataContainer;

use ContaoCommunityAlliance\DcGeneral\Event\PreEditModelEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderSRCField implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            PreEditModelEvent::NAME => array(
                array('parseDefaultValue')
            )
        );
    }

    // Todo remove this if dc-general has the hotfix/FileTreeOrder
    public function parseDefaultValue(PreEditModelEvent $event, $name, EventDispatcher $eventDispatcher)
    {
        $properties = $event->getModel()->getPropertiesAsArray();
        if (!array_key_exists('orderSRC', $properties)
            || (array_key_exists('orderSRC', $properties) && $properties['orderSRC'] != null)
        ) {
            return;
        }

        $entity = $event->getModel()->getEntity();
        $entity->setOrderSRC(array());
    }
}
