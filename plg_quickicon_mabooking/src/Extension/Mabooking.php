<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Quickicon.mabooking
 */

namespace Icc\Plugin\Quickicon\Mabooking\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\Module\Quickicon\Administrator\Event\QuickIconsEvent;

final class Mabooking extends CMSPlugin implements SubscriberInterface
{
	protected $autoloadLanguage = true;

	public static function getSubscribedEvents(): array
	{
		return [
			'onGetIcons' => 'onGetIcons',
		];
	}

	public function onGetIcons(QuickIconsEvent $event): void
	{
		$context = $event->getContext();

		if (
			$context !== $this->params->get('context', 'mod_quickicon')
			|| !$this->getApplication()->getIdentity()->authorise('core.manage', 'com_mabooking')
		) {
			return;
		}

		$result = $event->getArgument('result', []);

		$result[] = [
			[
				'link'   => 'index.php?option=com_mabooking&view=dashboard',
				'image'  => 'icon-calendar',
				'text'   => Text::_('PLG_QUICKICON_MABOOKING_DASHBOARD'),
				'access' => true,
				'group'  => 'MOD_QUICKICON_SITE',
			],
			[
				'link'   => 'index.php?option=com_mabooking&task=booking.add',
				'image'  => 'icon-plus-circle',
				'text'   => Text::_('PLG_QUICKICON_MABOOKING_NEW_BOOKING'),
				'access' => true,
				'group'  => 'MOD_QUICKICON_SITE',
			],
			[
				'link'   => 'index.php?option=com_mabooking&view=bookings',
				'image'  => 'icon-address-book',
				'text'   => Text::_('PLG_QUICKICON_MABOOKING_BOOKINGS'),
				'access' => true,
				'group'  => 'MOD_QUICKICON_SITE',
			],
			[
				'link'   => 'index.php?option=com_mabooking&view=venues',
				'image'  => 'icon-home',
				'text'   => Text::_('PLG_QUICKICON_MABOOKING_VENUES'),
				'access' => true,
				'group'  => 'MOD_QUICKICON_SITE',
			],
			[
				'link'   => 'index.php?option=com_mabooking&view=widgets',
				'image'  => 'icon-grid',
				'text'   => Text::_('PLG_QUICKICON_MABOOKING_WIDGETS'),
				'access' => true,
				'group'  => 'MOD_QUICKICON_SITE',
			],
		];

		$event->setArgument('result', $result);
	}
}
