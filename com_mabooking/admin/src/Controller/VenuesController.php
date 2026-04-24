<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

class VenuesController extends BaseController
{
	private const MANAGE_TAB_ROUTE = 'index.php?option=com_mabooking&view=venues&tab=manage';

	public function saveVenue(): void
	{
		$this->checkToken();

		$app = Factory::getApplication();
		/** @var \Icc\Component\Mabooking\Administrator\Model\VenuesModel $model */
		$model = $this->getModel('Venues');
		$data = $this->input->post->get('venue', [], 'array');

		if (!$model->saveVenue($data))
		{
			$app->enqueueMessage($model->getError() ?: Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
		}
		else
		{
			$app->enqueueMessage('Venue created successfully.', 'message');
		}

		$this->setRedirect(Route::_(self::MANAGE_TAB_ROUTE, false));
	}

	public function saveSpace(): void
	{
		$this->checkToken();

		$app = Factory::getApplication();
		/** @var \Icc\Component\Mabooking\Administrator\Model\VenuesModel $model */
		$model = $this->getModel('Venues');
		$data = $this->input->post->get('space', [], 'array');

		if (!$model->saveSpace($data))
		{
			$app->enqueueMessage($model->getError() ?: Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
		}
		else
		{
			$app->enqueueMessage('Room created successfully.', 'message');
		}

		$this->setRedirect(Route::_(self::MANAGE_TAB_ROUTE, false));
	}

	public function deleteVenue(): void
	{
		$this->checkToken();

		$app = Factory::getApplication();
		/** @var \Icc\Component\Mabooking\Administrator\Model\VenuesModel $model */
		$model = $this->getModel('Venues');
		$id = $this->input->post->getInt('venue_id');

		if (!$model->deleteVenue($id))
		{
			$app->enqueueMessage($model->getError() ?: Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
		}
		else
		{
			$app->enqueueMessage('Venue deleted successfully.', 'message');
		}

		$this->setRedirect(Route::_(self::MANAGE_TAB_ROUTE, false));
	}

	public function deleteSpace(): void
	{
		$this->checkToken();

		$app = Factory::getApplication();
		/** @var \Icc\Component\Mabooking\Administrator\Model\VenuesModel $model */
		$model = $this->getModel('Venues');
		$id = $this->input->post->getInt('space_id');

		if (!$model->deleteSpace($id))
		{
			$app->enqueueMessage($model->getError() ?: Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
		}
		else
		{
			$app->enqueueMessage('Room deleted successfully.', 'message');
		}

		$this->setRedirect(Route::_(self::MANAGE_TAB_ROUTE, false));
	}
}
