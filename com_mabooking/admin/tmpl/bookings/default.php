<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo Route::_('index.php?option=com_mabooking&view=bookings'); ?>" method="post" name="adminForm" id="adminForm">
	<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

	<div class="table-responsive">
		<table class="table table-striped" id="bookingList">
			<thead>
				<tr>
					<th width="1%"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
					<th><?php echo HTMLHelper::_('searchtools.sort', 'Event', 'b.event_title', $listDirn, $listOrder); ?></th>
					<th><?php echo HTMLHelper::_('searchtools.sort', 'Date', 'b.booking_date', $listDirn, $listOrder); ?></th>
					<th>Time</th>
					<th>Venue / Room</th>
					<th>Client</th>
					<th>Status</th>
					<th width="1%"><?php echo HTMLHelper::_('searchtools.sort', 'ID', 'b.id', $listDirn, $listOrder); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<tr>
						<td><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
						<td>
							<a href="<?php echo Route::_('index.php?option=com_mabooking&task=booking.edit&id=' . (int) $item->id); ?>">
								<?php echo $this->escape($item->event_title); ?>
							</a>
						</td>
						<td><?php echo $this->escape($item->booking_date); ?></td>
						<td><?php echo $this->escape($item->start_time . ' - ' . $item->end_time); ?></td>
						<td>
							<div><?php echo $this->escape($item->venue_title); ?></div>
							<small class="text-muted"><?php echo $this->escape($item->space_title); ?></small>
						</td>
						<td>
							<div><?php echo $this->escape($item->client_name); ?></div>
							<small class="text-muted"><?php echo $this->escape($item->client_email); ?></small>
						</td>
						<td><span class="badge bg-secondary"><?php echo $this->escape(ucfirst($item->status)); ?></span></td>
						<td><?php echo (int) $item->id; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<?php echo $this->pagination->getListFooter(); ?>

	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
