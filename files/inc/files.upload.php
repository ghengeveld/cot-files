<?php

defined('COT_CODE') or die('Wrong URL');

if ($_FILES && $_FILES['file'] && is_array($_FILES['file']['name']))
{
	$titles = cot_import('title', 'P', 'ARR');
	$descriptions = cot_import('description', 'P', 'ARR');
	$folder_id = cot_import('folder_id', 'P', 'INT');
	
	foreach ($_FILES['file']['name'] as $i => $name)
	{
		$title = cot_import($titles[$i], 'D', 'TXT');
		$description = cot_import($descriptions[$i], 'D', 'TXT');
		$size = $_FILES['file']['size'][$i];
		$mimetype = $_FILES['file']['type'][$i];
		$tmp_name = $_FILES['file']['tmp_name'][$i];
		
		if (is_uploaded_file($tmp_name) && $size > 0)
		{
			$file = new File($folder_id, $name, $title, $description, $size, $mimetype, $usr['id']);
			if ($file->store($tmp_name))
			{
				$file->save();
			}
		}
	}
}

?>