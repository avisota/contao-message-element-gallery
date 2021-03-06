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

namespace Avisota\Contao\Message\Element\Gallery;

use Avisota\Contao\Message\Core\Event\AvisotaMessageEvents;
use Avisota\Contao\Message\Core\Event\RenderMessageContentEvent;
use Contao\Doctrine\ORM\EntityAccessor;
use Contao\File;
use Contao\FilesModel;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Image\ResizeImageEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DefaultRenderer
 */
class DefaultRenderer implements EventSubscriberInterface
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
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            AvisotaMessageEvents::RENDER_MESSAGE_CONTENT => array(
                array('renderContent'),
            ),
        );
    }

    /**
     * Render a single message content element.
     *
     * @param RenderMessageContentEvent $event
     *
     * @return string
     * @internal param MessageContent $content
     * @internal param RecipientInterface $recipient
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function renderContent(RenderMessageContentEvent $event)
    {
        global $container;

        $content = $event->getMessageContent();

        if ($content->getType() != 'gallery' || $event->getRenderedContent()) {
            return;
        }

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $container['event-dispatcher'];

        /** @var EntityAccessor $entityAccessor */
        $entityAccessor = $container['doctrine.orm.entityAccessor'];

        $context = $entityAccessor->getProperties($content);

        $size         = $content->getImageSize();
        $images       = array();
        $sorting      = array();
        $imageSources = 'imageSources';
        if ($content->getSortBy() === 'custom') {
            $imageSources = 'orderSRC';
        }
        foreach ($context[$imageSources] as $index => $file) {
            $context[$imageSources][$index] = FilesModel::findByUuid($context[$imageSources][$index])->path;

            $file = new File($context[$imageSources][$index], true);

            if (!$file->exists()) {
                unset($context[$imageSources][$index]);
                continue;
            }

            switch ($content->getSortBy()) {
                case 'name_asc':
                case 'name_desc':
                    $sorting[] = $file->name;
                    break;

                case 'date_asc':
                case 'date_desc':
                    $sorting[] = $file->mime;
                    break;

                case 'random':
                    $sorting[] = mt_rand(-PHP_INT_MAX, PHP_INT_MAX);
            }

            $resizeImageEvent = new ResizeImageEvent($file->path, $size[0], $size[1], $size[2]);
            $eventDispatcher->dispatch(ContaoEvents::IMAGE_RESIZE, $resizeImageEvent);

            $images[] = $resizeImageEvent->getResultImage();
        }

        switch ($content->getSortBy()) {
            case 'name_asc':
                uasort($sorting, 'strnatcasecmp');
                break;

            case 'name_desc':
                uasort($sorting, 'strnatcasecmp');
                $sorting = array_reverse($sorting, true);
                break;

            case 'random':
            case 'date_asc':
                asort($sorting);
                break;

            case 'date_desc':
                arsort($sorting);
                break;

            default:
                $sorting = false;
        }

        if ($sorting) {
            $sorting = array_keys($sorting);
            uksort(
                $images,
                function ($primary, $secondary) use ($sorting) {
                    return array_search($primary, $sorting) - array_search($secondary, $sorting);
                }
            );
        }

        $context['rows'] = array();
        while (count($images)) {
            $row    = array_slice($images, 0, $content->getPerRow());
            $images = array_slice($images, $content->getPerRow());

            while (count($row) < $content->getPerRow()) {
                $row[] = false;
            }

            $context['rows'][] = $row;
        }

        $styles  = array();
        $margins = $content->getImagemargin();
        foreach (array('top', 'right', 'bottom', 'left') as $property) {
            if (!empty($margins[$property])) {
                $styles[] = sprintf('padding-%s:%s%s', $property, $margins[$property], $margins['unit']);
            }
        }
        $context['styles'] = implode(';', $styles);

        $template = new \TwigTemplate('avisota/message/renderer/default/mce_gallery', 'html');
        $buffer   = $template->parse($context);

        $event->setRenderedContent($buffer);
    }
}
