<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class DashboardModel extends BaseDatabaseModel
{
	public function getCalendarContext(): array
	{
		$input = Factory::getApplication()->input;
		$month = max(1, min(12, $input->getInt('month', (int) date('n'))));
		$year = max(2024, $input->getInt('year', (int) date('Y')));
		$firstDay = new \DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));

		return [
			'month' => $month,
			'year' => $year,
			'label' => $firstDay->format('F Y'),
			'daysInMonth' => (int) $firstDay->format('t'),
			'startWeekday' => (int) $firstDay->format('w'),
		];
	}

	public function getSummary(): array
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select(
				[
					'COUNT(*) AS total',
					"SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed",
					"SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending",
					"SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled",
				]
			)
			->from($db->quoteName('#__mabooking_bookings'));

		$db->setQuery($query);
		$result = (array) $db->loadAssoc();

		return [
			'total' => (int) ($result['total'] ?? 0),
			'confirmed' => (int) ($result['confirmed'] ?? 0),
			'pending' => (int) ($result['pending'] ?? 0),
			'cancelled' => (int) ($result['cancelled'] ?? 0),
		];
	}

	public function getUpcomingBookings(int $limit = 8): array
	{
		$db = $this->getDatabase();
		$today = (new \DateTimeImmutable('today'))->format('Y-m-d');
		$query = $db->getQuery(true)
			->select(
				[
					'b.*',
					'v.title AS venue_title',
					's.title AS space_title',
				]
			)
			->from($db->quoteName('#__mabooking_bookings', 'b'))
			->leftJoin($db->quoteName('#__mabooking_venues', 'v') . ' ON v.id = b.venue_id')
			->leftJoin($db->quoteName('#__mabooking_spaces', 's') . ' ON s.id = b.space_id')
			->where('b.booking_date >= ' . $db->quote($today))
			->where('b.state = 1')
			->order('b.booking_date ASC, b.start_time ASC');

		$db->setQuery($query, 0, $limit);

		return (array) $db->loadObjectList();
	}

	public function getMonthlyBookings(int $year, int $month): array
	{
		$db = $this->getDatabase();
		$from = sprintf('%04d-%02d-01', $year, $month);
		$to = (new \DateTimeImmutable($from))->modify('last day of this month')->format('Y-m-d');
		$query = $db->getQuery(true)
			->select(
				[
					'b.*',
					'v.title AS venue_title',
					's.title AS space_title',
				]
			)
			->from($db->quoteName('#__mabooking_bookings', 'b'))
			->leftJoin($db->quoteName('#__mabooking_venues', 'v') . ' ON v.id = b.venue_id')
			->leftJoin($db->quoteName('#__mabooking_spaces', 's') . ' ON s.id = b.space_id')
			->where('b.booking_date BETWEEN ' . $db->quote($from) . ' AND ' . $db->quote($to))
			->where('b.state = 1')
			->order('b.booking_date ASC, b.start_time ASC');

		$db->setQuery($query);
		$rows = (array) $db->loadObjectList();
		$grouped = [];

		foreach ($rows as $row)
		{
			$grouped[$row->booking_date][] = $row;
		}

		return $grouped;
	}
}
