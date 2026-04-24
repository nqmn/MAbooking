<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Site\Model;

\defined('_JEXEC') or die;

use Icc\Component\Mabooking\Site\Helper\ArticleHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\FormModel;

class CalendarModel extends FormModel
{
	public function getFeedBookings(): array
	{
		$db = $this->getDatabase();
		$params = ComponentHelper::getParams('com_mabooking');
		$confirmedOnly = (int) $params->get('public_ics_confirmed_only', 1) === 1;
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
			->where('b.state = 1')
			->where("b.status <> 'cancelled'")
			->where('b.booking_date >= ' . $db->quote($today))
			->order('b.booking_date ASC, b.start_time ASC');

		if ($confirmedOnly)
		{
			$query->where("b.status = 'confirmed'");
		}

		$db->setQuery($query);

		return (array) $db->loadObjectList();
	}

	public function buildIcsFeed(): string
	{
		$siteName = (string) Factory::getApplication()->get('sitename', 'MA Booking');
		$lines = [
			'BEGIN:VCALENDAR',
			'VERSION:2.0',
			'PRODID:-//MA Booking//Public Calendar//EN',
			'CALSCALE:GREGORIAN',
			'METHOD:PUBLISH',
			'X-WR-CALNAME:' . $this->escapeIcsText($siteName . ' Bookings'),
			'X-WR-CALDESC:' . $this->escapeIcsText('Public booking calendar exported from MA Booking'),
		];

		foreach ($this->getFeedBookings() as $booking)
		{
			$start = new \DateTimeImmutable($booking->booking_date . ' ' . $booking->start_time);
			$end = new \DateTimeImmutable($booking->booking_date . ' ' . $booking->end_time);
			$uid = 'mabooking-' . (int) $booking->id . '@' . preg_replace('/^https?:\/\//', '', rtrim(\Joomla\CMS\Uri\Uri::root(), '/'));
			$summary = $booking->event_title ?: ($booking->space_title . ' Booking');
			$description = trim(implode("\n", array_filter([
				'Venue: ' . $booking->venue_title,
				'Room: ' . $booking->space_title,
				$booking->notes ? 'Notes: ' . preg_replace("/\r\n|\r|\n/", ' ', (string) $booking->notes) : '',
			])));

			$lines[] = 'BEGIN:VEVENT';
			$lines[] = 'UID:' . $this->escapeIcsText($uid);
			$lines[] = 'DTSTAMP:' . gmdate('Ymd\THis\Z');
			$lines[] = 'DTSTART:' . $start->format('Ymd\THis');
			$lines[] = 'DTEND:' . $end->format('Ymd\THis');
			$lines[] = 'SUMMARY:' . $this->escapeIcsText($summary);
			$lines[] = 'DESCRIPTION:' . $this->escapeIcsText($description);
			$lines[] = 'LOCATION:' . $this->escapeIcsText(trim($booking->venue_title . ' - ' . $booking->space_title, ' -'));
			$lines[] = 'STATUS:CONFIRMED';
			$lines[] = 'END:VEVENT';
		}

		$lines[] = 'END:VCALENDAR';

		return implode("\r\n", $lines) . "\r\n";
	}

	public function getForm($data = [], $loadData = true): Form|bool
	{
		$form = $this->loadForm('com_mabooking.calendar', 'booking', ['control' => 'jform', 'load_data' => $loadData]);

		if (!$form)
		{
			return false;
		}

		return $form;
	}

	protected function loadFormData(): array
	{
		return (array) Factory::getApplication()->getUserState('com_mabooking.calendar.data', []);
	}

	public function getMonthContext(): array
	{
		$input = Factory::getApplication()->input;
		$month = max(1, min(12, $input->getInt('month', (int) date('n'))));
		$year = max(2024, $input->getInt('year', (int) date('Y')));

		$firstDay = new \DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));

		return [
			'month' => $month,
			'year' => $year,
			'firstDay' => $firstDay,
			'daysInMonth' => (int) $firstDay->format('t'),
			'startWeekday' => (int) $firstDay->format('w'),
			'label' => $firstDay->format('F Y'),
		];
	}

	public function getCalendarBookings(int $year, int $month): array
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
			->where('b.state = 1')
			->where("b.status <> 'cancelled'")
			->where('b.booking_date BETWEEN ' . $db->quote($from) . ' AND ' . $db->quote($to))
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

	public function getVenues(): array
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('id, title')
			->from($db->quoteName('#__mabooking_venues'))
			->where('state = 1')
			->order('ordering ASC, title ASC');

		$db->setQuery($query);

		return (array) $db->loadObjectList();
	}

	public function getSpaces(): array
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('id, venue_id, title')
			->from($db->quoteName('#__mabooking_spaces'))
			->where('state = 1')
			->order('venue_id ASC, ordering ASC, title ASC');

		$db->setQuery($query);
		$rows = (array) $db->loadObjectList();
		$grouped = [];

		foreach ($rows as $space)
		{
			$grouped[(int) $space->venue_id][] = $space;
		}

		return $grouped;
	}

	public function validate($form, $data, $group = null)
	{
		$data = parent::validate($form, $data, $group);

		if ($data === false)
		{
			return false;
		}

		$data = (array) $data;
		$data['start_time'] = $this->normalizeTime((string) ($data['start_time'] ?? ''));
		$data['end_time'] = $this->normalizeTime((string) ($data['end_time'] ?? ''));

		if (!$this->validateTimeWindow($data) || !$this->validateVenueSpace($data) || !$this->validateConflicts($data))
		{
			return false;
		}

		$data['status'] = 'pending';
		$data['state'] = 1;
		$data['source'] = 'site';

		return $data;
	}

	public function submitBooking(array $data): bool
	{
		$db = $this->getDatabase();
		$now = Factory::getDate()->toSql();
		$data['start_time'] = $this->normalizeTime((string) ($data['start_time'] ?? ''));
		$data['end_time'] = $this->normalizeTime((string) ($data['end_time'] ?? ''));
		$lockName = $this->buildLockName($data);
		$lockAcquired = false;
		$record = (object) [
			'event_title' => trim((string) ($data['event_title'] ?? '')),
			'alias' => ApplicationHelper::stringURLSafe(trim((string) ($data['event_title'] ?? '')) . '-' . (string) ($data['booking_date'] ?? '')),
			'booking_date' => $data['booking_date'],
			'start_time' => $data['start_time'],
			'end_time' => $data['end_time'],
			'venue_id' => (int) $data['venue_id'],
			'space_id' => (int) $data['space_id'],
			'client_name' => trim((string) $data['client_name']),
			'client_phone' => trim((string) $data['client_phone']),
			'client_email' => trim((string) $data['client_email']),
			'attendees' => (int) ($data['attendees'] ?? 0),
			'status' => 'pending',
			'source' => 'site',
			'article_id' => 0,
			'notes' => $data['notes'] ?? '',
			'state' => 1,
			'created' => $now,
			'modified' => null,
		];

		try
		{
			$db->transactionStart();
			$lockAcquired = $this->acquireLock($lockName);

			if (!$this->validateConflicts($data))
			{
				$db->transactionRollback();

				return false;
			}

			$db->insertObject('#__mabooking_bookings', $record);

			$bookingId = (int) $db->insertid();
			$record->id = $bookingId;
			$syncResult = ArticleHelper::syncBookingArticle((array) $record);

			if (!empty($syncResult['article_id']))
			{
				ArticleHelper::persistArticleId($bookingId, (int) $syncResult['article_id']);
			}

			if (!empty($syncResult['message']))
			{
				Factory::getApplication()->enqueueMessage($syncResult['message'], 'warning');
			}

			$db->transactionCommit();
		}
		catch (\RuntimeException $e)
		{
			$this->safeRollback($db);
			$this->setError($e->getMessage());

			return false;
		}
		finally
		{
			if ($lockAcquired)
			{
				$this->releaseLock($lockName);
			}
		}

		Factory::getApplication()->setUserState('com_mabooking.calendar.data', null);

		return true;
	}

	private function validateVenueSpace(array $data): bool
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__mabooking_spaces'))
			->where('id = ' . (int) ($data['space_id'] ?? 0))
			->where('venue_id = ' . (int) ($data['venue_id'] ?? 0));

		$db->setQuery($query);

		if ((int) $db->loadResult() === 0)
		{
			$this->setError('The selected room does not belong to the selected venue.');

			return false;
		}

		return true;
	}

	private function validateTimeWindow(array $data): bool
	{
		$start = (string) ($data['start_time'] ?? '');
		$end = (string) ($data['end_time'] ?? '');
		$timePattern = '/^([01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/';

		if (!preg_match($timePattern, $start) || !preg_match($timePattern, $end))
		{
			$this->setError('Start time and end time must use HH:MM or HH:MM:SS format.');

			return false;
		}

		if ($this->normalizeTime($end) <= $this->normalizeTime($start))
		{
			$this->setError('End time must be later than start time.');

			return false;
		}

		return true;
	}

	private function validateConflicts(array $data): bool
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__mabooking_bookings'))
			->where('venue_id = ' . (int) ($data['venue_id'] ?? 0))
			->where('space_id = ' . (int) ($data['space_id'] ?? 0))
			->where('booking_date = ' . $db->quote((string) ($data['booking_date'] ?? '')))
			->where("status <> 'cancelled'")
			->where(
				'(' .
				$db->quote((string) ($data['start_time'] ?? '')) . ' < end_time AND ' .
				$db->quote((string) ($data['end_time'] ?? '')) . ' > start_time' .
				')'
			);

		$db->setQuery($query);

		if ((int) $db->loadResult() > 0)
		{
			$this->setError('The selected slot is already reserved.');

			return false;
		}

		return true;
	}

	private function normalizeTime(string $time): string
	{
		if (preg_match('/^\d{2}:\d{2}$/', $time))
		{
			return $time . ':00';
		}

		return $time;
	}

	private function buildLockName(array $data): string
	{
		return 'mabooking:' . (int) ($data['venue_id'] ?? 0) . ':' . (int) ($data['space_id'] ?? 0) . ':' . (string) ($data['booking_date'] ?? '');
	}

	private function acquireLock(string $lockName): bool
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('GET_LOCK(' . $db->quote($lockName) . ', 10)');
		$db->setQuery($query);

		if ((int) $db->loadResult() !== 1)
		{
			$this->setError('Unable to acquire a booking lock. Please try again.');

			return false;
		}

		return true;
	}

	private function releaseLock(string $lockName): void
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('RELEASE_LOCK(' . $db->quote($lockName) . ')');
		$db->setQuery($query);
		$db->loadResult();
	}

	private function safeRollback($db): void
	{
		try
		{
			$db->transactionRollback();
		}
		catch (\Throwable)
		{
		}
	}

	private function escapeIcsText(string $value): string
	{
		$value = str_replace('\\', '\\\\', $value);
		$value = str_replace(';', '\;', $value);
		$value = str_replace(',', '\,', $value);
		$value = preg_replace("/\r\n|\r|\n/", '\n', $value);

		return $value;
	}
}
