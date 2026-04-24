<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Administrator\View\Venues;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	public array $venues = [];

	public array $venueOptions = [];

	public function display($tpl = null): void
	{
		/** @var \Icc\Component\Mabooking\Administrator\Model\VenuesModel $model */
		$model = $this->getModel();
		$this->venues = $model->getVenuesWithSpaces();
		$this->venueOptions = $model->getVenueOptions();

		$this->addToolbar();

		parent::display($tpl);
	}

	private function addToolbar(): void
	{
		ToolbarHelper::title('Venue Management', 'home');
		ToolbarHelper::preferences('com_mabooking');
	}
}
