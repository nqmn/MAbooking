UPDATE `#__menu`
SET `link` = REPLACE(`link`, 'index.php?index.php?', 'index.php?')
WHERE `link` LIKE 'index.php?index.php?option=com_mabooking%';

UPDATE `#__menu`
SET `link` = 'index.php?option=com_mabooking&view=dashboard'
WHERE `link` = 'index.php?option=com_mabooking'
  AND `client_id` = 1;
