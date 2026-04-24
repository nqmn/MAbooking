<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Administrator\Table;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

class BookingTable extends Table
{
	public function __construct($db)
	{
		parent::__construct('#__mabooking_bookings', 'id', $db);
	}

	public function check(): bool
	{
		if (trim((string) $this->event_title) === '')
		{
			$this->event_title = trim((string) $this->client_name . ' Booking');
		}

		if (trim((string) $this->alias) === '')
		{
			$this->alias = ApplicationHelper::stringURLSafe($this->event_title . '-' . $this->booking_date);
		}

		if ($this->end_time <= $this->start_time)
		{
			$this->setError('End time must be later than start time.');

			return false;
		}

		$date = Factory::getDate()->toSql();

		if ((int) $this->id > 0)
		{
			$this->modified = $date;
		}
		else
		{
			$this->created = $date;
		}

		return parent::check();
	}
}
