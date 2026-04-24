<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Administrator\View\Dashboard;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	public array $summary = [];

	public array $upcomingBookings = [];

	public array $allBookings = [];

	public array $pastBookings = [];

	public array $calendar = [];

	public array $monthlyBookings = [];

	public function display($tpl = null): void
	{
		/** @var \Icc\Component\Mabooking\Administrator\Model\DashboardModel $model */
		$model = $this->getModel();
		$this->summary = $model->getSummary();
		$this->calendar = $model->getCalendarContext();
		$this->allBookings = $model->getAllBookings();
		$this->monthlyBookings = $model->getMonthlyBookings($this->calendar['year'], $this->calendar['month']);
		$this->upcomingBookings = $model->getUpcomingBookings();
		$this->pastBookings = $model->getPastBookings();

		$this->addToolbar();

		parent::display($tpl);
	}

	private function addToolbar(): void
	{
		ToolbarHelper::title('MA Booking', 'calendar');
		ToolbarHelper::preferences('com_mabooking');
	}
}
