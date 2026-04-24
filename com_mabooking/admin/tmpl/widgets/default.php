<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

defined('_JEXEC') or die;
?>
<div class="mabooking-widgets">
	<div class="mabooking-widgets__hero">
		<h1>Calendar Widget</h1>
		<p><?php echo htmlspecialchars(\Joomla\CMS\Language\Text::_('COM_MABOOKING_WIDGETS_DESC'), ENT_QUOTES, 'UTF-8'); ?></p>
	</div>

	<div class="mabooking-widgets__grid">
		<div class="mabooking-widgets__card">
			<h2>Widget Link</h2>
			<p>Use this as a direct link or as a Joomla menu item target.</p>
			<textarea readonly rows="3" class="mabooking-widgets__code"><?php echo htmlspecialchars($this->widgetUrl, ENT_QUOTES, 'UTF-8'); ?></textarea>
		</div>

		<div class="mabooking-widgets__card">
			<h2>Iframe Embed</h2>
			<p>Paste this into a Custom HTML module, article, or external page.</p>
			<textarea readonly rows="5" class="mabooking-widgets__code"><?php echo htmlspecialchars($this->iframeCode, ENT_QUOTES, 'UTF-8'); ?></textarea>
		</div>

		<div class="mabooking-widgets__card">
			<h2>Joomla Route</h2>
			<p>Create a menu item or internal link with this component route.</p>
			<textarea readonly rows="2" class="mabooking-widgets__code"><?php echo htmlspecialchars($this->menuItemLink, ENT_QUOTES, 'UTF-8'); ?></textarea>
		</div>

		<div class="mabooking-widgets__card">
			<h2>Public ICS Feed</h2>
			<p>Copy this `.ics` URL into Google Calendar or any calendar app that supports internet calendars.</p>
			<?php if (!$this->icsEnabled) : ?>
				<p class="mabooking-widgets__warn">Public ICS is currently disabled. Enable it in component Options first.</p>
			<?php endif; ?>
			<textarea readonly rows="3" class="mabooking-widgets__code"><?php echo htmlspecialchars($this->icsUrl, ENT_QUOTES, 'UTF-8'); ?></textarea>
		</div>

		<div class="mabooking-widgets__card">
			<h2>Preview</h2>
			<p>Open the widget in a clean component layout.</p>
			<p><a class="mabooking-widgets__button" href="<?php echo htmlspecialchars($this->widgetUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">Open Widget Preview</a></p>
		</div>
	</div>
</div>

<style>
.mabooking-widgets { display: grid; gap: 1.5rem; color: #1c2834; }
.mabooking-widgets__hero { background: linear-gradient(135deg, #f6f8fb 0%, #e8eef5 100%); border: 1px solid #d9e2ec; border-radius: 20px; padding: 1.75rem; }
.mabooking-widgets__hero h1 { margin: 0 0 .35rem; font-size: 2rem; }
.mabooking-widgets__hero p { margin: 0; color: #516275; max-width: 60rem; }
.mabooking-widgets__grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
.mabooking-widgets__card { background: #fff; border: 1px solid #e4e9f0; border-radius: 18px; padding: 1.25rem; box-shadow: 0 10px 28px rgba(28, 40, 52, .05); }
.mabooking-widgets__card h2 { margin: 0 0 .35rem; font-size: 1.05rem; }
.mabooking-widgets__card p { margin: 0 0 .85rem; color: #667085; }
.mabooking-widgets__warn { color: #b45309 !important; font-weight: 700; }
.mabooking-widgets__code { width: 100%; border: 1px solid #d8dee8; border-radius: 12px; background: #f8fafc; padding: .9rem; font-family: Consolas, monospace; font-size: .84rem; color: #1c2834; resize: vertical; }
.mabooking-widgets__button { display: inline-block; padding: .8rem 1rem; border-radius: 12px; background: #314155; color: #fff; text-decoration: none; font-weight: 700; }
@media (max-width: 900px) {
	.mabooking-widgets__grid { grid-template-columns: 1fr; }
}
</style>
