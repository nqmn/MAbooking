<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

class ArticleHelper
{
	public static function syncBookingArticle(array|object $booking): array
	{
		$booking = (object) $booking;
		$params = ComponentHelper::getParams('com_mabooking');

		if (!(int) $params->get('enable_article_sync'))
		{
			return ['synced' => false, 'article_id' => (int) ($booking->article_id ?? 0), 'message' => null];
		}

		$categoryId = (int) $params->get('article_category_id');

		if ($categoryId <= 0)
		{
			return ['synced' => false, 'article_id' => (int) ($booking->article_id ?? 0), 'message' => 'Article sync is enabled but no Joomla article category is configured.'];
		}

		$statuses = self::getConfiguredStatuses($params->get('article_sync_statuses', ['confirmed']));
		$shouldSync = \in_array((string) $booking->status, $statuses, true);
		$articleId = (int) ($booking->article_id ?? 0);

		if (!$shouldSync)
		{
			if ($articleId > 0)
			{
				self::setArticleState($articleId, 0);
			}

			return ['synced' => false, 'article_id' => $articleId, 'message' => null];
		}

		$table = Table::getInstance('Content');
		$isNew = $articleId <= 0 || !$table->load($articleId);
		$now = Factory::getDate()->toSql();
		$title = trim((string) ($booking->event_title ?? 'Booking'));
		$alias = ApplicationHelper::stringURLSafe(($booking->alias ?: $title) . '-' . $booking->booking_date);
		$articleState = (int) $params->get('article_state', 1);
		$placeholderText = self::buildPlaceholderText($booking);

		if ($isNew)
		{
			$table->id = 0;
			$table->introtext = $placeholderText;
			$table->fulltext = '';
			$table->created = $now;
			$table->created_by = (int) Factory::getApplication()->getIdentity()->id;
		}

		$table->title = $title;
		$table->alias = $alias !== '' ? $alias : ApplicationHelper::stringURLSafe($title);
		$table->catid = $categoryId;
		$table->state = $articleState;
		$table->access = 1;
		$table->language = '*';
		$table->modified = $now;
		$table->modified_by = (int) Factory::getApplication()->getIdentity()->id;
		$table->publish_up = null;
		$table->publish_down = null;

		if (!$table->store())
		{
			throw new \RuntimeException($table->getError());
		}

		return ['synced' => true, 'article_id' => (int) $table->id, 'message' => null];
	}

	public static function persistArticleId(int $bookingId, int $articleId): void
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true)
			->update($db->quoteName('#__mabooking_bookings'))
			->set($db->quoteName('article_id') . ' = ' . (int) $articleId)
			->where($db->quoteName('id') . ' = ' . (int) $bookingId);
		$db->setQuery($query);
		$db->execute();
	}

	private static function getConfiguredStatuses(mixed $rawStatuses): array
	{
		if (\is_string($rawStatuses))
		{
			$rawStatuses = array_filter(array_map('trim', explode(',', $rawStatuses)));
		}

		if (!\is_array($rawStatuses))
		{
			$rawStatuses = ['confirmed'];
		}

		return array_values(array_unique(array_map('strval', array_filter($rawStatuses))));
	}

	private static function setArticleState(int $articleId, int $state): void
	{
		$table = Table::getInstance('Content');

		if ($table->load($articleId))
		{
			$table->state = $state;
			$table->modified = Factory::getDate()->toSql();
			$table->modified_by = (int) Factory::getApplication()->getIdentity()->id;
			$table->store();
		}
	}

	private static function buildPlaceholderText(object $booking): string
	{
		$lines = [
			'<p>This article was generated from a MA Booking record. You can now edit this article freely for description, gallery, media, and article content.</p>',
			'<ul>',
			'<li><strong>Booking date:</strong> ' . htmlspecialchars((string) $booking->booking_date, ENT_QUOTES, 'UTF-8') . '</li>',
			'<li><strong>Time:</strong> ' . htmlspecialchars((string) $booking->start_time . ' - ' . $booking->end_time, ENT_QUOTES, 'UTF-8') . '</li>',
			'<li><strong>Client:</strong> ' . htmlspecialchars((string) $booking->client_name, ENT_QUOTES, 'UTF-8') . '</li>',
			'<li><strong>Status:</strong> ' . htmlspecialchars(ucfirst((string) $booking->status), ENT_QUOTES, 'UTF-8') . '</li>',
			'</ul>',
		];

		if (!empty($booking->notes))
		{
			$lines[] = '<p><strong>Booking notes:</strong></p>';
			$lines[] = '<p>' . nl2br(htmlspecialchars((string) $booking->notes, ENT_QUOTES, 'UTF-8')) . '</p>';
		}

		return implode("\n", $lines);
	}
}
