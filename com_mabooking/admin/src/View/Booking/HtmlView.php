<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Administrator\View\Booking;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	public $form;

	public $item;

	public array $venues = [];

	public array $spaces = [];

	public function display($tpl = null): void
	{
		/** @var \Icc\Component\Mabooking\Administrator\Model\BookingModel $model */
		$model = $this->getModel();
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->venues = $model->getVenues();
		$this->spaces = $model->getSpaces();

		$this->addToolbar();

		parent::display($tpl);
	}

	private function addToolbar(): void
	{
		$isNew = empty($this->item->id);
		ToolbarHelper::title($isNew ? 'New Booking' : 'Edit Booking', 'pencil-alt');
		ToolbarHelper::apply('booking.apply');
		ToolbarHelper::save('booking.save');
		ToolbarHelper::save2new('booking.save2new');
		ToolbarHelper::cancel('booking.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}
}
