<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mabooking
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

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
?>
<div class="mabooking-widget">
	<div class="mabooking-widget__intro">
		<h2>MARK THE DATE FOR GREATNESS</h2>
		<p>Discover available dates and make your reservation to bring your special occasion to life.</p>
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
			<p>Highlighted dates show venue bookings for that day.</p>
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
				?>
				<div class="mabooking-widget__cell<?php echo $isToday ? ' is-today' : ''; ?>">
					<span class="mabooking-widget__day"><?php echo $day; ?></span>
					<?php if ($isToday) : ?>
						<span class="mabooking-widget__today">TODAY</span>
					<?php endif; ?>
					<?php foreach (array_slice($entries, 0, 2) as $entry) : ?>
						<?php
						$venueClass = $legendColors[$entry->venue_title] ?? 'bg-slate';
						?>
						<div class="mabooking-widget__booking">
							<i class="mabooking-dot <?php echo $venueClass; ?>"></i>
							<div>
								<strong><?php echo htmlspecialchars($entry->space_title, ENT_QUOTES, 'UTF-8'); ?></strong>
								<span><?php echo htmlspecialchars(substr($entry->start_time, 0, 5), ENT_QUOTES, 'UTF-8'); ?></span>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endfor; ?>
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
.mabooking-widget__cell--blank { background: #f9fafb; }
.mabooking-widget__cell.is-today { background: #f7fcf9; box-shadow: inset 0 0 0 1px #4ade80; }
.mabooking-widget__day { font-weight: 700; font-size: 14px; color: #1c2834; }
.mabooking-widget__today { position: absolute; top: 10px; right: 10px; font-size: 9px; font-weight: 700; color: #15803d; background: #dcfce7; border: 1px solid #86efac; padding: 2px 5px; border-radius: 4px; }
.mabooking-widget__booking { display: flex; gap: 8px; align-items: flex-start; margin-top: 8px; padding: 6px 8px; border-radius: 10px; background: #f8fafc; }
.mabooking-widget__booking strong { display: block; font-size: 12px; line-height: 1.25; }
.mabooking-widget__booking span { display: block; font-size: 11px; color: #667085; }
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
}
</style>
