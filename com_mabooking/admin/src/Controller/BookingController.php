<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

class BookingController extends FormController
{
	protected $view_list = 'bookings';

	public function add()
	{
		$result = parent::add();

		$date = $this->input->getString('booking_date', '');

		if ($date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date))
		{
			Factory::getApplication()->setUserState('com_mabooking.edit.booking.data', ['booking_date' => $date]);
		}

		return $result;
	}

	public function cancel($key = null)
	{
		$result = parent::cancel($key);
		$this->setRedirect(Route::_('index.php?option=com_mabooking&view=dashboard', false));

		return $result;
	}
}
