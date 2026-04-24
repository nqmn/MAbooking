<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
$month = (int) $this->calendar['month'];
$year = (int) $this->calendar['year'];
$daysInMonth = (int) $this->calendar['daysInMonth'];
$startWeekday = (int) $this->calendar['startWeekday'];
$prev = (new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)))->modify('-1 month');
$next = (new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)))->modify('+1 month');
$today = (new DateTimeImmutable('today'))->format('Y-m-d');
$siteRoot = Uri::root();

$roomColors = [
	'Grand Ballroom' => 'is-blue',
	'Exhibition Hall' => 'is-red',
	'Bougainvillea Room' => 'is-green',
	'Town Hall' => 'is-gold',
];

$venueCounts = [];

foreach ($roomColors as $venueTitle => $colorClass)
{
	$venueCounts[$venueTitle] = 0;
}

foreach ($this->monthlyBookings as $entries)
{
	foreach ($entries as $booking)
	{
		$venueTitle = (string) ($booking->venue_title ?? '');

		if ($venueTitle === '')
		{
			continue;
		}

		if (!isset($venueCounts[$venueTitle]))
		{
			$venueCounts[$venueTitle] = 0;
			$roomColors[$venueTitle] = 'is-slate';
		}

		$venueCounts[$venueTitle]++;
	}
}

$renderStatusBadge = static function (string $status): string {
	$status = strtolower($status);
	$class = 'is-slate';

	if ($status === 'confirmed')
	{
		$class = 'is-green';
	}
	elseif ($status === 'pending')
	{
		$class = 'is-amber';
	}
	elseif ($status === 'cancelled')
	{
		$class = 'is-red';
	}

	return '<span class="mabooking-status ' . $class . '">' . htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') . '</span>';
};
?>
<div class="mabooking-admin">
	<div class="mabooking-admin__header">
		<div>
			<h1>Admin Panel</h1>
			<p>Manage venue bookings and reservations.</p>
		</div>
		<div class="mabooking-admin__actions">
			<a class="mabooking-button mabooking-button--ghost" href="<?php echo htmlspecialchars($siteRoot, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">Back to Site</a>
			<a class="mabooking-button mabooking-button--primary" href="<?php echo Route::_('index.php?option=com_mabooking&task=booking.add'); ?>">New Booking</a>
			<a class="mabooking-button mabooking-button--ghost" href="<?php echo Route::_('index.php?option=com_mabooking&view=bookings'); ?>">Manage Bookings</a>
		</div>
	</div>

	<div class="mabooking-shortcuts">
		<a class="mabooking-shortcut is-booking" href="<?php echo Route::_('index.php?option=com_mabooking&task=booking.add'); ?>">
			<span class="mabooking-shortcut__icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" focusable="false">
					<path d="M7 2.75a.75.75 0 0 1 .75.75V5h8.5V3.5a.75.75 0 0 1 1.5 0V5h.75A2.5 2.5 0 0 1 21 7.5v11A2.5 2.5 0 0 1 18.5 21h-13A2.5 2.5 0 0 1 3 18.5v-11A2.5 2.5 0 0 1 5.5 5h.75V3.5A.75.75 0 0 1 7 2.75zm-2.5 7v8.75c0 .55.45 1 1 1h13c.55 0 1-.45 1-1V9.75h-15zm7.5 2a.75.75 0 0 1 .75.75v1.75h1.75a.75.75 0 0 1 0 1.5h-1.75v1.75a.75.75 0 0 1-1.5 0v-1.75H9.5a.75.75 0 0 1 0-1.5h1.75V12.5a.75.75 0 0 1 .75-.75z" fill="currentColor"/>
				</svg>
			</span>
			<span class="mabooking-shortcut__body">
				<strong>New Booking</strong>
				<small>Create a booking request</small>
			</span>
		</a>
		<a class="mabooking-shortcut is-list" href="<?php echo Route::_('index.php?option=com_mabooking&view=bookings'); ?>">
			<span class="mabooking-shortcut__icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" focusable="false">
					<path d="M5 5.25A2.25 2.25 0 0 1 7.25 3h9.5A2.25 2.25 0 0 1 19 5.25v13.5A2.25 2.25 0 0 1 16.75 21h-9.5A2.25 2.25 0 0 1 5 18.75V5.25zm3.25 2a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5zm0 4a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5zm0 4a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-4.5z" fill="currentColor"/>
				</svg>
			</span>
			<span class="mabooking-shortcut__body">
				<strong>Bookings</strong>
				<small>Review and update records</small>
			</span>
		</a>
		<a class="mabooking-shortcut is-venue" href="<?php echo Route::_('index.php?option=com_mabooking&view=venues'); ?>">
			<span class="mabooking-shortcut__icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" focusable="false">
					<path d="M12 2.75 4 6.5v10.88c0 .4.24.77.61.92L12 21.25l7.39-2.95a1 1 0 0 0 .61-.92V6.5l-8-3.75zm0 1.66 5.83 2.73L12 9.87 6.17 7.14 12 4.41zm-6.5 4.1 5.75 2.69v8.17L5.5 17.08V8.51zm7.25 10.86V11.2l5.75-2.69v8.57l-5.75 2.29z" fill="currentColor"/>
				</svg>
			</span>
			<span class="mabooking-shortcut__body">
				<strong>Venues</strong>
				<small>Manage venues and rooms</small>
			</span>
		</a>
		<a class="mabooking-shortcut is-widget" href="<?php echo Route::_('index.php?option=com_mabooking&view=widgets'); ?>">
			<span class="mabooking-shortcut__icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" focusable="false">
					<path d="M4.5 4h6A1.5 1.5 0 0 1 12 5.5v6A1.5 1.5 0 0 1 10.5 13h-6A1.5 1.5 0 0 1 3 11.5v-6A1.5 1.5 0 0 1 4.5 4zm9 0h6A1.5 1.5 0 0 1 21 5.5v3A1.5 1.5 0 0 1 19.5 10h-6A1.5 1.5 0 0 1 12 8.5v-3A1.5 1.5 0 0 1 13.5 4zm0 7h6a1.5 1.5 0 0 1 1.5 1.5v6a1.5 1.5 0 0 1-1.5 1.5h-6a1.5 1.5 0 0 1-1.5-1.5v-6A1.5 1.5 0 0 1 13.5 11zm-9 4h6A1.5 1.5 0 0 1 12 16.5v3A1.5 1.5 0 0 1 10.5 21h-6A1.5 1.5 0 0 1 3 19.5v-3A1.5 1.5 0 0 1 4.5 15z" fill="currentColor"/>
				</svg>
			</span>
			<span class="mabooking-shortcut__body">
				<strong>Widgets</strong>
				<small>Open embed and widget tools</small>
			</span>
		</a>
		<a class="mabooking-shortcut is-settings" href="<?php echo Route::_('index.php?option=com_config&view=component&component=com_mabooking'); ?>">
			<span class="mabooking-shortcut__icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" focusable="false">
					<path d="M12 2.75a1 1 0 0 1 .97.76l.45 1.84a6.96 6.96 0 0 1 1.45.6l1.62-.93a1 1 0 0 1 1.22.16l1.13 1.13a1 1 0 0 1 .16 1.22l-.93 1.62c.24.46.44.94.6 1.45l1.84.45a1 1 0 0 1 .76.97v1.6a1 1 0 0 1-.76.97l-1.84.45a6.96 6.96 0 0 1-.6 1.45l.93 1.62a1 1 0 0 1-.16 1.22l-1.13 1.13a1 1 0 0 1-1.22.16l-1.62-.93c-.46.24-.94.44-1.45.6l-.45 1.84a1 1 0 0 1-.97.76h-1.6a1 1 0 0 1-.97-.76l-.45-1.84a6.96 6.96 0 0 1-1.45-.6l-1.62.93a1 1 0 0 1-1.22-.16L5.1 18.84a1 1 0 0 1-.16-1.22l.93-1.62a6.96 6.96 0 0 1-.6-1.45l-1.84-.45a1 1 0 0 1-.76-.97v-1.6a1 1 0 0 1 .76-.97l1.84-.45c.16-.51.36-.99.6-1.45l-.93-1.62a1 1 0 0 1 .16-1.22L6.23 5.2a1 1 0 0 1 1.22-.16l1.62.93c.46-.24.94-.44 1.45-.6l.45-1.84a1 1 0 0 1 .97-.76h1.6zm0 6.25a3 3 0 1 0 0 6 3 3 0 0 0 0-6z" fill="currentColor"/>
				</svg>
			</span>
			<span class="mabooking-shortcut__body">
				<strong>Settings</strong>
				<small>Open component options</small>
			</span>
		</a>
	</div>

	<div class="mabooking-admin__controls">
		<div class="mabooking-search">
			<input type="search" id="mabooking-dashboard-search" placeholder="Search bookings..." aria-label="Search bookings">
		</div>
		<div class="mabooking-tabs" role="tablist" aria-label="Dashboard sections">
			<button type="button" class="mabooking-tab is-active" data-tab-target="master-calendar" role="tab" aria-selected="true">Master Calendar</button>
			<button type="button" class="mabooking-tab" data-tab-target="bookings" role="tab" aria-selected="false">Bookings</button>
			<button type="button" class="mabooking-tab" data-tab-target="upcoming-events" role="tab" aria-selected="false">Upcoming Events</button>
			<button type="button" class="mabooking-tab" data-tab-target="past-events" role="tab" aria-selected="false">Past Events</button>
		</div>
	</div>

	<section class="mabooking-pane is-active" data-tab-panel="master-calendar" role="tabpanel">
		<div class="mabooking-card">
			<div class="mabooking-card__top">
				<div class="mabooking-card__title">
					<h3><?php echo htmlspecialchars($this->calendar['label'], ENT_QUOTES, 'UTF-8'); ?></h3>
					<a class="mabooking-inline-button" href="<?php echo Route::_('index.php?option=com_mabooking&view=dashboard'); ?>">Today</a>
				</div>
				<div class="mabooking-nav">
					<a href="<?php echo Route::_('index.php?option=com_mabooking&view=dashboard&month=' . $prev->format('n') . '&year=' . $prev->format('Y')); ?>" aria-label="Previous month">&lsaquo;</a>
					<a href="<?php echo Route::_('index.php?option=com_mabooking&view=dashboard&month=' . $next->format('n') . '&year=' . $next->format('Y')); ?>" aria-label="Next month">&rsaquo;</a>
				</div>
			</div>

			<div class="mabooking-pills">
				<span class="mabooking-pill mabooking-pill--active">All Venues</span>
				<?php foreach ($venueCounts as $venueTitle => $count) : ?>
					<span class="mabooking-pill">
						<i class="<?php echo htmlspecialchars($roomColors[$venueTitle] ?? 'is-slate', ENT_QUOTES, 'UTF-8'); ?>"></i>
						<?php echo htmlspecialchars($venueTitle, ENT_QUOTES, 'UTF-8'); ?> (<?php echo (int) $count; ?>)
					</span>
				<?php endforeach; ?>
			</div>

			<div class="mabooking-grid-head">
				<?php foreach ($days as $day) : ?>
					<div><?php echo $day; ?></div>
				<?php endforeach; ?>
			</div>

			<div class="mabooking-grid">
				<?php for ($i = 0; $i < $startWeekday; $i++) : ?>
					<div class="mabooking-grid__cell mabooking-grid__cell--blank"></div>
				<?php endfor; ?>

				<?php for ($day = 1; $day <= $daysInMonth; $day++) : ?>
					<?php $date = sprintf('%04d-%02d-%02d', $year, $month, $day); ?>
					<?php $entries = $this->monthlyBookings[$date] ?? []; ?>
					<div class="mabooking-grid__cell<?php echo $date === $today ? ' is-today' : ''; ?>" role="button" tabindex="0" data-booking-date="<?php echo $date; ?>">
						<div class="mabooking-grid__day"><?php echo $day; ?></div>
						<?php foreach (array_slice($entries, 0, 3) as $booking) : ?>
							<?php $roomColor = $roomColors[$booking->venue_title] ?? 'is-slate'; ?>
							<div class="mabooking-calendar-entry">
								<span class="mabooking-calendar-entry__dot <?php echo $roomColor; ?>"></span>
								<div>
									<strong><?php echo htmlspecialchars($booking->space_title, ENT_QUOTES, 'UTF-8'); ?></strong>
									<span><?php echo htmlspecialchars(substr($booking->start_time, 0, 5) . '-' . substr($booking->end_time, 0, 5), ENT_QUOTES, 'UTF-8'); ?></span>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endfor; ?>
			</div>

			<div class="mabooking-legend">
				<div>
					<p>Venue Legend</p>
					<div class="mabooking-legend__items">
						<?php foreach ($venueCounts as $venueTitle => $count) : ?>
							<div><span class="mabooking-calendar-entry__dot <?php echo htmlspecialchars($roomColors[$venueTitle] ?? 'is-slate', ENT_QUOTES, 'UTF-8'); ?>"></span> <?php echo htmlspecialchars($venueTitle, ENT_QUOTES, 'UTF-8'); ?></div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="mabooking-pane" data-tab-panel="bookings" role="tabpanel" hidden>
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
						<?php foreach ($this->allBookings as $booking) : ?>
							<?php $roomColor = $roomColors[$booking->venue_title] ?? 'is-slate'; ?>
							<tr data-search-row>
								<td>
									<div class="mabooking-table__primary"><?php echo htmlspecialchars($booking->booking_date, ENT_QUOTES, 'UTF-8'); ?></div>
									<div class="mabooking-table__secondary"><?php echo htmlspecialchars(substr($booking->start_time, 0, 5) . ' - ' . substr($booking->end_time, 0, 5), ENT_QUOTES, 'UTF-8'); ?></div>
								</td>
								<td>
									<div class="mabooking-room">
										<span class="mabooking-calendar-entry__dot <?php echo $roomColor; ?>"></span>
										<span class="mabooking-table__primary"><?php echo htmlspecialchars($booking->space_title, ENT_QUOTES, 'UTF-8'); ?></span>
									</div>
									<div class="mabooking-table__secondary"><?php echo htmlspecialchars($booking->venue_title, ENT_QUOTES, 'UTF-8'); ?></div>
								</td>
								<td><?php echo htmlspecialchars($booking->client_name, ENT_QUOTES, 'UTF-8'); ?></td>
								<td>
									<div class="mabooking-table__secondary"><?php echo htmlspecialchars($booking->client_phone, ENT_QUOTES, 'UTF-8'); ?></div>
									<div class="mabooking-table__secondary"><?php echo htmlspecialchars($booking->client_email, ENT_QUOTES, 'UTF-8'); ?></div>
								</td>
								<td><?php echo $renderStatusBadge((string) $booking->status); ?></td>
								<td class="is-center">
									<a class="mabooking-row-link" href="<?php echo Route::_('index.php?option=com_mabooking&task=booking.edit&id=' . (int) $booking->id); ?>">Edit</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>

	<section class="mabooking-pane" data-tab-panel="upcoming-events" role="tabpanel" hidden>
		<div class="mabooking-section-heading">
			<h2>Upcoming Events</h2>
			<p>Bookings from today onward.</p>
		</div>
		<div class="mabooking-card">
			<div class="mabooking-table-wrap">
				<table class="mabooking-table">
					<thead>
						<tr>
							<th>Date</th>
							<th>Event</th>
							<th>Venue / Room</th>
							<th>Client</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!$this->upcomingBookings) : ?>
							<tr><td colspan="5" class="mabooking-empty">No upcoming events.</td></tr>
						<?php else : ?>
							<?php foreach ($this->upcomingBookings as $booking) : ?>
								<tr data-search-row>
									<td>
										<div class="mabooking-table__primary"><?php echo htmlspecialchars($booking->booking_date, ENT_QUOTES, 'UTF-8'); ?></div>
										<div class="mabooking-table__secondary"><?php echo htmlspecialchars(substr($booking->start_time, 0, 5) . ' - ' . substr($booking->end_time, 0, 5), ENT_QUOTES, 'UTF-8'); ?></div>
									</td>
									<td><?php echo htmlspecialchars($booking->event_title, ENT_QUOTES, 'UTF-8'); ?></td>
									<td>
										<div class="mabooking-table__primary"><?php echo htmlspecialchars($booking->space_title, ENT_QUOTES, 'UTF-8'); ?></div>
										<div class="mabooking-table__secondary"><?php echo htmlspecialchars($booking->venue_title, ENT_QUOTES, 'UTF-8'); ?></div>
									</td>
									<td><?php echo htmlspecialchars($booking->client_name, ENT_QUOTES, 'UTF-8'); ?></td>
									<td><?php echo $renderStatusBadge((string) $booking->status); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>

	<section class="mabooking-pane" data-tab-panel="past-events" role="tabpanel" hidden>
		<div class="mabooking-section-heading">
			<h2>Past Events</h2>
			<p>Bookings that already took place.</p>
		</div>
		<div class="mabooking-card">
			<div class="mabooking-table-wrap">
				<table class="mabooking-table">
					<thead>
						<tr>
							<th>Date</th>
							<th>Event</th>
							<th>Venue / Room</th>
							<th>Client</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!$this->pastBookings) : ?>
							<tr><td colspan="5" class="mabooking-empty">No past events.</td></tr>
						<?php else : ?>
							<?php foreach ($this->pastBookings as $booking) : ?>
								<tr data-search-row>
									<td>
										<div class="mabooking-table__primary"><?php echo htmlspecialchars($booking->booking_date, ENT_QUOTES, 'UTF-8'); ?></div>
										<div class="mabooking-table__secondary"><?php echo htmlspecialchars(substr($booking->start_time, 0, 5) . ' - ' . substr($booking->end_time, 0, 5), ENT_QUOTES, 'UTF-8'); ?></div>
									</td>
									<td><?php echo htmlspecialchars($booking->event_title, ENT_QUOTES, 'UTF-8'); ?></td>
									<td>
										<div class="mabooking-table__primary"><?php echo htmlspecialchars($booking->space_title, ENT_QUOTES, 'UTF-8'); ?></div>
										<div class="mabooking-table__secondary"><?php echo htmlspecialchars($booking->venue_title, ENT_QUOTES, 'UTF-8'); ?></div>
									</td>
									<td><?php echo htmlspecialchars($booking->client_name, ENT_QUOTES, 'UTF-8'); ?></td>
									<td><?php echo $renderStatusBadge((string) $booking->status); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var tabs = document.querySelectorAll('.mabooking-tab');
	var panels = document.querySelectorAll('.mabooking-pane');
	var search = document.getElementById('mabooking-dashboard-search');

	tabs.forEach(function (tab) {
		tab.addEventListener('click', function () {
			var target = tab.getAttribute('data-tab-target');

			tabs.forEach(function (item) {
				item.classList.toggle('is-active', item === tab);
				item.setAttribute('aria-selected', item === tab ? 'true' : 'false');
			});

			panels.forEach(function (panel) {
				var isActive = panel.getAttribute('data-tab-panel') === target;
				panel.classList.toggle('is-active', isActive);
				panel.hidden = !isActive;
			});
		});
	});

	document.querySelectorAll('.mabooking-grid__cell[data-booking-date]').forEach(function (cell) {
		cell.addEventListener('click', function () {
			var date = cell.getAttribute('data-booking-date');
			window.location.href = 'index.php?option=com_mabooking&task=booking.add&booking_date=' + date;
		});
		cell.addEventListener('keydown', function (e) {
			if (e.key === 'Enter') {
				cell.click();
			}
		});
	});

	if (search) {
		search.addEventListener('input', function () {
			var query = search.value.toLowerCase().trim();

			document.querySelectorAll('[data-search-row]').forEach(function (row) {
				var text = row.textContent.toLowerCase();
				row.hidden = query !== '' && text.indexOf(query) === -1;
			});
		});
	}
});
</script>
<style>
.mabooking-admin { color: #1c2834; max-width: 1200px; margin: 0 auto; padding: 1.5rem; display: grid; gap: 1.5rem; background: rgba(248, 250, 252, .85); }
.mabooking-admin__header, .mabooking-admin__controls, .mabooking-card__top { display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
.mabooking-admin__header h1, .mabooking-section-heading h2 { margin: 0; font-size: 2rem; font-weight: 700; letter-spacing: -.02em; }
.mabooking-admin__header p, .mabooking-section-heading p { margin: .3rem 0 0; color: #6b7280; font-size: .9rem; }
.mabooking-admin__actions { display: flex; flex-wrap: wrap; gap: .75rem; }
.mabooking-shortcuts { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 1rem; }
.mabooking-shortcut { position: relative; display: flex; align-items: center; gap: .95rem; padding: 1rem 1.05rem; border-radius: 1rem; text-decoration: none; background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(15, 23, 42, .06); transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
.mabooking-shortcut:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(15, 23, 42, .08); border-color: #cbd5e1; }
.mabooking-shortcut__icon { width: 3rem; height: 3rem; display: inline-flex; align-items: center; justify-content: center; border-radius: .95rem; flex: 0 0 auto; }
.mabooking-shortcut__icon svg { width: 1.4rem; height: 1.4rem; display: block; }
.mabooking-shortcut__body { display: grid; gap: .2rem; min-width: 0; }
.mabooking-shortcut__body strong { color: #1c2834; font-size: .9rem; line-height: 1.2; }
.mabooking-shortcut__body small { color: #6b7280; font-size: .75rem; line-height: 1.35; }
.mabooking-shortcut.is-booking .mabooking-shortcut__icon { background: #eff6ff; color: #2563eb; }
.mabooking-shortcut.is-list .mabooking-shortcut__icon { background: #f8fafc; color: #475569; }
.mabooking-shortcut.is-venue .mabooking-shortcut__icon { background: #ecfdf5; color: #16a34a; }
.mabooking-shortcut.is-widget .mabooking-shortcut__icon { background: #fff7ed; color: #ea580c; }
.mabooking-shortcut.is-settings .mabooking-shortcut__icon { background: #f5f3ff; color: #7c3aed; }
.mabooking-button, .mabooking-inline-button { text-decoration: none; font-weight: 700; font-size: .75rem; border-radius: .55rem; transition: background-color .2s, border-color .2s, color .2s; }
.mabooking-button { padding: .8rem 1rem; }
.mabooking-button--primary { background: #314155; color: #fff; border: 1px solid #314155; }
.mabooking-button--primary:hover { background: #1c2834; border-color: #1c2834; color: #fff; }
.mabooking-button--ghost, .mabooking-inline-button { background: #fff; color: #4b5563; border: 1px solid #e5e7eb; }
.mabooking-inline-button { padding: .45rem .8rem; }
.mabooking-admin__controls { flex-wrap: wrap; align-items: flex-start; }
.mabooking-search { max-width: 22rem; width: 100%; }
.mabooking-search input { width: 100%; border: 1px solid #e5e7eb; border-radius: .65rem; background: #fff; padding: .78rem 1rem; font-size: .92rem; box-shadow: 0 1px 2px rgba(15, 23, 42, .05); }
.mabooking-tabs { display: inline-flex; flex-wrap: wrap; border: 1px solid #e5e7eb; border-radius: 999px; padding: .25rem; background: #fff; box-shadow: 0 1px 2px rgba(15, 23, 42, .06); }
.mabooking-tab { border: 0; background: transparent; color: #6b7280; padding: .7rem 1.2rem; border-radius: 999px; font-size: .75rem; font-weight: 700; }
.mabooking-tab.is-active { background: #314155; color: #fff; box-shadow: 0 1px 2px rgba(15, 23, 42, .12); }
.mabooking-pane { display: none; gap: 1rem; }
.mabooking-pane.is-active { display: grid; }
.mabooking-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 1rem; box-shadow: 0 1px 3px rgba(15, 23, 42, .06); overflow: hidden; }
.mabooking-card__top { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; }
.mabooking-card__title { display: flex; align-items: center; gap: .9rem; flex-wrap: wrap; }
.mabooking-card__title h3 { margin: 0; font-size: 1.35rem; }
.mabooking-nav { display: flex; gap: .5rem; }
.mabooking-nav a { width: 2rem; height: 2rem; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; border-radius: .5rem; color: #6b7280; text-decoration: none; background: #fff; font-size: 1.2rem; }
.mabooking-pills { display: flex; flex-wrap: wrap; gap: .75rem; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
.mabooking-pill { display: inline-flex; align-items: center; gap: .5rem; padding: .55rem .95rem; border-radius: 999px; font-size: .76rem; font-weight: 700; }
.mabooking-pill i { width: .5rem; height: .5rem; border-radius: 999px; display: inline-block; }
.mabooking-pill--active { background: #1c2834; color: #fff; }
.mabooking-pill--blue { background: #eff6ff; color: #2563eb; }
.mabooking-pill--blue i { background: #3b82f6; }
.mabooking-pill--amber { background: #fff7ed; color: #d97706; }
.mabooking-pill--amber i { background: #f59e0b; }
.mabooking-pill--green { background: #ecfdf5; color: #16a34a; }
.mabooking-pill--green i { background: #22c55e; }
.mabooking-grid-head { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); text-align: center; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
.mabooking-grid-head div { font-size: .75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .08em; }
.mabooking-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: .75rem; padding: 1.5rem; background: #fff; }
.mabooking-grid__cell { min-height: 7rem; border: 1px solid #f1f5f9; border-radius: .85rem; padding: .65rem; display: grid; align-content: start; gap: .4rem; cursor: pointer; }
.mabooking-grid__cell--blank { border-color: transparent; background: transparent; cursor: default; }
.mabooking-grid__cell.is-today { background: #f8fafc; border-color: #9ca3af; box-shadow: inset 0 0 0 1px #d1d5db; }
.mabooking-grid__day { font-size: .78rem; font-weight: 700; color: #4b5563; }
.mabooking-calendar-entry { display: flex; align-items: flex-start; gap: .45rem; font-size: .68rem; background: #f8fafc; border-radius: .65rem; padding: .4rem .45rem; }
.mabooking-calendar-entry strong, .mabooking-table__primary { display: block; color: #1c2834; font-weight: 700; }
.mabooking-calendar-entry span, .mabooking-table__secondary { color: #6b7280; font-size: .74rem; }
.mabooking-calendar-entry__dot { width: .55rem; height: .55rem; border-radius: 999px; margin-top: .22rem; flex: 0 0 auto; }
.is-blue { background: #3b82f6; }
.is-red { background: #ef4444; }
.is-green { background: #22c55e; }
.is-gold { background: #f59e0b; }
.is-slate { background: #64748b; }
.mabooking-legend { padding: 1.5rem; border-top: 1px solid #f1f5f9; background: #f8fafc; }
.mabooking-legend p { margin: 0 0 .75rem; font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; }
.mabooking-legend__items { display: flex; flex-wrap: wrap; gap: 2rem; font-size: .8rem; color: #4b5563; }
.mabooking-legend__items div { display: flex; align-items: center; gap: .5rem; }
.mabooking-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }
.mabooking-stat { background: #fff; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(15, 23, 42, .06); min-height: 8rem; display: grid; align-content: space-between; }
.mabooking-stat span { color: #6b7280; font-size: .76rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; }
.mabooking-stat strong { font-size: 2rem; }
.is-green-text { color: #22c55e; }
.is-amber-text { color: #f59e0b; }
.is-red-text { color: #ef4444; }
.mabooking-table-wrap { overflow-x: auto; }
.mabooking-table { width: 100%; border-collapse: collapse; min-width: 760px; }
.mabooking-table thead { background: #f8fafc; }
.mabooking-table th { text-align: left; font-size: .73rem; color: #6b7280; text-transform: uppercase; letter-spacing: .08em; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
.mabooking-table td { padding: 1rem 1.5rem; border-top: 1px solid #f8fafc; vertical-align: top; }
.mabooking-table tbody tr:hover { background: #fafafa; }
.mabooking-room { display: flex; align-items: center; gap: .5rem; }
.mabooking-status { display: inline-flex; align-items: center; padding: .35rem .75rem; border-radius: 999px; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; }
.mabooking-status.is-green { background: #ecfdf5; color: #15803d; }
.mabooking-status.is-amber { background: #fff7ed; color: #c2410c; }
.mabooking-status.is-red { background: #fef2f2; color: #b91c1c; }
.mabooking-status.is-slate { background: #f1f5f9; color: #475569; }
.mabooking-row-link { color: #314155; text-decoration: none; font-weight: 700; }
.mabooking-empty, .is-center { text-align: center; }
@media (max-width: 1100px) {
	.mabooking-shortcuts { grid-template-columns: repeat(3, minmax(0, 1fr)); }
	.mabooking-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
	.mabooking-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
	.mabooking-grid-head { grid-template-columns: repeat(4, minmax(0, 1fr)); row-gap: .75rem; }
}
@media (max-width: 800px) {
	.mabooking-admin { padding: 1rem; }
	.mabooking-admin__header, .mabooking-admin__controls, .mabooking-card__top { flex-direction: column; align-items: flex-start; }
	.mabooking-shortcuts { grid-template-columns: 1fr; }
	.mabooking-stats, .mabooking-grid, .mabooking-grid-head { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
</style>
