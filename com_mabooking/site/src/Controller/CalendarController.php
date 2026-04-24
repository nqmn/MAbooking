<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

class CalendarController extends BaseController
{
	public function ics(): void
	{
		$params = ComponentHelper::getParams('com_mabooking');

		if (!(int) $params->get('enable_public_ics'))
		{
			throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		/** @var \Icc\Component\Mabooking\Site\Model\CalendarModel $model */
		$model = $this->getModel('Calendar');
		$ics = $model->buildIcsFeed();
		$app = Factory::getApplication();

		$app->setHeader('Content-Type', 'text/calendar; charset=utf-8', true);
		$app->setHeader('Content-Disposition', 'inline; filename="mabooking-calendar.ics"', true);

		echo $ics;
		$app->close();
	}
}
