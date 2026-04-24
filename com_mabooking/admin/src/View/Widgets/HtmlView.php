<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Administrator\View\Widgets;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

class HtmlView extends BaseHtmlView
{
	public string $widgetUrl = '';

	public string $iframeCode = '';

	public string $menuItemLink = '';

	public string $icsUrl = '';

	public bool $icsEnabled = false;

	public function display($tpl = null): void
	{
		$params = ComponentHelper::getParams('com_mabooking');
		$siteRoot = $this->getSiteRoot();
		$this->widgetUrl = $siteRoot . 'index.php?option=com_mabooking&view=calendar&layout=widget&tmpl=component';
		$this->iframeCode = '<iframe src="' . $this->widgetUrl . '" loading="lazy" style="width:100%;min-height:760px;border:0;border-radius:16px;overflow:hidden;"></iframe>';
		$this->menuItemLink = 'index.php?option=com_mabooking&view=calendar&layout=widget';
		$this->icsUrl = $siteRoot . 'index.php?option=com_mabooking&task=calendar.ics';
		$this->icsEnabled = (int) $params->get('enable_public_ics') === 1;

		$this->addToolbar();
		parent::display($tpl);
	}

	private function addToolbar(): void
	{
		ToolbarHelper::title('Calendar Widget', 'link');
	}

	private function getSiteRoot(): string
	{
		$uri = Uri::root();

		if (str_contains($uri, '/administrator/'))
		{
			return str_replace('/administrator/', '/', $uri);
		}

		if (str_ends_with($uri, '/administrator'))
		{
			return substr($uri, 0, -13);
		}

		return $uri;
	}
}
