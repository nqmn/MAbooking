<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

$iconEdit = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 15.5V20h4.5L19 9.5 14.5 5 4 15.5zm12.8-11.3 2.5 2.5 1.2-1.2a1.77 1.77 0 0 0 0-2.5l-1.2-1.2a1.77 1.77 0 0 0-2.5 0l-1.2 1.2z" fill="currentColor"/></svg>';
$iconSave = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7l-4-4zM7 5h8v4H7V5zm12 14H5v-8h14v8z" fill="currentColor"/></svg>';
$iconDelete = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 3h6l1 2h4v2H4V5h4l1-2zm1 6v8h2V9h-2zm4 0v8h2V9h-2zM7 9h2v8H7V9zm1 11h8a2 2 0 0 0 2-2V8H6v10a2 2 0 0 0 2 2z" fill="currentColor"/></svg>';
?>
<div class="mabooking-venues">
	<div class="mabooking-venues__header">
		<div>
			<h1>Venue Management</h1>
			<p>Manage venues and their linked rooms from one page. Booking forms will only show rooms that belong to the selected venue.</p>
		</div>
	</div>

	<div class="mabooking-venues__tabs" role="tablist" aria-label="Venue management tabs">
		<button type="button" class="mabooking-venues__tab is-active" data-tab-target="add">Add New</button>
		<button type="button" class="mabooking-venues__tab" data-tab-target="manage">Manage List</button>
	</div>

	<section class="mabooking-venues__pane is-active" data-tab-panel="add">
		<div class="mabooking-venues__forms">
			<div class="mabooking-card">
				<h2>Add Venue</h2>
				<form action="<?php echo Route::_('index.php?option=com_mabooking&task=venues.saveVenue'); ?>" method="post" class="mabooking-form">
					<div class="mabooking-form__field">
						<label for="venue_title">Venue Title *</label>
						<input id="venue_title" type="text" name="venue[title]" required>
					</div>
					<div class="mabooking-form__field">
						<label for="venue_alias">Alias</label>
						<input id="venue_alias" type="text" name="venue[alias]" placeholder="optional-auto-generated-if-empty">
					</div>
					<div class="mabooking-form__field">
						<label for="venue_description">Description</label>
						<textarea id="venue_description" name="venue[description]" rows="4"></textarea>
					</div>
					<button type="submit" class="mabooking-button mabooking-button--primary">Create Venue</button>
					<?php echo HTMLHelper::_('form.token'); ?>
				</form>
			</div>

			<div class="mabooking-card">
				<h2>Add Room</h2>
				<form action="<?php echo Route::_('index.php?option=com_mabooking&task=venues.saveSpace'); ?>" method="post" class="mabooking-form">
					<div class="mabooking-form__field">
						<label for="space_venue_id">Linked Venue *</label>
						<select id="space_venue_id" name="space[venue_id]" required>
							<option value="">Select a venue...</option>
							<?php foreach ($this->venueOptions as $venue) : ?>
								<option value="<?php echo (int) $venue->id; ?>"><?php echo htmlspecialchars($venue->title, ENT_QUOTES, 'UTF-8'); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="mabooking-form__field">
						<label for="space_title">Room Title *</label>
						<input id="space_title" type="text" name="space[title]" required>
					</div>
					<div class="mabooking-form__grid">
						<div class="mabooking-form__field">
							<label for="space_capacity_min">Min Capacity</label>
							<input id="space_capacity_min" type="number" min="0" name="space[capacity_min]" value="0">
						</div>
						<div class="mabooking-form__field">
							<label for="space_capacity_max">Max Capacity</label>
							<input id="space_capacity_max" type="number" min="0" name="space[capacity_max]" value="0">
						</div>
					</div>
					<div class="mabooking-form__field">
						<label for="space_size_label">Size Label</label>
						<input id="space_size_label" type="text" name="space[size_label]" placeholder="e.g. 1,200 sq ft">
					</div>
					<div class="mabooking-form__field">
						<label for="space_details">Details</label>
						<textarea id="space_details" name="space[details]" rows="4"></textarea>
					</div>
					<button type="submit" class="mabooking-button mabooking-button--primary">Create Room</button>
					<?php echo HTMLHelper::_('form.token'); ?>
				</form>
			</div>
		</div>
	</section>

	<section class="mabooking-venues__pane" data-tab-panel="manage" hidden>
		<div class="mabooking-venues__list">
			<?php foreach ($this->venues as $venue) : ?>
			<div class="mabooking-card mabooking-card--venue">
					<button type="button" class="mabooking-card__top mabooking-collapse-toggle" data-collapse-target="venue-<?php echo (int) $venue->id; ?>" aria-expanded="false">
						<div>
							<h2><?php echo htmlspecialchars($venue->title, ENT_QUOTES, 'UTF-8'); ?></h2>
							<?php if ($venue->description !== '') : ?>
								<p><?php echo htmlspecialchars($venue->description, ENT_QUOTES, 'UTF-8'); ?></p>
							<?php else : ?>
								<p>No venue description yet.</p>
							<?php endif; ?>
						</div>
						<div class="mabooking-card__meta">
							<span class="mabooking-badge"><?php echo count($venue->spaces); ?> room<?php echo count($venue->spaces) === 1 ? '' : 's'; ?></span>
							<span class="mabooking-chevron">▾</span>
						</div>
					</button>

					<div class="mabooking-collapse" id="venue-<?php echo (int) $venue->id; ?>" hidden>
						<div class="mabooking-manage-grid">
							<div class="mabooking-manage-block">
								<div class="mabooking-manage-block__head">
									<h3>Edit Venue</h3>
									<div class="mabooking-actions">
										<button type="submit" form="venue-edit-<?php echo (int) $venue->id; ?>" class="mabooking-icon mabooking-icon--save" title="Save venue"><?php echo $iconSave; ?></button>
										<button type="submit" form="venue-delete-<?php echo (int) $venue->id; ?>" class="mabooking-icon mabooking-icon--delete mabooking-confirm-delete" data-confirm-message="Delete this venue and all linked rooms?" title="Delete venue"><?php echo $iconDelete; ?></button>
									</div>
								</div>
								<form id="venue-edit-<?php echo (int) $venue->id; ?>" action="<?php echo Route::_('index.php?option=com_mabooking&task=venues.saveVenue'); ?>" method="post" class="mabooking-form">
									<input type="hidden" name="venue[id]" value="<?php echo (int) $venue->id; ?>">
									<input type="hidden" name="venue[ordering]" value="<?php echo (int) $venue->ordering; ?>">
									<div class="mabooking-form__field">
										<label>Venue Title *</label>
										<input type="text" name="venue[title]" value="<?php echo htmlspecialchars($venue->title, ENT_QUOTES, 'UTF-8'); ?>" required>
									</div>
									<div class="mabooking-form__field">
										<label>Description</label>
										<textarea name="venue[description]" rows="3"><?php echo htmlspecialchars($venue->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
									</div>
									<?php echo HTMLHelper::_('form.token'); ?>
								</form>
								<form id="venue-delete-<?php echo (int) $venue->id; ?>" action="<?php echo Route::_('index.php?option=com_mabooking&task=venues.deleteVenue'); ?>" method="post">
									<input type="hidden" name="venue_id" value="<?php echo (int) $venue->id; ?>">
									<?php echo HTMLHelper::_('form.token'); ?>
								</form>
							</div>

							<div class="mabooking-manage-block">
								<div class="mabooking-manage-block__head">
									<h3>Rooms</h3>
								</div>
								<div class="mabooking-room-list">
									<?php if (!$venue->spaces) : ?>
										<div class="mabooking-empty">No rooms linked yet.</div>
									<?php else : ?>
										<?php foreach ($venue->spaces as $space) : ?>
											<div class="mabooking-room-card">
												<button type="button" class="mabooking-room-card__head mabooking-collapse-toggle" data-collapse-target="space-<?php echo (int) $space->id; ?>" aria-expanded="false">
													<div>
														<div class="mabooking-room__title"><?php echo htmlspecialchars($space->title, ENT_QUOTES, 'UTF-8'); ?></div>
														<div class="mabooking-room__meta">
															<?php if ($space->capacity_min > 0 || $space->capacity_max > 0) : ?>
																<span>Capacity: <?php echo (int) $space->capacity_min; ?>-<?php echo (int) $space->capacity_max; ?></span>
															<?php endif; ?>
															<?php if ($space->size_label !== '') : ?>
																<span><?php echo htmlspecialchars($space->size_label, ENT_QUOTES, 'UTF-8'); ?></span>
															<?php endif; ?>
														</div>
													</div>
													<div class="mabooking-card__meta">
														<span class="mabooking-chevron">▾</span>
													</div>
												</button>
												<div class="mabooking-room-card__body" id="space-<?php echo (int) $space->id; ?>" hidden>
													<div class="mabooking-actions mabooking-actions--inline">
														<button type="submit" form="space-edit-<?php echo (int) $space->id; ?>" class="mabooking-icon mabooking-icon--save" title="Save room"><?php echo $iconSave; ?></button>
														<button type="submit" form="space-delete-<?php echo (int) $space->id; ?>" class="mabooking-icon mabooking-icon--delete mabooking-confirm-delete" data-confirm-message="Delete this room?" title="Delete room"><?php echo $iconDelete; ?></button>
													</div>
													<form id="space-edit-<?php echo (int) $space->id; ?>" action="<?php echo Route::_('index.php?option=com_mabooking&task=venues.saveSpace'); ?>" method="post" class="mabooking-form">
														<input type="hidden" name="space[id]" value="<?php echo (int) $space->id; ?>">
														<input type="hidden" name="space[venue_id]" value="<?php echo (int) $venue->id; ?>">
														<input type="hidden" name="space[ordering]" value="<?php echo (int) $space->ordering; ?>">
														<div class="mabooking-form__field">
															<label>Room Title *</label>
															<input type="text" name="space[title]" value="<?php echo htmlspecialchars($space->title, ENT_QUOTES, 'UTF-8'); ?>" required>
														</div>
														<div class="mabooking-form__grid">
															<div class="mabooking-form__field">
																<label>Min Capacity</label>
																<input type="number" min="0" name="space[capacity_min]" value="<?php echo (int) $space->capacity_min; ?>">
															</div>
															<div class="mabooking-form__field">
																<label>Max Capacity</label>
																<input type="number" min="0" name="space[capacity_max]" value="<?php echo (int) $space->capacity_max; ?>">
															</div>
														</div>
														<div class="mabooking-form__field">
															<label>Size Label</label>
															<input type="text" name="space[size_label]" value="<?php echo htmlspecialchars($space->size_label, ENT_QUOTES, 'UTF-8'); ?>">
														</div>
														<div class="mabooking-form__field">
															<label>Details</label>
															<textarea name="space[details]" rows="3"><?php echo htmlspecialchars($space->details, ENT_QUOTES, 'UTF-8'); ?></textarea>
														</div>
														<?php echo HTMLHelper::_('form.token'); ?>
													</form>
												</div>
												<form id="space-delete-<?php echo (int) $space->id; ?>" action="<?php echo Route::_('index.php?option=com_mabooking&task=venues.deleteSpace'); ?>" method="post">
													<input type="hidden" name="space_id" value="<?php echo (int) $space->id; ?>">
													<?php echo HTMLHelper::_('form.token'); ?>
												</form>
											</div>
										<?php endforeach; ?>
									<?php endif; ?>
										<div class="mabooking-room-card mabooking-room-card--add">
											<button type="button" class="mabooking-add-room" data-add-room-target="add-space-<?php echo (int) $venue->id; ?>">+ Add Room</button>
											<div class="mabooking-room-card__body" id="add-space-<?php echo (int) $venue->id; ?>" hidden>
												<form action="<?php echo Route::_('index.php?option=com_mabooking&task=venues.saveSpace'); ?>" method="post" class="mabooking-form">
													<input type="hidden" name="space[venue_id]" value="<?php echo (int) $venue->id; ?>">
													<div class="mabooking-form__field">
														<label>Room Title *</label>
														<input type="text" name="space[title]" required>
													</div>
													<div class="mabooking-form__grid">
														<div class="mabooking-form__field">
															<label>Min Capacity</label>
															<input type="number" min="0" name="space[capacity_min]" value="0">
														</div>
														<div class="mabooking-form__field">
															<label>Max Capacity</label>
															<input type="number" min="0" name="space[capacity_max]" value="0">
														</div>
													</div>
													<div class="mabooking-form__field">
														<label>Size Label</label>
														<input type="text" name="space[size_label]">
													</div>
													<div class="mabooking-form__field">
														<label>Details</label>
														<textarea name="space[details]" rows="3"></textarea>
													</div>
													<div class="mabooking-actions mabooking-actions--inline">
														<button type="submit" class="mabooking-button mabooking-button--primary">Create Room</button>
													</div>
													<?php echo HTMLHelper::_('form.token'); ?>
												</form>
											</div>
										</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var tabs = document.querySelectorAll('.mabooking-venues__tab');
	var panes = document.querySelectorAll('.mabooking-venues__pane');
	var currentTab = new URLSearchParams(window.location.search).get('tab') || 'add';

	var activateTab = function (target) {
		tabs.forEach(function (item) {
			item.classList.toggle('is-active', item.getAttribute('data-tab-target') === target);
		});

		panes.forEach(function (pane) {
			var active = pane.getAttribute('data-tab-panel') === target;
			pane.classList.toggle('is-active', active);
			pane.hidden = !active;
		});
	};

	tabs.forEach(function (tab) {
		tab.addEventListener('click', function () {
			var target = tab.getAttribute('data-tab-target');
			activateTab(target);
		});
	});

	activateTab(currentTab === 'manage' ? 'manage' : 'add');

	document.querySelectorAll('.mabooking-collapse-toggle').forEach(function (button) {
		button.addEventListener('click', function () {
			var targetId = button.getAttribute('data-collapse-target');
			var panel = document.getElementById(targetId);

			if (!panel) {
				return;
			}

			var hidden = panel.hasAttribute('hidden');
			if (hidden) {
				panel.removeAttribute('hidden');
				button.classList.add('is-open');
				button.setAttribute('aria-expanded', 'true');
			} else {
				panel.setAttribute('hidden', 'hidden');
				button.classList.remove('is-open');
				button.setAttribute('aria-expanded', 'false');
			}
		});
	});

	document.querySelectorAll('[data-add-room-target]').forEach(function (button) {
		button.addEventListener('click', function () {
			var targetId = button.getAttribute('data-add-room-target');
			var panel = document.getElementById(targetId);

			if (!panel) {
				return;
			}

			panel.hidden = !panel.hidden;
			button.classList.toggle('is-open', !panel.hidden);
			button.textContent = panel.hidden ? '+ Add Room' : '− Hide Add Room';
		});
	});

	document.querySelectorAll('.mabooking-confirm-delete').forEach(function (button) {
		button.addEventListener('click', function (event) {
			var message = button.getAttribute('data-confirm-message') || 'Delete this item?';
			if (!window.confirm(message)) {
				event.preventDefault();
			}
		});
	});
});
</script>

<style>
.mabooking-venues { display: grid; gap: 1.5rem; max-width: 1200px; margin: 0 auto; color: #1c2834; }
.mabooking-venues__header h1 { margin: 0 0 .35rem; font-size: 2rem; }
.mabooking-venues__header p { margin: 0; color: #6b7280; max-width: 62rem; }
.mabooking-venues__tabs { display: inline-flex; gap: .35rem; border: 1px solid #e5e7eb; border-radius: 999px; padding: .25rem; background: #fff; box-shadow: 0 1px 3px rgba(15, 23, 42, .06); }
.mabooking-venues__tab { border: 0; background: transparent; color: #6b7280; padding: .7rem 1.2rem; border-radius: 999px; font-size: .76rem; font-weight: 700; }
.mabooking-venues__tab.is-active { background: #314155; color: #fff; }
.mabooking-venues__pane { display: none; }
.mabooking-venues__pane.is-active { display: block; }
.mabooking-venues__forms { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
.mabooking-venues__list { display: grid; gap: 1rem; }
.mabooking-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 1rem; box-shadow: 0 1px 3px rgba(15, 23, 42, .06); padding: 1.5rem; }
.mabooking-card h2, .mabooking-manage-block__head h3 { margin: 0; font-size: 1.05rem; }
.mabooking-form { display: grid; gap: 1rem; }
.mabooking-form__grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
.mabooking-form__field label { display: block; margin-bottom: .45rem; color: #4b5563; font-size: .78rem; font-weight: 700; }
.mabooking-form__field input,
.mabooking-form__field select,
.mabooking-form__field textarea { width: 100%; border: 1px solid #e5e7eb; border-radius: .6rem; background: #f9fafb; padding: .8rem .9rem; font-size: .9rem; color: #1c2834; }
.mabooking-form__field textarea { resize: vertical; min-height: 6rem; }
.mabooking-button { border: 0; border-radius: .65rem; padding: .85rem 1rem; font-weight: 700; cursor: pointer; }
.mabooking-button--primary { background: #1c2834; color: #fff; }
.mabooking-card--venue { padding: 0; overflow: hidden; }
.mabooking-card__top { width: 100%; display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; padding: 1.25rem 1.5rem; border: 0; border-bottom: 1px solid #f3f4f6; background: #fff; text-align: left; }
.mabooking-card__top p { margin: .35rem 0 0; color: #6b7280; }
.mabooking-card__meta { display: flex; align-items: center; gap: .75rem; }
.mabooking-badge { display: inline-flex; padding: .4rem .75rem; border-radius: 999px; background: #eef3f8; color: #314155; font-size: .75rem; font-weight: 700; white-space: nowrap; }
.mabooking-chevron { color: #64748b; transition: transform .2s; }
.mabooking-collapse-toggle.is-open .mabooking-chevron { transform: rotate(180deg); }
.mabooking-collapse { padding: 1.25rem 1.5rem 1.5rem; background: #fbfcfe; }
.mabooking-manage-grid { display: grid; grid-template-columns: 360px minmax(0, 1fr); gap: 1rem; }
.mabooking-manage-block { background: #fff; border: 1px solid #e8edf3; border-radius: .9rem; padding: 1rem; }
.mabooking-manage-block__head, .mabooking-room-card__head { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: .9rem; }
.mabooking-actions { display: flex; gap: .5rem; }
.mabooking-actions--inline { justify-content: flex-end; margin-top: .75rem; }
.mabooking-icon { width: 2.15rem; height: 2.15rem; display: inline-flex; align-items: center; justify-content: center; border-radius: .65rem; border: 1px solid #dce4ed; background: #fff; cursor: pointer; color: #314155; transition: background-color .15s, border-color .15s, color .15s, transform .15s; }
.mabooking-icon svg { width: 1rem; height: 1rem; display: block; }
.mabooking-icon:hover { background: #f8fafc; border-color: #cbd5e1; transform: translateY(-1px); }
.mabooking-icon--save { color: #166534; }
.mabooking-icon--save:hover { background: #ecfdf5; border-color: #bbf7d0; }
.mabooking-icon--delete { color: #b91c1c; }
.mabooking-icon--delete:hover { background: #fef2f2; border-color: #fecaca; }
.mabooking-room-list { display: grid; gap: .75rem; }
.mabooking-room-card { border: 1px solid #eef2f7; border-radius: .85rem; padding: .9rem 1rem; background: #f8fafc; }
.mabooking-room-card--add { background: #fff; border-style: dashed; }
.mabooking-room-card__head { width: 100%; border: 0; background: transparent; padding: 0; text-align: left; margin-bottom: 0; }
.mabooking-room-card__body { margin-top: .9rem; }
.mabooking-room__title { font-weight: 700; }
.mabooking-room__meta { display: flex; flex-wrap: wrap; gap: .85rem; margin-top: .35rem; color: #64748b; font-size: .76rem; }
.mabooking-empty { color: #64748b; }
.mabooking-add-room { width: 100%; border: 0; background: transparent; color: #314155; text-align: left; font-weight: 700; padding: 0; cursor: pointer; }
@media (max-width: 980px) {
	.mabooking-venues__forms,
	.mabooking-form__grid,
	.mabooking-manage-grid { grid-template-columns: 1fr; }
}
</style>
