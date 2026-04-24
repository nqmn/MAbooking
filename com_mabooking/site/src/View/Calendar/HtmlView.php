<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Site\View\Calendar;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
	public array $calendar = [];

	public array $bookings = [];

	public array $venues = [];

	public array $spaces = [];

	public $form;

	public function display($tpl = null): void
	{
		/** @var \Icc\Component\Mabooking\Site\Model\CalendarModel $model */
		$model = $this->getModel();
		$this->calendar = $model->getMonthContext();
		$this->bookings = $model->getCalendarBookings($this->calendar['year'], $this->calendar['month']);
		$this->venues = $model->getVenues();
		$this->spaces = $model->getSpaces();
		$this->form = $model->getForm();

		parent::display($tpl);
	}
}
