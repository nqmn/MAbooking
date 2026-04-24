<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class VenuesModel extends BaseDatabaseModel
{
	public function getVenueOptions(): array
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

	public function getVenuesWithSpaces(): array
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select(
				[
					'v.id',
					'v.title',
					'v.alias',
					'v.description',
					'v.ordering',
					'v.state',
					's.id AS space_id',
					's.title AS space_title',
					's.capacity_min',
					's.capacity_max',
					's.size_label',
					's.details',
					's.ordering AS space_ordering',
				]
			)
			->from($db->quoteName('#__mabooking_venues', 'v'))
			->leftJoin($db->quoteName('#__mabooking_spaces', 's') . ' ON s.venue_id = v.id AND s.state = 1')
			->where('v.state = 1')
			->order('v.ordering ASC, v.title ASC, s.ordering ASC, s.title ASC');

		$db->setQuery($query);
		$rows = (array) $db->loadObjectList();
		$grouped = [];

		foreach ($rows as $row)
		{
			$venueId = (int) $row->id;

			if (!isset($grouped[$venueId]))
			{
				$grouped[$venueId] = (object) [
					'id' => $venueId,
					'title' => $row->title,
					'alias' => $row->alias,
					'description' => $row->description,
					'ordering' => (int) $row->ordering,
					'spaces' => [],
				];
			}

			if (!empty($row->space_id))
			{
				$grouped[$venueId]->spaces[] = (object) [
					'id' => (int) $row->space_id,
					'title' => $row->space_title,
					'capacity_min' => (int) $row->capacity_min,
					'capacity_max' => (int) $row->capacity_max,
					'size_label' => (string) $row->size_label,
					'details' => (string) $row->details,
					'ordering' => (int) $row->space_ordering,
				];
			}
		}

		return array_values($grouped);
	}

	public function saveVenue(array $data): bool
	{
		$id = (int) ($data['id'] ?? 0);
		$title = trim((string) ($data['title'] ?? ''));

		if ($title === '')
		{
			$this->setError('Venue title is required.');

			return false;
		}

		$db = $this->getDatabase();
		$now = Factory::getDate()->toSql();
		$record = (object) [
			'id' => $id,
			'title' => $title,
			'alias' => ApplicationHelper::stringURLSafe((string) ($data['alias'] ?? $title)),
			'description' => trim((string) ($data['description'] ?? '')),
			'ordering' => $id > 0 ? (int) ($data['ordering'] ?? 0) : $this->getNextOrdering('#__mabooking_venues'),
			'state' => 1,
			'modified' => $id > 0 ? $now : null,
		];

		if ($id <= 0)
		{
			$record->created = $now;
		}

		try
		{
			if ($id > 0)
			{
				$db->updateObject('#__mabooking_venues', $record, 'id');
			}
			else
			{
				$db->insertObject('#__mabooking_venues', $record);
			}
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return true;
	}

	public function saveSpace(array $data): bool
	{
		$id = (int) ($data['id'] ?? 0);
		$title = trim((string) ($data['title'] ?? ''));
		$venueId = (int) ($data['venue_id'] ?? 0);

		if ($venueId <= 0)
		{
			$this->setError('Please select a venue for the room.');

			return false;
		}

		if ($title === '')
		{
			$this->setError('Room title is required.');

			return false;
		}

		if (!$this->venueExists($venueId))
		{
			$this->setError('The selected venue no longer exists.');

			return false;
		}

		$db = $this->getDatabase();
		$now = Factory::getDate()->toSql();
		$record = (object) [
			'id' => $id,
			'venue_id' => $venueId,
			'title' => $title,
			'alias' => ApplicationHelper::stringURLSafe((string) ($data['alias'] ?? $title)),
			'capacity_min' => max(0, (int) ($data['capacity_min'] ?? 0)),
			'capacity_max' => max(0, (int) ($data['capacity_max'] ?? 0)),
			'size_label' => trim((string) ($data['size_label'] ?? '')),
			'details' => trim((string) ($data['details'] ?? '')),
			'ordering' => $id > 0 ? (int) ($data['ordering'] ?? 0) : $this->getNextOrdering('#__mabooking_spaces', 'venue_id = ' . $venueId),
			'state' => 1,
			'modified' => $id > 0 ? $now : null,
		];

		if ($id <= 0)
		{
			$record->created = $now;
		}

		try
		{
			if ($id > 0)
			{
				$db->updateObject('#__mabooking_spaces', $record, 'id');
			}
			else
			{
				$db->insertObject('#__mabooking_spaces', $record);
			}
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return true;
	}

	public function deleteVenue(int $id): bool
	{
		if ($id <= 0)
		{
			$this->setError('Invalid venue selected.');

			return false;
		}

		$db = $this->getDatabase();

		try
		{
			$query = $db->getQuery(true)
				->update($db->quoteName('#__mabooking_venues'))
				->set($db->quoteName('state') . ' = 0')
				->set($db->quoteName('modified') . ' = ' . $db->quote(Factory::getDate()->toSql()))
				->where($db->quoteName('id') . ' = ' . $id);
			$db->setQuery($query)->execute();

			$query = $db->getQuery(true)
				->update($db->quoteName('#__mabooking_spaces'))
				->set($db->quoteName('state') . ' = 0')
				->set($db->quoteName('modified') . ' = ' . $db->quote(Factory::getDate()->toSql()))
				->where($db->quoteName('venue_id') . ' = ' . $id);
			$db->setQuery($query)->execute();
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return true;
	}

	public function deleteSpace(int $id): bool
	{
		if ($id <= 0)
		{
			$this->setError('Invalid room selected.');

			return false;
		}

		$db = $this->getDatabase();

		try
		{
			$query = $db->getQuery(true)
				->update($db->quoteName('#__mabooking_spaces'))
				->set($db->quoteName('state') . ' = 0')
				->set($db->quoteName('modified') . ' = ' . $db->quote(Factory::getDate()->toSql()))
				->where($db->quoteName('id') . ' = ' . $id);
			$db->setQuery($query)->execute();
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return true;
	}

	private function getNextOrdering(string $table, string $where = ''): int
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('COALESCE(MAX(ordering), 0)')
			->from($db->quoteName($table));

		if ($where !== '')
		{
			$query->where($where);
		}

		$db->setQuery($query);

		return (int) $db->loadResult() + 1;
	}

	private function venueExists(int $venueId): bool
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__mabooking_venues'))
			->where('id = ' . $venueId)
			->where('state = 1');

		$db->setQuery($query);

		return (int) $db->loadResult() > 0;
	}
}
