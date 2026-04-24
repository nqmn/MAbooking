<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

$days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
$month = (int) $this->calendar['month'];
$year = (int) $this->calendar['year'];
$daysInMonth = (int) $this->calendar['daysInMonth'];
$startWeekday = (int) $this->calendar['startWeekday'];
$prev = (new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)))->modify('-1 month');
$next = (new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)))->modify('+1 month');
?>
<div class="iccbooking-admin">
	<div class="iccbooking-admin__header">
		<div>
			<h1>Admin Panel</h1>
			<p>Master calendar and booking overview styled from the `app.html` admin concept.</p>
		</div>
		<div class="iccbooking-admin__actions">
			<a class="iccbooking-admin__ghost" href="<?php echo Route::_('index.php?option=com_mabooking&view=bookings'); ?>">Bookings</a>
			<a class="iccbooking-admin__primary" href="<?php echo Route::_('index.php?option=com_mabooking&task=booking.add'); ?>">New Booking</a>
		</div>
	</div>

	<div class="iccbooking-admin__stats">
		<div class="iccbooking-admin__stat">
			<span>Total Bookings</span>
			<strong><?php echo (int) $this->summary['total']; ?></strong>
		</div>
		<div class="iccbooking-admin__stat">
			<span>Confirmed</span>
			<strong class="is-confirmed"><?php echo (int) $this->summary['confirmed']; ?></strong>
		</div>
		<div class="iccbooking-admin__stat">
			<span>Pending</span>
			<strong class="is-pending"><?php echo (int) $this->summary['pending']; ?></strong>
		</div>
		<div class="iccbooking-admin__stat">
			<span>Cancelled</span>
			<strong class="is-cancelled"><?php echo (int) $this->summary['cancelled']; ?></strong>
		</div>
	</div>

	<div class="iccbooking-panel">
		<div class="iccbooking-panel__top">
			<div>
				<h2><?php echo htmlspecialchars($this->calendar['label'], ENT_QUOTES, 'UTF-8'); ?></h2>
				<p>View all bookings in a single calendar, matching the card-and-pill structure from the mockup.</p>
			</div>
			<div class="iccbooking-panel__nav">
				<a href="<?php echo Route::_('index.php?option=com_mabooking&view=dashboard&month=' . $prev->format('n') . '&year=' . $prev->format('Y')); ?>">&larr;</a>
				<a href="<?php echo Route::_('index.php?option=com_mabooking&view=dashboard'); ?>">Today</a>
				<a href="<?php echo Route::_('index.php?option=com_mabooking&view=dashboard&month=' . $next->format('n') . '&year=' . $next->format('Y')); ?>">&rarr;</a>
			</div>
		</div>

		<div class="iccbooking-panel__filters">
			<span class="is-active">All Bookings</span>
			<span><i class="dot dot--confirmed"></i> Confirmed (<?php echo (int) $this->summary['confirmed']; ?>)</span>
			<span><i class="dot dot--pending"></i> Pending (<?php echo (int) $this->summary['pending']; ?>)</span>
			<span><i class="dot dot--cancelled"></i> Cancelled (<?php echo (int) $this->summary['cancelled']; ?>)</span>
		</div>

		<div class="iccbooking-grid">
			<?php foreach ($days as $day) : ?>
				<div class="iccbooking-grid__head"><?php echo $day; ?></div>
			<?php endforeach; ?>

			<?php for ($i = 0; $i < $startWeekday; $i++) : ?>
				<div class="iccbooking-grid__cell iccbooking-grid__cell--blank"></div>
			<?php endfor; ?>

			<?php for ($day = 1; $day <= $daysInMonth; $day++) : ?>
				<?php $date = sprintf('%04d-%02d-%02d', $year, $month, $day); ?>
				<div class="iccbooking-grid__cell" role="button" tabindex="0"
					data-booking-date="<?php echo $date; ?>"
					title="Add booking for <?php echo $date; ?>">
					<div class="iccbooking-grid__day"><?php echo $day; ?></div>
					<?php if (!empty($this->monthlyBookings[$date])) : ?>
						<?php foreach ($this->monthlyBookings[$date] as $booking) : ?>
							<div class="iccbooking-chip iccbooking-chip--<?php echo htmlspecialchars($booking->status, ENT_QUOTES, 'UTF-8'); ?>">
								<strong><?php echo htmlspecialchars($booking->space_title, ENT_QUOTES, 'UTF-8'); ?></strong>
								<span><?php echo htmlspecialchars(substr($booking->start_time, 0, 5), ENT_QUOTES, 'UTF-8'); ?></span>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<div class="iccbooking-chip iccbooking-chip--empty">+ Add booking</div>
					<?php endif; ?>
				</div>
			<?php endfor; ?>
		</div>

		<div class="iccbooking-legend">
			<div><i class="dot dot--confirmed"></i> Confirmed</div>
			<div><i class="dot dot--pending"></i> Pending</div>
			<div><i class="dot dot--cancelled"></i> Cancelled</div>
			<div><i class="dot dot--empty"></i> Empty</div>
		</div>
	</div>

	<div class="iccbooking-table">
		<div class="iccbooking-table__head">
			<h3>Upcoming Bookings</h3>
			<p>Compact list below the calendar for quick action, similar to the mockup’s secondary admin blocks.</p>
		</div>
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>Date</th>
						<th>Time</th>
						<th>Venue</th>
						<th>Room</th>
						<th>Client</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!$this->upcomingBookings) : ?>
						<tr>
							<td colspan="6" class="text-center text-muted">No upcoming bookings yet.</td>
						</tr>
					<?php else : ?>
						<?php foreach ($this->upcomingBookings as $booking) : ?>
							<tr>
								<td><?php echo htmlspecialchars($booking->booking_date, ENT_QUOTES, 'UTF-8'); ?></td>
								<td><?php echo htmlspecialchars($booking->start_time . ' - ' . $booking->end_time, ENT_QUOTES, 'UTF-8'); ?></td>
								<td><?php echo htmlspecialchars($booking->venue_title, ENT_QUOTES, 'UTF-8'); ?></td>
								<td><?php echo htmlspecialchars($booking->space_title, ENT_QUOTES, 'UTF-8'); ?></td>
								<td><?php echo htmlspecialchars($booking->client_name, ENT_QUOTES, 'UTF-8'); ?></td>
								<td><span class="iccbooking-badge iccbooking-badge--<?php echo htmlspecialchars($booking->status, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(ucfirst($booking->status), ENT_QUOTES, 'UTF-8'); ?></span></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('.iccbooking-grid__cell[data-booking-date]').forEach(function (cell) {
		cell.addEventListener('click', function () {
			var date = cell.getAttribute('data-booking-date');
			window.location.href = 'index.php?option=com_mabooking&task=booking.add&booking_date=' + date;
		});
		cell.addEventListener('keydown', function (e) {
			if (e.key === 'Enter') cell.click();
		});
	});
});
</script>
<style>
.iccbooking-admin { display: grid; gap: 1.5rem; color: #1c2834; }
.iccbooking-admin__header { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; }
.iccbooking-admin__header h1 { margin: 0 0 .35rem; font-size: 2rem; font-weight: 700; }
.iccbooking-admin__header p { margin: 0; color: #667085; }
.iccbooking-admin__actions { display: flex; gap: .75rem; flex-wrap: wrap; }
.iccbooking-admin__primary, .iccbooking-admin__ghost { padding: .8rem 1rem; border-radius: .7rem; text-decoration: none; font-weight: 700; }
.iccbooking-admin__primary { background: #314155; color: #fff; }
.iccbooking-admin__ghost { background: #fff; color: #314155; border: 1px solid #d8dee8; }
.iccbooking-admin__stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }
.iccbooking-admin__stat { background: #fff; border: 1px solid #e4e9f0; border-radius: 1rem; padding: 1.2rem 1.25rem; box-shadow: 0 8px 24px rgba(28, 40, 52, .04); }
.iccbooking-admin__stat span { display: block; font-size: .78rem; color: #6b7280; text-transform: uppercase; letter-spacing: .08em; }
.iccbooking-admin__stat strong { display: block; margin-top: .65rem; font-size: 2rem; }
.iccbooking-admin__stat .is-confirmed { color: #15803d; }
.iccbooking-admin__stat .is-pending { color: #c2410c; }
.iccbooking-admin__stat .is-cancelled { color: #b91c1c; }
.iccbooking-panel, .iccbooking-table { background: #fff; border: 1px solid #e4e9f0; border-radius: 1.2rem; box-shadow: 0 10px 28px rgba(28, 40, 52, .05); overflow: hidden; }
.iccbooking-panel__top { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1.5rem; border-bottom: 1px solid #edf2f7; }
.iccbooking-panel__top h2 { margin: 0 0 .3rem; font-size: 1.5rem; }
.iccbooking-panel__top p, .iccbooking-table__head p { margin: 0; color: #667085; }
.iccbooking-panel__nav { display: flex; gap: .6rem; align-items: center; }
.iccbooking-panel__nav a { text-decoration: none; color: #314155; padding: .55rem .8rem; border: 1px solid #d8dee8; border-radius: .6rem; background: #fff; }
.iccbooking-panel__filters { display: flex; flex-wrap: wrap; gap: .75rem; padding: 1rem 1.5rem; border-bottom: 1px solid #edf2f7; }
.iccbooking-panel__filters span { display: inline-flex; align-items: center; gap: .55rem; padding: .55rem .9rem; border-radius: 999px; background: #eef3f8; color: #486074; font-size: .78rem; font-weight: 700; }
.iccbooking-panel__filters .is-active { background: #1c2834; color: #fff; }
.dot { width: .55rem; height: .55rem; border-radius: 50%; display: inline-block; }
.dot--confirmed { background: #16a34a; }
.dot--pending { background: #ea580c; }
.dot--cancelled { background: #dc2626; }
.dot--empty { background: #94a3b8; }
.iccbooking-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: .8rem; padding: 1.5rem; background: #f8fafc; }
.iccbooking-grid__head { text-align: center; font-size: .78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .08em; }
.iccbooking-grid__cell { min-height: 9rem; display: grid; align-content: start; gap: .45rem; background: #fff; border: 1px solid #e2e8f0; border-radius: .9rem; padding: .75rem; cursor: pointer; transition: border-color .15s, box-shadow .15s; }
.iccbooking-grid__cell:hover { border-color: #314155; box-shadow: 0 4px 12px rgba(49,65,85,.12); }
.iccbooking-grid__cell--blank { background: transparent; border-style: dashed; }
.iccbooking-grid__day { font-weight: 700; color: #1c2834; }
.iccbooking-chip { display: grid; gap: .1rem; padding: .45rem .55rem; border-radius: .7rem; font-size: .72rem; line-height: 1.35; }
.iccbooking-chip--confirmed { background: #effaf3; color: #166534; }
.iccbooking-chip--pending { background: #fff5eb; color: #9a3412; }
.iccbooking-chip--cancelled { background: #fef2f2; color: #991b1b; }
.iccbooking-chip--empty { background: #f8fafc; color: #64748b; }
.iccbooking-legend { display: flex; flex-wrap: wrap; gap: 1rem; padding: 0 1.5rem 1.5rem; color: #64748b; font-size: .8rem; font-weight: 600; }
.iccbooking-table__head { padding: 1.5rem 1.5rem 0; }
.iccbooking-table__head h3 { margin: 0 0 .35rem; font-size: 1.15rem; }
.iccbooking-table table { margin: 1rem 0 0; }
.iccbooking-badge { display: inline-block; padding: .35rem .7rem; border-radius: 999px; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; }
.iccbooking-badge--confirmed { background: #effaf3; color: #166534; }
.iccbooking-badge--pending { background: #fff5eb; color: #9a3412; }
.iccbooking-badge--cancelled { background: #fef2f2; color: #991b1b; }
@media (max-width: 1100px) {
	.iccbooking-admin__stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
	.iccbooking-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
}
@media (max-width: 800px) {
	.iccbooking-admin__header, .iccbooking-panel__top { flex-direction: column; align-items: flex-start; }
	.iccbooking-admin__stats, .iccbooking-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
</style>
