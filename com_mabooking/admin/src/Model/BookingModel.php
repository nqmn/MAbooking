<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Administrator\Model;

\defined('_JEXEC') or die;

use Icc\Component\Mabooking\Administrator\Helper\ArticleHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;

class BookingModel extends AdminModel
{
	public $typeAlias = 'com_mabooking.booking';

	protected $text_prefix = 'COM_MABOOKING';

	public function getTable($type = 'Booking', $prefix = 'Administrator', $config = [])
	{
		return $this->getMVCFactory()->createTable($type, $prefix, $config);
	}

	public function getForm($data = [], $loadData = true): Form|bool
	{
		$form = $this->loadForm('com_mabooking.booking', 'booking', ['control' => 'jform', 'load_data' => $loadData]);

		if (!$form)
		{
			return false;
		}

		return $form;
	}

	protected function loadFormData(): array
	{
		$data = Factory::getApplication()->getUserState('com_mabooking.edit.booking.data', []);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return (array) $data;
	}

	protected function prepareTable($table): void
	{
		$table->event_title = trim((string) $table->event_title);
		$table->client_name = trim((string) $table->client_name);
		$table->client_email = trim((string) $table->client_email);
		$table->start_time = $this->normalizeTime((string) $table->start_time);
		$table->end_time = $this->normalizeTime((string) $table->end_time);
	}

	public function save($data): bool
	{
		$data = (array) $data;
		$data['start_time'] = $this->normalizeTime((string) ($data['start_time'] ?? ''));
		$data['end_time'] = $this->normalizeTime((string) ($data['end_time'] ?? ''));

		if (!$this->validateTimeWindow($data) || !$this->validateVenueSpace($data) || !$this->validateConflicts($data))
		{
			return false;
		}

		$db = $this->getDatabase();
		$lockName = $this->buildLockName($data);
		$lockAcquired = false;

		try
		{
			$db->transactionStart();
			$lockAcquired = $this->acquireLock($lockName);

			if (!$this->validateConflicts($data))
			{
				$db->transactionRollback();

				return false;
			}

			$result = parent::save($data);

			if (!$result)
			{
				$db->transactionRollback();

				return false;
			}

			$item = $this->getItem((int) $this->getState($this->getName() . '.id'));

			$syncResult = ArticleHelper::syncBookingArticle((array) $item);

			if (!empty($syncResult['article_id']) && (int) $syncResult['article_id'] !== (int) ($item->article_id ?? 0))
			{
				ArticleHelper::persistArticleId((int) $item->id, (int) $syncResult['article_id']);
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

		return true;
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

		return $data;
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

	private function validateConflicts(array $data): bool
	{
		$db = $this->getDatabase();
		$id = (int) ($data['id'] ?? 0);
		$venueId = (int) ($data['venue_id'] ?? 0);
		$spaceId = (int) ($data['space_id'] ?? 0);
		$date = $db->quote((string) ($data['booking_date'] ?? ''));
		$start = $db->quote((string) ($data['start_time'] ?? ''));
		$end = $db->quote((string) ($data['end_time'] ?? ''));

		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__mabooking_bookings'))
			->where('venue_id = ' . $venueId)
			->where('space_id = ' . $spaceId)
			->where('booking_date = ' . $date)
			->where("status <> 'cancelled'")
			->where('(' . $start . ' < end_time AND ' . $end . ' > start_time)');

		if ($id > 0)
		{
			$query->where('id <> ' . $id);
		}

		$db->setQuery($query);

		if ((int) $db->loadResult() > 0)
		{
			$this->setError('This room already has an overlapping booking for the selected time.');

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
}
