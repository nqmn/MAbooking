<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mabooking
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('bootstrap.tooltip');

$articleId = (int) ($this->item->article_id ?? 0);
?>
<form action="<?php echo Route::_('index.php?option=com_mabooking&layout=edit&id=' . (int) ($this->item->id ?? 0)); ?>" method="post" name="adminForm" id="booking-form" class="form-validate">
	<div class="row">
		<div class="col-lg-8">
			<div class="card mb-3">
				<div class="card-body">
					<?php echo $this->form->renderFieldset('details'); ?>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="card mb-3">
				<div class="card-body">
					<?php echo $this->form->renderFieldset('client'); ?>
					<?php echo $this->form->renderFieldset('publishing'); ?>
					<?php if ($articleId > 0) : ?>
						<div class="mt-3">
							<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_content&task=article.edit&id=' . $articleId); ?>">
								Edit Linked Joomla Article
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
