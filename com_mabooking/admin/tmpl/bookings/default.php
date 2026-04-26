<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

$search = (string) $this->state->get('filter.search');
$status = (string) $this->state->get('filter.status');
$encodedSearch = rawurlencode($search);
$searchSuffix = $search !== '' ? '&filter_search=' . $encodedSearch : '';
$roomColors = [
	'Grand Ballroom' => 'is-blue',
	'Exhibition Hall' => 'is-red',
	'Bougainvillea Room' => 'is-green',
	'Town Hall' => 'is-gold',
];

$renderStatusBadge = static function (string $value): string {
	$class = 'is-slate';

	if ($value === 'confirmed')
	{
		$class = 'is-green';
	}
	elseif ($value === 'pending')
	{
		$class = 'is-amber';
	}
	elseif ($value === 'cancelled')
	{
		$class = 'is-red';
	}

	return '<span class="mabooking-status ' . $class . '">' . htmlspecialchars(ucfirst($value), ENT_QUOTES, 'UTF-8') . '</span>';
};
?>
<div class="mabooking-bookings">
	<div class="mabooking-bookings__header">
		<div>
			<h1>Bookings</h1>
			<p>Manage all booking records using the same visual language as the dashboard booking tab.</p>
		</div>
		<div class="mabooking-bookings__actions">
			<a class="mabooking-button mabooking-button--primary" href="<?php echo Route::_('index.php?option=com_mabooking&task=booking.add'); ?>">New Booking</a>
		</div>
	</div>

	<form action="<?php echo Route::_('index.php?option=com_mabooking&view=bookings'); ?>" method="get" class="mabooking-bookings__filters">
		<input type="hidden" name="option" value="com_mabooking">
		<input type="hidden" name="view" value="bookings">
		<div class="mabooking-search">
			<input type="search" name="filter_search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search bookings...">
		</div>
		<div class="mabooking-filter-pills">
			<a href="<?php echo Route::_('index.php?option=com_mabooking&view=bookings&filter_status=' . $searchSuffix); ?>" class="mabooking-pill<?php echo $status === '' ? ' is-active' : ''; ?>">All</a>
			<a href="<?php echo Route::_('index.php?option=com_mabooking&view=bookings&filter_status=confirmed' . $searchSuffix); ?>" class="mabooking-pill mabooking-pill--green<?php echo $status === 'confirmed' ? ' is-active' : ''; ?>">Confirmed</a>
			<a href="<?php echo Route::_('index.php?option=com_mabooking&view=bookings&filter_status=pending' . $searchSuffix); ?>" class="mabooking-pill mabooking-pill--amber<?php echo $status === 'pending' ? ' is-active' : ''; ?>">Pending</a>
			<a href="<?php echo Route::_('index.php?option=com_mabooking&view=bookings&filter_status=cancelled' . $searchSuffix); ?>" class="mabooking-pill mabooking-pill--red<?php echo $status === 'cancelled' ? ' is-active' : ''; ?>">Cancelled</a>
		</div>
		<button type="submit" class="mabooking-button mabooking-button--ghost">Apply</button>
	</form>

	<div class="mabooking-stats">
		<div class="mabooking-stat">
			<span>Total Bookings</span>
			<strong><?php echo (int) $this->summary['total']; ?></strong>
		</div>
		<div class="mabooking-stat">
			<span>Confirmed</span>
			<strong class="is-green-text"><?php echo (int) $this->summary['confirmed']; ?></strong>
		</div>
		<div class="mabooking-stat">
			<span>Pending</span>
			<strong class="is-amber-text"><?php echo (int) $this->summary['pending']; ?></strong>
		</div>
		<div class="mabooking-stat">
			<span>Cancelled</span>
			<strong class="is-red-text"><?php echo (int) $this->summary['cancelled']; ?></strong>
		</div>
	</div>

	<div class="mabooking-card">
		<div class="mabooking-table-wrap">
			<table class="mabooking-table">
				<thead>
					<tr>
						<th>Date</th>
						<th>Venue / Room</th>
						<th>Client</th>
						<th>Contact</th>
						<th>Status</th>
						<th class="is-center">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!$this->items) : ?>
						<tr>
							<td colspan="6" class="mabooking-empty">No bookings found.</td>
						</tr>
					<?php else : ?>
						<?php foreach ($this->items as $item) : ?>
							<?php $roomColor = $roomColors[$item->venue_title] ?? 'is-slate'; ?>
							<tr>
								<td>
									<div class="mabooking-table__primary"><?php echo htmlspecialchars($item->booking_date, ENT_QUOTES, 'UTF-8'); ?></div>
									<div class="mabooking-table__secondary"><?php echo htmlspecialchars(substr($item->start_time, 0, 5) . ' - ' . substr($item->end_time, 0, 5), ENT_QUOTES, 'UTF-8'); ?></div>
								</td>
								<td>
									<div class="mabooking-room">
										<span class="mabooking-room-dot <?php echo $roomColor; ?>"></span>
										<span class="mabooking-table__primary"><?php echo htmlspecialchars($item->space_title, ENT_QUOTES, 'UTF-8'); ?></span>
									</div>
									<div class="mabooking-table__secondary"><?php echo htmlspecialchars($item->venue_title, ENT_QUOTES, 'UTF-8'); ?></div>
								</td>
								<td><?php echo htmlspecialchars($item->client_name, ENT_QUOTES, 'UTF-8'); ?></td>
								<td>
									<div class="mabooking-table__secondary"><?php echo htmlspecialchars($item->client_phone, ENT_QUOTES, 'UTF-8'); ?></div>
									<div class="mabooking-table__secondary"><?php echo htmlspecialchars($item->client_email, ENT_QUOTES, 'UTF-8'); ?></div>
								</td>
								<td><?php echo $renderStatusBadge((string) $item->status); ?></td>
								<td class="is-center">
									<div class="mabooking-actions">
										<a class="mabooking-action-icon" href="<?php echo Route::_('index.php?option=com_mabooking&task=booking.edit&id=' . (int) $item->id); ?>" aria-label="Edit booking" title="Edit booking">
											<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
												<path d="M4 20.5V17l9.9-9.9 3.5 3.5L7.5 20.5H4zm11.7-12.8 1.4-1.4a1 1 0 0 1 1.4 0l1.8 1.8a1 1 0 0 1 0 1.4l-1.4 1.4-3.2-3.2z" fill="currentColor"/>
											</svg>
										</a>
										<form action="<?php echo Route::_('index.php?option=com_mabooking&view=bookings'); ?>" method="post" class="mabooking-inline-form" onsubmit="return confirm('Delete this booking?');">
											<input type="hidden" name="task" value="bookings.delete">
											<input type="hidden" name="cid[]" value="<?php echo (int) $item->id; ?>">
											<input type="hidden" name="boxchecked" value="1">
											<?php echo HTMLHelper::_('form.token'); ?>
											<button type="submit" class="mabooking-action-icon mabooking-action-icon--danger" aria-label="Delete booking" title="Delete booking">
												<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
													<path d="M9 3.75h6l.75 1.5H20a.75.75 0 0 1 0 1.5h-.9l-.8 11a2.25 2.25 0 0 1-2.24 2.09H8a2.25 2.25 0 0 1-2.24-2.09l-.8-11H4a.75.75 0 0 1 0-1.5h4.25L9 3.75zm1.2 4.5a.75.75 0 0 0-.75.75v6.75a.75.75 0 0 0 1.5 0V9a.75.75 0 0 0-.75-.75zm3.6 0a.75.75 0 0 0-.75.75v6.75a.75.75 0 0 0 1.5 0V9a.75.75 0 0 0-.75-.75z" fill="currentColor"/>
												</svg>
											</button>
										</form>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="mabooking-pagination">
			<?php echo $this->pagination->getListFooter(); ?>
		</div>
	</div>

	<footer class="ma360-footer">
		&copy; Developed by <a href="https://github.io/nqmn" target="_blank" rel="noopener">NQMN</a>
	</footer>
</div>

<style>
.mabooking-bookings { color: #1c2834; max-width: 1200px; margin: 0 auto; padding: 1.5rem; display: grid; gap: 1.5rem; }
.mabooking-bookings__header, .mabooking-bookings__filters { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; }
.mabooking-bookings__header h1 { margin: 0; font-size: 2rem; }
.mabooking-bookings__header p { margin: .35rem 0 0; color: #6b7280; }
.mabooking-button { text-decoration: none; border: 1px solid #dce4ed; border-radius: .65rem; padding: .8rem 1rem; font-weight: 700; font-size: .82rem; background: #fff; color: #314155; }
.mabooking-button--primary { background: #314155; border-color: #314155; color: #fff; }
.mabooking-search { max-width: 22rem; width: 100%; }
.mabooking-search input { width: 100%; border: 1px solid #e5e7eb; border-radius: .65rem; background: #fff; padding: .78rem 1rem; font-size: .92rem; }
.mabooking-filter-pills { display: flex; flex-wrap: wrap; gap: .5rem; }
.mabooking-pill { display: inline-flex; align-items: center; padding: .6rem .9rem; border-radius: 999px; text-decoration: none; background: #eef3f8; color: #486074; font-size: .75rem; font-weight: 700; }
.mabooking-pill.is-active { background: #1c2834; color: #fff; }
.mabooking-pill--green { background: #ecfdf5; color: #15803d; }
.mabooking-pill--amber { background: #fff7ed; color: #c2410c; }
.mabooking-pill--red { background: #fef2f2; color: #b91c1c; }
.mabooking-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }
.mabooking-stat { background: #fff; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(15, 23, 42, .06); min-height: 8rem; display: grid; align-content: space-between; }
.mabooking-stat span { color: #6b7280; font-size: .76rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; }
.mabooking-stat strong { font-size: 2rem; }
.is-green-text { color: #22c55e; }
.is-amber-text { color: #f59e0b; }
.is-red-text { color: #ef4444; }
.mabooking-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 1rem; box-shadow: 0 1px 3px rgba(15, 23, 42, .06); overflow: hidden; }
.mabooking-table-wrap { overflow-x: auto; }
.mabooking-table { width: 100%; border-collapse: collapse; min-width: 760px; }
.mabooking-table thead { background: #f8fafc; }
.mabooking-table th { text-align: left; font-size: .73rem; color: #6b7280; text-transform: uppercase; letter-spacing: .08em; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
.mabooking-table td { padding: 1rem 1.5rem; border-top: 1px solid #f8fafc; vertical-align: top; }
.mabooking-table tbody tr:hover { background: #fafafa; }
.mabooking-room { display: flex; align-items: center; gap: .5rem; }
.mabooking-room-dot { width: .55rem; height: .55rem; border-radius: 999px; flex: 0 0 auto; }
.is-blue { background: #3b82f6; }
.is-red { background: #ef4444; }
.is-green { background: #22c55e; }
.is-gold { background: #f59e0b; }
.is-slate { background: #64748b; }
.mabooking-table__primary { display: block; color: #1c2834; font-weight: 700; }
.mabooking-table__secondary { color: #6b7280; font-size: .74rem; }
.mabooking-status { display: inline-flex; align-items: center; padding: .35rem .75rem; border-radius: 999px; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; }
.mabooking-status.is-green { background: #ecfdf5; color: #15803d; }
.mabooking-status.is-amber { background: #fff7ed; color: #c2410c; }
.mabooking-status.is-red { background: #fef2f2; color: #b91c1c; }
.mabooking-status.is-slate { background: #f1f5f9; color: #475569; }
.mabooking-actions { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; }
.mabooking-action-icon { width: 2.25rem; height: 2.25rem; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #dce4ed; border-radius: .75rem; background: #fff; color: #314155; text-decoration: none; cursor: pointer; transition: border-color .2s ease, background-color .2s ease, color .2s ease, transform .2s ease; }
.mabooking-action-icon:hover { border-color: #314155; background: #f8fafc; color: #1c2834; transform: translateY(-1px); }
.mabooking-action-icon svg { width: 1rem; height: 1rem; display: block; }
.mabooking-action-icon--danger { color: #b91c1c; }
.mabooking-action-icon--danger:hover { border-color: #fecaca; background: #fef2f2; color: #991b1b; }
.mabooking-inline-form { display: inline; }
.mabooking-empty, .is-center { text-align: center; }
.mabooking-pagination { padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9; }
@media (max-width: 980px) {
	.mabooking-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 800px) {
	.mabooking-bookings { padding: 1rem; }
	.mabooking-stats { grid-template-columns: 1fr; }
}
.ma360-footer { text-align: center; font-size: .75rem; color: #9ca3af; padding: 1.5rem 0 .5rem; }
.ma360-footer a { color: #9ca3af; text-decoration: none; }
.ma360-footer a:hover { text-decoration: underline; }
</style>
