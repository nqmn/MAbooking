<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Administrator\View\Bookings;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	public array $items = [];

	public $pagination;

	public $state;

	public $filterForm;

	public $activeFilters;

	public function display($tpl = null): void
	{
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->addToolbar();

		parent::display($tpl);
	}

	private function addToolbar(): void
	{
		ToolbarHelper::title('Bookings', 'address-book');
		ToolbarHelper::addNew('booking.add');
		ToolbarHelper::editList('booking.edit');
		ToolbarHelper::deleteList('', 'bookings.delete');
	}
}
