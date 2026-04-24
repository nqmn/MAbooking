<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

class BookingsModel extends ListModel
{
	private const ALLOWED_ORDER_COLUMNS = [
		'b.booking_date',
		'b.event_title',
		'b.client_name',
		'b.status',
		'b.id',
	];

	protected function populateState($ordering = 'b.booking_date', $direction = 'DESC'): void
	{
		parent::populateState($ordering, $direction);

		$app = Factory::getApplication();
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$status = $app->getUserStateFromRequest($this->context . '.filter.status', 'filter_status');

		$this->setState('filter.search', $search);
		$this->setState('filter.status', $status);
	}

	protected function getListQuery()
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select(
			[
				'b.*',
				'v.title AS venue_title',
				's.title AS space_title',
			]
		)
			->from($db->quoteName('#__mabooking_bookings', 'b'))
			->leftJoin($db->quoteName('#__mabooking_venues', 'v') . ' ON v.id = b.venue_id')
			->leftJoin($db->quoteName('#__mabooking_spaces', 's') . ' ON s.id = b.space_id')
			->where('b.state >= 0');

		$search = trim((string) $this->getState('filter.search'));

		if ($search !== '')
		{
			$safeSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
			$token = $db->quote('%' . $safeSearch . '%');
			$query->where(
				implode(
					' OR ',
					[
						'b.event_title LIKE ' . $token,
						'b.client_name LIKE ' . $token,
						'b.client_email LIKE ' . $token,
						'b.client_phone LIKE ' . $token,
						'v.title LIKE ' . $token,
						's.title LIKE ' . $token,
					]
				)
			);
		}

		$status = (string) $this->getState('filter.status');

		if ($status !== '')
		{
			$query->where('b.status = ' . $db->quote($status));
		}

		$rawOrdering = (string) $this->getState('list.ordering', 'b.booking_date');
		$ordering = \in_array($rawOrdering, self::ALLOWED_ORDER_COLUMNS, true) ? $rawOrdering : 'b.booking_date';
		$direction = strtoupper((string) $this->getState('list.direction', 'DESC')) === 'ASC' ? 'ASC' : 'DESC';
		$query->order($db->quoteName($ordering) . ' ' . $direction);

		return $query;
	}

	protected function getStoreId($id = ''): string
	{
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.status');

		return parent::getStoreId($id);
	}

	public function getFilterForm($data = [], $loadData = true)
	{
		return $this->loadForm($this->context . '.filter', 'filter_bookings', ['control' => '', 'load_data' => $loadData]);
	}

	protected function loadFormData()
	{
		return [
			'filter_search' => $this->getState('filter.search'),
			'filter_status' => $this->getState('filter.status'),
		];
	}
}
