<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('bootstrap.tooltip');

$articleId = (int) ($this->item->article_id ?? 0);
$params = ComponentHelper::getParams('com_mabooking');
$articleSyncEnabled = (int) $params->get('enable_article_sync') === 1;
$isNew = empty($this->item->id);
$title = $isNew ? 'Create New Booking' : 'Edit Booking';
$subtitle = $isNew ? 'Fill in the details to block out a venue.' : 'Update the booking details using the same booking workflow.';
$form = $this->form;

$fieldValue = static function (string $name, $fallback = '') use ($form): string {
	$value = $form->getValue($name);

	if ($value === null || $value === '')
	{
		$value = $fallback;
	}

	return (string) $value;
};

$eventTitle = $fieldValue('event_title');
$bookingDate = $fieldValue('booking_date');
$startTime = substr($fieldValue('start_time', '09:00'), 0, 5);
$endTime = substr($fieldValue('end_time', '17:00'), 0, 5);
$venueId = $fieldValue('venue_id');
$spaceId = $fieldValue('space_id');
$attendees = $fieldValue('attendees', '0');
$notes = $fieldValue('notes');
$clientName = $fieldValue('client_name');
$clientPhone = $fieldValue('client_phone');
$clientEmail = $fieldValue('client_email');
$status = $fieldValue('status', 'pending');
$state = $fieldValue('state', '1');
$source = $fieldValue('source', 'admin');
$linkedArticleId = $fieldValue('article_id');

$statusField = $this->form->getField('status');
$stateField = $this->form->getField('state');
$sourceField = $this->form->getField('source');
?>
<form action="<?php echo Route::_('index.php?option=com_mabooking&layout=edit&id=' . (int) ($this->item->id ?? 0)); ?>" method="post" name="adminForm" id="booking-form" class="form-validate mabooking-booking-page">
	<div class="mabooking-booking-shell">
		<div class="mabooking-booking-card">
			<div class="mabooking-booking-card__header">
				<div>
					<h1><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
					<p><?php echo htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?></p>
				</div>
				<a class="mabooking-booking-close" href="<?php echo Route::_('index.php?option=com_mabooking&view=dashboard'); ?>" aria-label="Back to dashboard">&times;</a>
			</div>

			<div class="mabooking-booking-card__body">
				<div class="mabooking-section">
					<h2>Event Title</h2>
					<div class="mabooking-grid mabooking-grid--one">
						<div class="mabooking-field">
							<label for="jform_event_title">Event Title *</label>
							<input id="jform_event_title" name="jform[event_title]" type="text" value="<?php echo htmlspecialchars($eventTitle, ENT_QUOTES, 'UTF-8'); ?>" required class="required">
						</div>
					</div>
				</div>

				<div class="mabooking-section">
					<h2>Date &amp; Time</h2>
					<div class="mabooking-grid mabooking-grid--three">
						<div class="mabooking-field">
							<label for="jform_booking_date">Date *</label>
							<input id="jform_booking_date" name="jform[booking_date]" type="date" value="<?php echo htmlspecialchars($bookingDate, ENT_QUOTES, 'UTF-8'); ?>" required class="required">
						</div>
						<div class="mabooking-field">
							<label for="jform_start_time">Start Time *</label>
							<input id="jform_start_time" name="jform[start_time]" type="time" value="<?php echo htmlspecialchars($startTime, ENT_QUOTES, 'UTF-8'); ?>" step="60" required class="required">
						</div>
						<div class="mabooking-field">
							<label for="jform_end_time">End Time *</label>
							<input id="jform_end_time" name="jform[end_time]" type="time" value="<?php echo htmlspecialchars($endTime, ENT_QUOTES, 'UTF-8'); ?>" step="60" required class="required">
						</div>
					</div>
				</div>

				<div class="mabooking-section">
					<h2>Venue Details</h2>
					<div class="mabooking-stack">
						<div class="mabooking-field">
							<label for="jform_venue_id">Venue Category *</label>
							<div class="mabooking-select-wrap">
								<select id="jform_venue_id" name="jform[venue_id]" required class="required">
									<option value="">Select a venue category...</option>
									<?php foreach ($this->venues as $venue) : ?>
										<?php $value = (string) $venue->id; ?>
										<option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $value === $venueId ? ' selected' : ''; ?>>
											<?php echo htmlspecialchars((string) $venue->title, ENT_QUOTES, 'UTF-8'); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<span class="mabooking-select-arrow">▾</span>
							</div>
						</div>

						<div class="mabooking-field">
							<label for="jform_space_id">Room Selection *</label>
							<div class="mabooking-select-wrap">
								<select id="jform_space_id" name="jform[space_id]" required class="required">
									<option value="">Select a room...</option>
									<?php foreach ($this->spaces as $parentVenueId => $spaces) : ?>
										<?php foreach ($spaces as $space) : ?>
											<?php $value = (string) $space->id; ?>
											<option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" data-venue-id="<?php echo htmlspecialchars((string) $parentVenueId, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $value === $spaceId ? ' selected' : ''; ?>>
												<?php echo htmlspecialchars((string) $space->title, ENT_QUOTES, 'UTF-8'); ?>
											</option>
										<?php endforeach; ?>
									<?php endforeach; ?>
								</select>
								<span class="mabooking-select-arrow">▾</span>
							</div>
						</div>

						<div class="mabooking-grid mabooking-grid--two">
							<div class="mabooking-field">
								<label for="jform_attendees">Estimated Attendees</label>
								<input id="jform_attendees" name="jform[attendees]" type="number" min="0" value="<?php echo htmlspecialchars($attendees, ENT_QUOTES, 'UTF-8'); ?>">
							</div>
							<div class="mabooking-field">
								<label for="jform_source">Source</label>
								<div class="mabooking-select-wrap">
									<select id="jform_source" name="jform[source]">
										<?php foreach ($sourceField->options as $option) : ?>
											<?php $value = (string) ($option->value ?? ''); ?>
											<?php if ($value === '') : ?>
												<?php continue; ?>
											<?php endif; ?>
											<option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $value === $source ? ' selected' : ''; ?>>
												<?php echo htmlspecialchars((string) ($option->text ?? ''), ENT_QUOTES, 'UTF-8'); ?>
											</option>
										<?php endforeach; ?>
									</select>
									<span class="mabooking-select-arrow">▾</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="mabooking-section">
					<h2>Client Information</h2>
					<div class="mabooking-grid mabooking-grid--two">
						<div class="mabooking-field">
							<label for="jform_client_name">Client Name *</label>
							<input id="jform_client_name" name="jform[client_name]" type="text" value="<?php echo htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8'); ?>" required class="required">
						</div>
						<div class="mabooking-field">
							<label for="jform_client_phone">Client Phone *</label>
							<input id="jform_client_phone" name="jform[client_phone]" type="text" value="<?php echo htmlspecialchars($clientPhone, ENT_QUOTES, 'UTF-8'); ?>" required class="required">
						</div>
					</div>
					<div class="mabooking-grid mabooking-grid--two">
						<div class="mabooking-field">
							<label for="jform_client_email">Client Email *</label>
							<input id="jform_client_email" name="jform[client_email]" type="email" value="<?php echo htmlspecialchars($clientEmail, ENT_QUOTES, 'UTF-8'); ?>" required class="required validate-email">
						</div>
						<div class="mabooking-field">
							<label for="jform_status">Status</label>
							<div class="mabooking-select-wrap">
								<select id="jform_status" name="jform[status]">
									<?php foreach ($statusField->options as $option) : ?>
										<?php $value = (string) ($option->value ?? ''); ?>
										<?php if ($value === '') : ?>
											<?php continue; ?>
										<?php endif; ?>
										<option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $value === $status ? ' selected' : ''; ?>>
											<?php echo htmlspecialchars((string) ($option->text ?? ''), ENT_QUOTES, 'UTF-8'); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<span class="mabooking-select-arrow">▾</span>
							</div>
						</div>
					</div>
				</div>

				<div class="mabooking-section">
					<h2>Additional Information</h2>
					<div class="mabooking-field">
						<label for="jform_notes">Notes</label>
						<textarea id="jform_notes" name="jform[notes]" rows="3"><?php echo htmlspecialchars($notes, ENT_QUOTES, 'UTF-8'); ?></textarea>
					</div>
				</div>

				<div class="mabooking-section">
					<h2>Publishing</h2>
					<div class="mabooking-grid mabooking-grid--two">
						<div class="mabooking-field">
							<label for="jform_state">Published</label>
							<div class="mabooking-select-wrap">
								<select id="jform_state" name="jform[state]">
									<?php foreach ($stateField->options as $option) : ?>
										<?php $value = (string) ($option->value ?? ''); ?>
										<?php if ($value === '') : ?>
											<?php continue; ?>
										<?php endif; ?>
										<option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $value === $state ? ' selected' : ''; ?>>
											<?php echo htmlspecialchars((string) ($option->text ?? ''), ENT_QUOTES, 'UTF-8'); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<span class="mabooking-select-arrow">▾</span>
							</div>
						</div>
						<?php if ($articleSyncEnabled) : ?>
							<div class="mabooking-field">
								<label for="jform_article_id">Linked Article ID</label>
								<input id="jform_article_id" name="jform[article_id]" type="text" value="<?php echo htmlspecialchars($linkedArticleId, ENT_QUOTES, 'UTF-8'); ?>" readonly>
							</div>
						<?php endif; ?>
					</div>

					<?php if ($articleSyncEnabled && $articleId > 0) : ?>
						<div class="mabooking-linked">
							<a href="<?php echo Route::_('index.php?option=com_content&task=article.edit&id=' . $articleId); ?>">Edit Linked Joomla Article</a>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="mabooking-booking-card__footer">
				<button type="button" class="mabooking-footer-button mabooking-footer-button--ghost" onclick="Joomla.submitbutton('booking.cancel')">Cancel</button>
				<button type="button" class="mabooking-footer-button mabooking-footer-button--primary" onclick="Joomla.submitbutton('booking.save')"><?php echo $isNew ? 'Create Booking' : 'Save Booking'; ?></button>
			</div>
		</div>
	</div>

	<input type="hidden" name="jform[id]" value="<?php echo (int) ($this->item->id ?? 0); ?>">
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<footer class="ma360-footer">
	&copy; Developed by <a href="https://github.io/nqmn" target="_blank" rel="noopener">NQMN</a>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var venue = document.getElementById('jform_venue_id');
	var room = document.getElementById('jform_space_id');

	if (venue && room) {
		var options = Array.prototype.slice.call(room.options);

		var syncRooms = function () {
			var selectedVenue = venue.value;
			var selectedRoom = room.value;

			options.forEach(function (option) {
				if (!option.value) {
					option.hidden = false;
					return;
				}

				var roomVenue = option.getAttribute('data-venue-id');
				var visible = !selectedVenue || !roomVenue || roomVenue === selectedVenue;
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

<style>
.mabooking-booking-page { color: #1c2834; }
.mabooking-booking-shell { max-width: 980px; margin: 0 auto; padding: 1.5rem; }
.mabooking-booking-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 1rem; box-shadow: 0 20px 48px rgba(15, 23, 42, .12); overflow: hidden; }
.mabooking-booking-card__header { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1.5rem; border-bottom: 1px solid #f3f4f6; }
.mabooking-booking-card__header h1 { margin: 0; font-size: 1.25rem; font-weight: 700; }
.mabooking-booking-card__header p { margin: .35rem 0 0; color: #6b7280; font-size: .78rem; }
.mabooking-booking-close { width: 2.25rem; height: 2.25rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 999px; background: #f9fafb; color: #9ca3af; font-size: 1.4rem; text-decoration: none; }
.mabooking-booking-card__body { padding: 1.5rem; display: grid; gap: 2rem; }
.mabooking-section h2 { margin: 0 0 1rem; padding-bottom: .55rem; border-bottom: 1px solid #f3f4f6; font-size: .9rem; font-weight: 700; }
.mabooking-stack { display: grid; gap: 1rem; }
.mabooking-grid { display: grid; gap: 1.25rem; }
.mabooking-grid--one { grid-template-columns: 1fr; }
.mabooking-grid--two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.mabooking-grid--three { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.mabooking-field label { display: block; margin-bottom: .5rem; color: #4b5563; font-size: .76rem; font-weight: 700; }
.mabooking-field input,
.mabooking-field select,
.mabooking-field textarea { width: 100%; padding: .75rem .85rem; border: 1px solid #e5e7eb; border-radius: .4rem; background: #f9fafb; color: #1c2834; font-size: .9rem; transition: border-color .2s, background-color .2s; }
.mabooking-field input:focus,
.mabooking-field select:focus,
.mabooking-field textarea:focus { outline: none; border-color: #4a7ba7; background: #fff; }
.mabooking-field textarea { resize: vertical; min-height: 6rem; }
.mabooking-select-wrap { position: relative; }
.mabooking-select-wrap select { appearance: none; padding-right: 2.2rem; }
.mabooking-select-arrow { position: absolute; right: .85rem; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; }
.mabooking-linked { margin-top: 1rem; }
.mabooking-linked a { color: #314155; text-decoration: none; font-weight: 700; }
.mabooking-booking-card__footer { display: flex; justify-content: flex-end; gap: .75rem; padding: 1.5rem; border-top: 1px solid #f3f4f6; background: #f9fafb; }
.mabooking-footer-button { border: 1px solid #d1d5db; border-radius: .4rem; padding: .8rem 1.3rem; font-size: .9rem; font-weight: 700; transition: background-color .2s, border-color .2s, color .2s; }
.mabooking-footer-button--ghost { background: #fff; color: #374151; }
.mabooking-footer-button--primary { background: #1c2834; border-color: #1c2834; color: #fff; }
.mabooking-footer-button--primary:hover { background: #2c3d4f; border-color: #2c3d4f; }
@media (max-width: 900px) {
	.mabooking-grid--two,
	.mabooking-grid--three { grid-template-columns: 1fr; }
	.mabooking-booking-card__header,
	.mabooking-booking-card__footer { flex-direction: column; align-items: flex-start; }
}
.ma360-footer { text-align: center; font-size: .75rem; color: #9ca3af; padding: 1.5rem 0 .5rem; }
.ma360-footer a { color: #9ca3af; text-decoration: none; }
.ma360-footer a:hover { text-decoration: underline; }
</style>
