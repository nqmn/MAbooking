<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mabooking
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

$langTag = Factory::getApplication()->getLanguage()->getTag();
$isMalay = $langTag === 'ms-MY';
$introTitle = $isMalay ? 'TANDAKAN TARIKH UNTUK DETIK GEMILANG' : 'MARK THE DATE FOR GREATNESS';
$introText = $isMalay
	? 'Lihat tarikh yang tersedia dan buat tempahan anda untuk merealisasikan acara istimewa anda.'
	: 'Discover available dates and make your reservation to bring your special occasion to life.';
$legendNote = $isMalay
	? 'Tarikh yang diserlahkan menunjukkan tempahan venue pada hari tersebut.'
	: 'Highlighted dates show venue bookings for that day.';
$todayLabel = $isMalay ? 'HARI INI' : 'TODAY';

$month = (int) $this->calendar['month'];
$year = (int) $this->calendar['year'];
$daysInMonth = (int) $this->calendar['daysInMonth'];
$startWeekday = (int) $this->calendar['startWeekday'];
$days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
$prev = (new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)))->modify('-1 month');
$next = (new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)))->modify('+1 month');

$legendColors = [
	'Grand Ballroom' => 'bg-blue',
	'Exhibition Hall' => 'bg-red',
	'Bougainvillea Room' => 'bg-green',
	'Town Hall' => 'bg-yellow',
];
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
<div class="mabooking-widget">
	<div class="mabooking-widget__intro">
		<h2><?php echo htmlspecialchars($introTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
		<p><?php echo htmlspecialchars($introText, ENT_QUOTES, 'UTF-8'); ?></p>
	</div>

	<div class="mabooking-widget__card">
		<div class="mabooking-widget__header">
			<a class="mabooking-widget__arrow" href="<?php echo Route::_('index.php?option=com_mabooking&view=calendar&layout=widget&tmpl=component&month=' . $prev->format('n') . '&year=' . $prev->format('Y')); ?>">&lsaquo;</a>
			<h3><?php echo htmlspecialchars(strtoupper($this->calendar['label']), ENT_QUOTES, 'UTF-8'); ?></h3>
			<a class="mabooking-widget__arrow" href="<?php echo Route::_('index.php?option=com_mabooking&view=calendar&layout=widget&tmpl=component&month=' . $next->format('n') . '&year=' . $next->format('Y')); ?>">&rsaquo;</a>
		</div>

		<div class="mabooking-widget__legend">
			<?php foreach ($legendColors as $label => $class) : ?>
				<span><i class="mabooking-dot <?php echo $class; ?>"></i><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span>
			<?php endforeach; ?>
			<p><?php echo htmlspecialchars($legendNote, ENT_QUOTES, 'UTF-8'); ?></p>
		</div>

		<div class="mabooking-widget__days">
			<?php foreach ($days as $day) : ?>
				<div><?php echo $day; ?></div>
			<?php endforeach; ?>
		</div>

		<div class="mabooking-widget__grid">
			<?php for ($i = 0; $i < $startWeekday; $i++) : ?>
				<div class="mabooking-widget__cell mabooking-widget__cell--blank"></div>
			<?php endfor; ?>

			<?php for ($day = 1; $day <= $daysInMonth; $day++) : ?>
				<?php
				$date = sprintf('%04d-%02d-%02d', $year, $month, $day);
				$isToday = $date === date('Y-m-d');
				$entries = $this->bookings[$date] ?? [];
				$isTopHalf = ($startWeekday + $day - 1) < 14;
				?>
				<div class="mabooking-widget__cell<?php echo $isToday ? ' is-today' : ''; ?><?php echo $entries ? ' has-bookings' : ''; ?>" <?php echo $entries ? 'role="button" tabindex="0" data-booking-date="' . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
					<span class="mabooking-widget__day"><?php echo $day; ?></span>
					<?php if ($isToday) : ?>
						<span class="mabooking-widget__today"><?php echo htmlspecialchars($todayLabel, ENT_QUOTES, 'UTF-8'); ?></span>
					<?php endif; ?>
					<?php foreach (array_slice($entries, 0, 2) as $entry) : ?>
						<?php
						$venueClass = $legendColors[$entry->venue_title] ?? 'bg-slate';
						?>
						<div class="mabooking-widget__booking">
							<i class="mabooking-dot <?php echo $venueClass; ?>"></i>
							<div>
								<strong><?php echo htmlspecialchars($entry->space_title, ENT_QUOTES, 'UTF-8'); ?></strong>
								<span><?php echo htmlspecialchars(substr($entry->start_time, 0, 5) . '-' . substr($entry->end_time, 0, 5), ENT_QUOTES, 'UTF-8'); ?></span>
							</div>
						</div>
					<?php endforeach; ?>
					<?php if ($entries) : ?>
						<div class="mabooking-widget__popover <?php echo $isTopHalf ? 'is-below' : 'is-above'; ?>">
							<div class="mabooking-widget__popover-head">
								<strong><?php echo htmlspecialchars((new DateTimeImmutable($date))->format('D, M j, Y'), ENT_QUOTES, 'UTF-8'); ?></strong>
								<span><?php echo count($entries); ?> Booking<?php echo count($entries) === 1 ? '' : 's'; ?></span>
							</div>
							<div class="mabooking-widget__popover-body">
								<?php foreach ($entries as $entry) : ?>
									<?php $venueClass = $legendColors[$entry->venue_title] ?? 'bg-slate'; ?>
									<article class="mabooking-widget__popover-item">
										<i class="mabooking-dot <?php echo htmlspecialchars($venueClass, ENT_QUOTES, 'UTF-8'); ?>"></i>
										<div>
											<strong><?php echo htmlspecialchars($entry->venue_title, ENT_QUOTES, 'UTF-8'); ?></strong>
											<span>Booked sections: <?php echo htmlspecialchars($entry->space_title, ENT_QUOTES, 'UTF-8'); ?></span>
											<span>Client: <?php echo htmlspecialchars($entry->client_name, ENT_QUOTES, 'UTF-8'); ?></span>
											<span>Time: <?php echo htmlspecialchars(substr($entry->start_time, 0, 5) . ' - ' . substr($entry->end_time, 0, 5), ENT_QUOTES, 'UTF-8'); ?></span>
										</div>
									</article>
								<?php endforeach; ?>
							</div>
							<div class="mabooking-widget__popover-foot">Click date for full details</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endfor; ?>
		</div>

		<div class="mabooking-widget__details" id="mabooking-widget-details" hidden>
			<div class="mabooking-widget__details-head">
				<h4 id="mabooking-widget-details-title">Schedule Details</h4>
				<button type="button" id="mabooking-widget-details-close" aria-label="Close schedule details">&times;</button>
			</div>
			<div class="mabooking-widget__details-list" id="mabooking-widget-details-list"></div>
		</div>
	</div>
</div>

<style>
body { margin: 0; background: #fff; }
.mabooking-widget { padding: 24px; font-family: Arial, sans-serif; color: #1c2834; background: #fff; }
.mabooking-widget__intro { text-align: center; max-width: 760px; margin: 0 auto 24px; }
.mabooking-widget__intro h2 { margin: 0 0 10px; font-size: 30px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
.mabooking-widget__intro p { margin: 0; color: #667085; font-size: 18px; }
.mabooking-widget__card { max-width: 1100px; margin: 0 auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 18px 18px 0 0; box-shadow: 0 14px 32px rgba(28, 40, 52, .08); overflow: hidden; }
.mabooking-widget__header { display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; background: #7a95b1; }
.mabooking-widget__header h3 { margin: 0; color: #fff; font-size: 20px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
.mabooking-widget__arrow { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 999px; background: #fff; color: #7a95b1; text-decoration: none; font-size: 28px; line-height: 1; }
.mabooking-widget__legend { padding: 16px 24px; border-bottom: 1px solid #f0f2f5; }
.mabooking-widget__legend span { display: inline-flex; align-items: center; gap: 8px; margin-right: 20px; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #374151; }
.mabooking-widget__legend p { margin: 4px 0 0; color: #98a2b3; font-size: 12px; font-style: italic; }
.mabooking-widget__days { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); text-align: center; padding: 12px 0; background: #1c2834; color: #fff; font-size: 12px; font-weight: 700; letter-spacing: .08em; }
.mabooking-widget__grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 1px; background: #e5e7eb; }
.mabooking-widget__cell { min-height: 112px; padding: 12px; background: #fff; position: relative; }
.mabooking-widget__cell.has-bookings { cursor: pointer; transition: box-shadow .2s ease, transform .2s ease; }
.mabooking-widget__cell.has-bookings:hover,
.mabooking-widget__cell.has-bookings.is-selected { z-index: 1; box-shadow: inset 0 0 0 2px #7a95b1, 0 12px 24px rgba(28, 40, 52, .12); transform: translateY(-1px); }
.mabooking-widget__cell--blank { background: #f9fafb; }
.mabooking-widget__cell.is-today { background: #f7fcf9; box-shadow: inset 0 0 0 1px #4ade80; }
.mabooking-widget__day { font-weight: 700; font-size: 14px; color: #1c2834; }
.mabooking-widget__today { position: absolute; top: 10px; right: 10px; font-size: 9px; font-weight: 700; color: #15803d; background: #dcfce7; border: 1px solid #86efac; padding: 2px 5px; border-radius: 4px; }
.mabooking-widget__booking { display: flex; gap: 8px; align-items: flex-start; margin-top: 8px; padding: 6px 8px; border-radius: 10px; background: #f8fafc; }
.mabooking-widget__booking strong { display: block; font-size: 12px; line-height: 1.25; }
.mabooking-widget__booking span { display: block; font-size: 11px; color: #667085; }
.mabooking-widget__popover { position: absolute; left: 50%; z-index: 60; width: 256px; border: 1px solid #d9e2ec; border-radius: 12px; background: #fff; box-shadow: 0 22px 44px rgba(15, 23, 42, .22); opacity: 0; pointer-events: none; transform: translateX(-50%) translateY(4px); transition: opacity .16s ease, transform .16s ease; overflow: hidden; }
.mabooking-widget__cell.has-bookings:hover .mabooking-widget__popover,
.mabooking-widget__cell.has-bookings:focus-within .mabooking-widget__popover { opacity: 1; pointer-events: auto; transform: translateX(-50%) translateY(0); }
.mabooking-widget__popover.is-below { top: calc(100% + 10px); }
.mabooking-widget__popover.is-above { bottom: calc(100% + 10px); }
.mabooking-widget__popover-head { padding: 12px 14px; background: #64748b; color: #fff; display: grid; gap: 3px; }
.mabooking-widget__popover-head strong { font-size: 13px; }
.mabooking-widget__popover-head span { color: #e5e7eb; font-size: 11px; }
.mabooking-widget__popover-body { max-height: 256px; overflow-y: auto; background: #fff; }
.mabooking-widget__popover-item { display: flex; align-items: flex-start; gap: 8px; padding: 12px; border-bottom: 1px solid #edf2f7; }
.mabooking-widget__popover-item strong { display: block; color: #1c2834; font-size: 13px; margin-bottom: 4px; }
.mabooking-widget__popover-item span { display: block; color: #667085; font-size: 11px; margin-top: 2px; }
.mabooking-widget__popover-foot { padding: 9px 12px; background: #f8fafc; color: #64748b; font-size: 11px; text-align: center; border-top: 1px solid #edf2f7; }
.mabooking-widget__details { border-top: 1px solid #e5e7eb; background: #fff; }
.mabooking-widget__details-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; padding: 18px 24px; background: #f8fafc; border-bottom: 1px solid #eef2f7; }
.mabooking-widget__details-head h4 { margin: 0; font-size: 18px; color: #1c2834; }
.mabooking-widget__details-head button { width: 30px; height: 30px; border: 1px solid #d6dee8; border-radius: 8px; background: #fff; color: #1c2834; font-size: 20px; line-height: 1; cursor: pointer; }
.mabooking-widget__details-list { display: grid; gap: 10px; padding: 18px 24px 24px; }
.mabooking-widget__detail { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 12px; padding: 12px; border: 1px solid #edf2f7; border-left: 4px solid #7a95b1; border-radius: 10px; }
.mabooking-widget__detail h5 { margin: 0 0 6px; font-size: 14px; color: #1c2834; }
.mabooking-widget__detail p { margin: 2px 0; font-size: 12px; color: #667085; }
.mabooking-widget__status { align-self: start; padding: 4px 8px; border-radius: 999px; background: #eef3f8; color: #486074; font-size: 10px; font-weight: 700; text-transform: uppercase; }
.mabooking-dot { width: 10px; height: 10px; border-radius: 999px; display: inline-block; margin-top: 3px; flex: 0 0 auto; }
.bg-blue { background: #3b82f6; }
.bg-red { background: #ef4444; }
.bg-green { background: #22c55e; }
.bg-yellow { background: #facc15; }
.bg-slate { background: #64748b; }
@media (max-width: 900px) {
	.mabooking-widget { padding: 12px; }
	.mabooking-widget__intro h2 { font-size: 22px; }
	.mabooking-widget__intro p { font-size: 15px; }
	.mabooking-widget__cell { min-height: 96px; padding: 8px; }
	.mabooking-widget__header { padding: 14px 16px; }
	.mabooking-widget__header h3 { font-size: 16px; letter-spacing: .08em; }
	.mabooking-widget__days { font-size: 10px; }
	.mabooking-widget__booking strong { font-size: 11px; }
	.mabooking-widget__booking span { font-size: 10px; }
	.mabooking-widget__detail { grid-template-columns: 1fr; }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var bookingDetails = <?php echo json_encode($bookingDetails, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
	var detailsPanel = document.getElementById('mabooking-widget-details');
	var detailsTitle = document.getElementById('mabooking-widget-details-title');
	var detailsList = document.getElementById('mabooking-widget-details-list');
	var detailsClose = document.getElementById('mabooking-widget-details-close');

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

	var showDetails = function (date) {
		var entries = bookingDetails[date] || [];

		if (!detailsPanel || !detailsTitle || !detailsList || !entries.length) {
			return;
		}

		document.querySelectorAll('.mabooking-widget__cell.is-selected').forEach(function (cell) {
			cell.classList.remove('is-selected');
		});

		var selectedCell = document.querySelector('.mabooking-widget__cell[data-booking-date="' + date + '"]');

		if (selectedCell) {
			selectedCell.classList.add('is-selected');
		}

		detailsTitle.textContent = 'Schedule for ' + formatDate(date);
		detailsList.innerHTML = entries.map(function (entry) {
			return '<article class="mabooking-widget__detail">'
				+ '<div>'
				+ '<h5>' + escapeHtml(entry.event) + '</h5>'
				+ '<p><strong>Venue:</strong> ' + escapeHtml(entry.venue) + '</p>'
				+ '<p><strong>Room:</strong> ' + escapeHtml(entry.space) + '</p>'
				+ '<p><strong>Time:</strong> ' + escapeHtml(entry.time) + '</p>'
				+ '<p><strong>Client:</strong> ' + escapeHtml(entry.client) + '</p>'
				+ '</div>'
				+ '<span class="mabooking-widget__status">' + escapeHtml(entry.status) + '</span>'
				+ '</article>';
		}).join('');
		detailsPanel.hidden = false;
		detailsPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
	};

	document.querySelectorAll('.mabooking-widget__cell[data-booking-date]').forEach(function (cell) {
		cell.addEventListener('click', function () {
			showDetails(cell.getAttribute('data-booking-date'));
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
			document.querySelectorAll('.mabooking-widget__cell.is-selected').forEach(function (cell) {
				cell.classList.remove('is-selected');
			});
		});
	}
});
</script>
