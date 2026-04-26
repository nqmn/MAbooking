<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mabooking
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

$month = (int) $this->calendar['month'];
$year = (int) $this->calendar['year'];
$daysInMonth = (int) $this->calendar['daysInMonth'];
$startWeekday = (int) $this->calendar['startWeekday'];
$days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
$prev = (new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)))->modify('-1 month');
$next = (new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)))->modify('+1 month');
$bookingDetails = [];

foreach ($this->bookings as $date => $entries)
{
	foreach ($entries as $booking)
	{
		$bookingDetails[$date][] = [
			'id' => (int) $booking->id,
			'date' => (string) $booking->booking_date,
			'event' => (string) ($booking->event_title ?: $booking->space_title . ' Booking'),
			'venue' => (string) $booking->venue_title,
			'space' => (string) $booking->space_title,
			'time' => substr((string) $booking->start_time, 0, 5) . ' - ' . substr((string) $booking->end_time, 0, 5),
			'status' => ucfirst((string) $booking->status),
			'client' => (string) $booking->client_name,
		];
	}
}
?>
<div class="iccbooking-shell">
	<div class="iccbooking-hero">
		<div>
			<p class="iccbooking-kicker">Venue Booking</p>
			<h1><?php echo htmlspecialchars($this->calendar['label'], ENT_QUOTES, 'UTF-8'); ?></h1>
			<p class="iccbooking-copy">Calendar-first booking flow adapted from the `app.html` mockup into a reusable Joomla component.</p>
		</div>
		<div class="iccbooking-nav">
			<a href="<?php echo Route::_('index.php?option=com_mabooking&view=calendar&month=' . $prev->format('n') . '&year=' . $prev->format('Y')); ?>">&larr; Previous</a>
			<a href="<?php echo Route::_('index.php?option=com_mabooking&view=calendar'); ?>">Today</a>
			<a href="<?php echo Route::_('index.php?option=com_mabooking&view=calendar&month=' . $next->format('n') . '&year=' . $next->format('Y')); ?>">Next &rarr;</a>
		</div>
	</div>

	<div class="iccbooking-panel">
		<div class="iccbooking-filterbar">
			<span class="is-active">All Bookings</span>
			<span><i class="dot dot--confirmed"></i> Confirmed</span>
			<span><i class="dot dot--pending"></i> Pending</span>
			<span><i class="dot dot--empty"></i> Available</span>
		</div>

		<div class="iccbooking-grid">
			<?php foreach ($days as $day) : ?>
				<div class="iccbooking-grid-head"><?php echo $day; ?></div>
			<?php endforeach; ?>

			<?php for ($i = 0; $i < $startWeekday; $i++) : ?>
				<div class="iccbooking-day iccbooking-day--empty"></div>
			<?php endfor; ?>

			<?php for ($day = 1; $day <= $daysInMonth; $day++) : ?>
				<?php $date = sprintf('%04d-%02d-%02d', $year, $month, $day); ?>
				<?php $hasBookings = !empty($this->bookings[$date]); ?>
				<?php $isTopHalf = ($startWeekday + $day - 1) < 14; ?>
				<div class="iccbooking-day<?php echo $hasBookings ? ' has-bookings' : ''; ?>" <?php echo $hasBookings ? 'role="button" tabindex="0" data-booking-date="' . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
					<div class="iccbooking-day-number"><?php echo $day; ?></div>
					<?php if ($hasBookings) : ?>
						<?php foreach ($this->bookings[$date] as $booking) : ?>
							<div class="iccbooking-entry iccbooking-entry--<?php echo htmlspecialchars($booking->status, ENT_QUOTES, 'UTF-8'); ?>">
								<strong><?php echo htmlspecialchars($booking->space_title, ENT_QUOTES, 'UTF-8'); ?></strong>
								<span><?php echo htmlspecialchars(substr($booking->start_time, 0, 5) . ' - ' . substr($booking->end_time, 0, 5), ENT_QUOTES, 'UTF-8'); ?></span>
							</div>
						<?php endforeach; ?>
						<div class="iccbooking-popover <?php echo $isTopHalf ? 'is-below' : 'is-above'; ?>">
							<div class="iccbooking-popover__head">
								<strong><?php echo htmlspecialchars((new DateTimeImmutable($date))->format('D, M j, Y'), ENT_QUOTES, 'UTF-8'); ?></strong>
								<span><?php echo count($this->bookings[$date]); ?> Booking<?php echo count($this->bookings[$date]) === 1 ? '' : 's'; ?></span>
							</div>
							<div class="iccbooking-popover__body">
								<?php foreach ($this->bookings[$date] as $booking) : ?>
									<article class="iccbooking-popover__item">
										<strong><?php echo htmlspecialchars($booking->venue_title, ENT_QUOTES, 'UTF-8'); ?></strong>
										<span>Booked sections: <?php echo htmlspecialchars($booking->space_title, ENT_QUOTES, 'UTF-8'); ?></span>
										<span>Client: <?php echo htmlspecialchars($booking->client_name, ENT_QUOTES, 'UTF-8'); ?></span>
										<span>Time: <?php echo htmlspecialchars(substr($booking->start_time, 0, 5) . ' - ' . substr($booking->end_time, 0, 5), ENT_QUOTES, 'UTF-8'); ?></span>
									</article>
								<?php endforeach; ?>
							</div>
							<div class="iccbooking-popover__foot">Click date for full details</div>
						</div>
					<?php else : ?>
						<div class="iccbooking-entry iccbooking-entry--empty">Available</div>
					<?php endif; ?>
				</div>
			<?php endfor; ?>
		</div>
	</div>

	<div class="iccbooking-details" id="iccbooking-details" hidden>
		<div class="iccbooking-details__head">
			<div>
				<p class="iccbooking-kicker">Schedule Details</p>
				<h2 id="iccbooking-details-title">Selected Date</h2>
			</div>
			<button type="button" class="iccbooking-details__close" id="iccbooking-details-close" aria-label="Close schedule details">&times;</button>
		</div>
		<div class="iccbooking-details__list" id="iccbooking-details-list"></div>
	</div>

	<div class="iccbooking-form-wrap">
		<div class="iccbooking-form-head">
			<div>
				<h2>New Booking</h2>
				<p>Grouped like the mockup: date and time, venue details, client information, and notes.</p>
			</div>
		</div>

		<form action="<?php echo Route::_('index.php?option=com_mabooking&task=booking.submit'); ?>" method="post" class="iccbooking-form">
			<div class="iccbooking-section">
				<h3>Date &amp; Time</h3>
				<div class="iccbooking-form-grid">
					<div><?php echo $this->form->renderField('booking_date'); ?></div>
					<div><?php echo $this->form->renderField('start_time'); ?></div>
					<div><?php echo $this->form->renderField('end_time'); ?></div>
					<div><?php echo $this->form->renderField('event_title'); ?></div>
				</div>
			</div>

			<div class="iccbooking-section">
				<h3>Venue Details</h3>
				<div class="iccbooking-form-grid">
					<div><?php echo $this->form->renderField('venue_id'); ?></div>
					<div><?php echo $this->form->renderField('space_id'); ?></div>
					<div><?php echo $this->form->renderField('attendees'); ?></div>
				</div>
			</div>

			<div class="iccbooking-section">
				<h3>Client Information</h3>
				<div class="iccbooking-form-grid">
					<div><?php echo $this->form->renderField('client_name'); ?></div>
					<div><?php echo $this->form->renderField('client_phone'); ?></div>
					<div><?php echo $this->form->renderField('client_email'); ?></div>
				</div>
			</div>

			<div class="iccbooking-section">
				<h3>Additional Information</h3>
				<div class="iccbooking-form-notes"><?php echo $this->form->renderField('notes'); ?></div>
			</div>

			<button type="submit" class="iccbooking-submit">Submit Booking Request</button>
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	</div>
</div>

<style>
.iccbooking-shell { display: grid; gap: 2rem; color: #1c2834; }
.iccbooking-hero { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; padding: 2rem; background: linear-gradient(135deg, #f6f8fb 0%, #e8eef5 100%); border: 1px solid #d9e2ec; border-radius: 1.5rem; }
.iccbooking-kicker { margin: 0 0 .45rem; font-size: .75rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: #4a7ba7; }
.iccbooking-hero h1 { margin: 0 0 .5rem; font-size: 2.2rem; }
.iccbooking-copy { margin: 0; max-width: 42rem; color: #516275; }
.iccbooking-nav { display: flex; gap: .75rem; flex-wrap: wrap; }
.iccbooking-nav a { text-decoration: none; padding: .75rem 1rem; border-radius: .8rem; border: 1px solid #d6dee8; background: #fff; color: #1c2834; font-weight: 700; }
.iccbooking-panel, .iccbooking-form-wrap { background: #fff; border: 1px solid #dce4ed; border-radius: 1.5rem; box-shadow: 0 14px 36px rgba(28, 40, 52, .05); overflow: hidden; }
.iccbooking-filterbar { display: flex; flex-wrap: wrap; gap: .75rem; padding: 1.2rem 1.5rem; border-bottom: 1px solid #edf2f7; }
.iccbooking-filterbar span { display: inline-flex; align-items: center; gap: .5rem; padding: .55rem .9rem; border-radius: 999px; background: #eef3f8; color: #486074; font-size: .8rem; font-weight: 700; }
.iccbooking-filterbar .is-active { background: #1c2834; color: #fff; }
.dot { width: .55rem; height: .55rem; border-radius: 50%; display: inline-block; }
.dot--confirmed { background: #16a34a; }
.dot--pending { background: #ea580c; }
.dot--empty { background: #94a3b8; }
.iccbooking-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: .85rem; padding: 1.5rem; background: #f8fafc; }
.iccbooking-grid-head { font-weight: 700; text-transform: uppercase; font-size: .75rem; color: #516275; text-align: center; letter-spacing: .08em; }
.iccbooking-day { min-height: 8.5rem; padding: .75rem; border: 1px solid #d8e0e8; border-radius: .95rem; background: #fff; display: grid; align-content: start; gap: .4rem; position: relative; }
.iccbooking-day.has-bookings { cursor: pointer; transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease; }
.iccbooking-day.has-bookings:hover,
.iccbooking-day.has-bookings.is-selected { border-color: #7a95b1; box-shadow: 0 12px 26px rgba(28, 40, 52, .12); transform: translateY(-1px); }
.iccbooking-day--empty { background: transparent; border-style: dashed; }
.iccbooking-day-number { font-weight: 700; color: #1c2834; }
.iccbooking-entry { font-size: .75rem; border-radius: .75rem; padding: .45rem .55rem; display: grid; gap: .15rem; }
.iccbooking-entry--confirmed { background: #ecfdf3; color: #166534; }
.iccbooking-entry--pending { background: #fff7ed; color: #9a3412; }
.iccbooking-entry--cancelled { background: #fef2f2; color: #991b1b; }
.iccbooking-entry--empty { background: #f8fafc; color: #64748b; }
.iccbooking-popover { position: absolute; left: 50%; z-index: 50; width: 16rem; border: 1px solid #d9e2ec; border-radius: .85rem; background: #fff; box-shadow: 0 22px 44px rgba(15, 23, 42, .22); opacity: 0; pointer-events: none; transform: translateX(-50%) translateY(4px); transition: opacity .16s ease, transform .16s ease; overflow: hidden; }
.iccbooking-day.has-bookings:hover .iccbooking-popover,
.iccbooking-day.has-bookings:focus-within .iccbooking-popover { opacity: 1; pointer-events: auto; transform: translateX(-50%) translateY(0); }
.iccbooking-popover.is-below { top: calc(100% + .65rem); }
.iccbooking-popover.is-above { bottom: calc(100% + .65rem); }
.iccbooking-popover__head { padding: .8rem .95rem; background: #64748b; color: #fff; display: grid; gap: .2rem; }
.iccbooking-popover__head strong { font-size: .86rem; }
.iccbooking-popover__head span { font-size: .72rem; color: #e5e7eb; }
.iccbooking-popover__body { max-height: 16rem; overflow-y: auto; background: #fff; }
.iccbooking-popover__item { display: grid; gap: .3rem; padding: .9rem; border-bottom: 1px solid #edf2f7; border-left: 4px solid #7a95b1; }
.iccbooking-popover__item strong { color: #1c2834; font-size: .85rem; }
.iccbooking-popover__item span { color: #516275; font-size: .75rem; }
.iccbooking-popover__foot { padding: .6rem .75rem; background: #f8fafc; color: #64748b; font-size: .7rem; text-align: center; border-top: 1px solid #edf2f7; }
.iccbooking-details { background: #fff; border: 1px solid #dce4ed; border-radius: 1.5rem; box-shadow: 0 14px 36px rgba(28, 40, 52, .05); overflow: hidden; }
.iccbooking-details__head { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; padding: 1.5rem; border-bottom: 1px solid #edf2f7; background: #f8fafc; }
.iccbooking-details__head h2 { margin: 0; font-size: 1.35rem; }
.iccbooking-details__close { width: 2rem; height: 2rem; border: 1px solid #d6dee8; border-radius: .55rem; background: #fff; color: #1c2834; font-size: 1.3rem; line-height: 1; cursor: pointer; }
.iccbooking-details__list { display: grid; gap: .85rem; padding: 1.5rem; }
.iccbooking-detail { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 1rem; padding: 1rem; border: 1px solid #edf2f7; border-left: 4px solid #7a95b1; border-radius: .9rem; background: #fff; }
.iccbooking-detail h3 { margin: 0 0 .35rem; font-size: 1rem; color: #1c2834; }
.iccbooking-detail p { margin: .2rem 0; color: #516275; font-size: .86rem; }
.iccbooking-detail__status { align-self: start; border-radius: 999px; padding: .35rem .7rem; background: #eef3f8; color: #486074; font-size: .72rem; font-weight: 700; text-transform: uppercase; }
.iccbooking-form-head { padding: 1.5rem 1.5rem 0; }
.iccbooking-form-head h2 { margin: 0 0 .35rem; }
.iccbooking-form-head p { margin: 0; color: #516275; }
.iccbooking-form { padding: 1.5rem; display: grid; gap: 1.5rem; }
.iccbooking-section h3 { margin: 0 0 .9rem; padding-bottom: .55rem; border-bottom: 1px solid #edf2f7; font-size: 1rem; }
.iccbooking-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
.iccbooking-submit { margin-top: .25rem; background: #1c2834; color: #fff; border: 0; border-radius: 999px; padding: .95rem 1.3rem; font-weight: 700; cursor: pointer; }
.iccbooking-form .control-group,
.iccbooking-form .mb-3 { margin-bottom: 0; }
@media (max-width: 900px) {
	.iccbooking-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
	.iccbooking-form-grid { grid-template-columns: 1fr; }
	.iccbooking-hero { flex-direction: column; align-items: flex-start; }
	.iccbooking-detail { grid-template-columns: 1fr; }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var venue = document.getElementById('jform_venue_id');
	var room = document.getElementById('jform_space_id');
	var spaceMap = <?php echo json_encode($this->spaces, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
	var bookingDetails = <?php echo json_encode($bookingDetails, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
	var detailsPanel = document.getElementById('iccbooking-details');
	var detailsTitle = document.getElementById('iccbooking-details-title');
	var detailsList = document.getElementById('iccbooking-details-list');
	var detailsClose = document.getElementById('iccbooking-details-close');

	var escapeHtml = function (value) {
		return String(value || '').replace(/[&<>"']/g, function (character) {
			return {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			}[character];
		});
	};

	var formatDate = function (dateValue) {
		var parts = String(dateValue).split('-');

		if (parts.length !== 3) {
			return dateValue;
		}

		var date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));

		return date.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
	};

	var showBookingDetails = function (date) {
		var entries = bookingDetails[date] || [];

		if (!detailsPanel || !detailsTitle || !detailsList || !entries.length) {
			return;
		}

		document.querySelectorAll('.iccbooking-day.is-selected').forEach(function (cell) {
			cell.classList.remove('is-selected');
		});

		var selectedCell = document.querySelector('.iccbooking-day[data-booking-date="' + date + '"]');

		if (selectedCell) {
			selectedCell.classList.add('is-selected');
		}

		detailsTitle.textContent = 'Schedule for ' + formatDate(date);
		detailsList.innerHTML = entries.map(function (entry) {
			return '<article class="iccbooking-detail">'
				+ '<div>'
				+ '<h3>' + escapeHtml(entry.event) + '</h3>'
				+ '<p><strong>Venue:</strong> ' + escapeHtml(entry.venue) + '</p>'
				+ '<p><strong>Room:</strong> ' + escapeHtml(entry.space) + '</p>'
				+ '<p><strong>Time:</strong> ' + escapeHtml(entry.time) + '</p>'
				+ '<p><strong>Client:</strong> ' + escapeHtml(entry.client) + '</p>'
				+ '</div>'
				+ '<span class="iccbooking-detail__status">' + escapeHtml(entry.status) + '</span>'
				+ '</article>';
		}).join('');
		detailsPanel.hidden = false;
		detailsPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
	};

	document.querySelectorAll('.iccbooking-day[data-booking-date]').forEach(function (cell) {
		cell.addEventListener('click', function () {
			showBookingDetails(cell.getAttribute('data-booking-date'));
		});
		cell.addEventListener('keydown', function (event) {
			if (event.key === 'Enter' || event.key === ' ') {
				event.preventDefault();
				cell.click();
			}
		});
	});

	if (detailsClose && detailsPanel) {
		detailsClose.addEventListener('click', function () {
			detailsPanel.hidden = true;
			document.querySelectorAll('.iccbooking-day.is-selected').forEach(function (cell) {
				cell.classList.remove('is-selected');
			});
		});
	}

	['jform_start_time', 'jform_end_time'].forEach(function (id) {
		var input = document.getElementById(id);

		if (!input) {
			return;
		}

		input.setAttribute('type', 'time');
		input.setAttribute('step', '60');
		input.setAttribute('placeholder', '');
	});

	if (venue && room) {
		var spaceToVenue = {};

		Object.keys(spaceMap).forEach(function (venueId) {
			(spaceMap[venueId] || []).forEach(function (space) {
				spaceToVenue[String(space.id)] = String(venueId);
			});
		});

		var syncRooms = function () {
			var selectedVenue = venue.value;
			var selectedRoom = room.value;

			Array.prototype.slice.call(room.options).forEach(function (option) {
				if (!option.value) {
					option.hidden = false;
					return;
				}

				var visible = !selectedVenue || spaceToVenue[String(option.value)] === String(selectedVenue);
				option.hidden = !visible;

				if (!visible && option.selected) {
					room.value = '';
				}
			});

			if (selectedRoom && room.value !== selectedRoom) {
				room.value = '';
			}
		};

		venue.addEventListener('change', syncRooms);
		syncRooms();
	}
});
</script>
