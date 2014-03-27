<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-message-element-article
 * @license    LGPL-3.0+
 * @filesource
 */


namespace Avisota\Contao\Message\Element\Gallery;

use Avisota\Contao\Core\Message\Renderer;
use Avisota\Contao\Entity\MessageContent;
use Avisota\Contao\Message\Core\Event\AvisotaMessageEvents;
use Avisota\Contao\Message\Core\Event\RenderMessageContentEvent;
use Avisota\Recipient\RecipientInterface;
use Contao\Doctrine\ORM\Entity;
use Contao\Doctrine\ORM\EntityAccessor;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Controller\GetArticleEvent;
use ContaoCommunityAlliance\Contao\Bindings\Events\Image\ResizeImageEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class DefaultRenderer
 */
class DefaultRenderer implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			AvisotaMessageEvents::RENDER_MESSAGE_CONTENT => 'renderContent',
		);
	}

	/**
	 * Render a single message content element.
	 *
	 * @param MessageContent     $content
	 * @param RecipientInterface $recipient
	 *
	 * @return string
	 */
	public function renderContent(RenderMessageContentEvent $event)
	{
		$content = $event->getMessageContent();

		if ($content->getType() != 'gallery' || $event->getRenderedContent()) {
			return;
		}

		/** @var EventDispatcher $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];

		/** @var EntityAccessor $entityAccessor */
		$entityAccessor = $GLOBALS['container']['doctrine.orm.entityAccessor'];

		$context = $entityAccessor->getProperties($content);

		$size    = $content->getSize();
		$images  = array();
		$sorting = array();
		foreach ($context['multiSRC'] as $index => $file) {
			$context['multiSRC'][$index] = $file = \Compat::resolveFile($file);

			switch ($content->getSortBy()) {
				case 'name_asc':
				case 'name_desc':
					$sorting[] = basename($file);
					break;

				case 'date_asc':
				case 'date_desc':
					$sorting[] = filemtime(TL_ROOT . DIRECTORY_SEPARATOR . $file);
					break;

				case 'random':
					$sorting[] = rand(-PHP_INT_MAX, PHP_INT_MAX);
			}

			$resizeImageEvent = new ResizeImageEvent($file, $size[0], $size[1], $size[2]);
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
				function ($a, $b) use ($sorting) {
					return array_search($a, $sorting) - array_search($b, $sorting);
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
