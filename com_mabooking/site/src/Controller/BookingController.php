<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

class BookingController extends BaseController
{
	public function submit(): void
	{
		$this->checkToken();

		$app = Factory::getApplication();
		$data = $this->input->post->get('jform', [], 'array');
		/** @var \Icc\Component\Mabooking\Site\Model\CalendarModel $model */
		$model = $this->getModel('Calendar');
		$form = $model->getForm($data, false);
		$validData = $model->validate($form, $data);

		if ($validData === false)
		{
			foreach ($model->getErrors() as $error)
			{
				$app->enqueueMessage($error instanceof \Throwable ? $error->getMessage() : $error, 'warning');
			}

			$app->setUserState('com_mabooking.calendar.data', $data);
			$this->setRedirect(Route::_('index.php?option=com_mabooking&view=calendar', false));

			return;
		}

		if (!$model->submitBooking($validData))
		{
			$app->enqueueMessage($model->getError() ?: Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			$app->setUserState('com_mabooking.calendar.data', $data);
			$this->setRedirect(Route::_('index.php?option=com_mabooking&view=calendar', false));

			return;
		}

		$app->enqueueMessage(Text::_('COM_MABOOKING_BOOKING_SUBMITTED'), 'message');
		$this->setRedirect(Route::_('index.php?option=com_mabooking&view=calendar', false));
	}
}
