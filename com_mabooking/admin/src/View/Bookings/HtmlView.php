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

	public array $summary = [];

	public function display($tpl = null): void
	{
		/** @var \Icc\Component\Mabooking\Administrator\Model\BookingsModel $model */
		$model = $this->getModel();
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->summary = $model->getSummary();

		$this->addToolbar();

		parent::display($tpl);
	}

	private function addToolbar(): void
	{
		ToolbarHelper::title('Bookings', 'address-book');
	}
}
