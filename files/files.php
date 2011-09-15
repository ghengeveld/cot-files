<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * Files module
 *
 * @package files
 * @version 0.1
 * @author Gert Hengeveld
 * @copyright Copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

// Configuration
$cfg['files_dir'] = 'datas/files';

// Environment setup
$env['location'] = 'files';

// Additional API requirements
require_once cot_incfile('uploads');

function autoloader($class)
{
	require "modules/files/classes/$class.php";
}
spl_autoload_register('autoloader');

if ($a == 'upload') include cot_incfile('files', 'module', 'upload');

?>

<form action="index.php?e=files&a=upload" method="post" enctype="multipart/form-data">
	<input type="file" name="file[]">
	<input type="text" name="title[]">
	<input type="text" name="description[]">
	<input type="file" name="file[]">
	<input type="text" name="title[]">
	<input type="text" name="description[]">
	<button type="submit">Upload</button>
</form>