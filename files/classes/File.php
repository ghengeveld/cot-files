<?php

/**
 * File
 */
class File
{
	protected $table_name = 'files';
	protected $columns = array(
		'id' => 'int',
		'folder_id' => 'int',
		'name' => 'string',
		'title' => 'string',
		'description' => 'string',
		'size' => 'int',
		'mimetype' => 'string',
		'fullpath' => 'string',
		'owner_id' => 'int',
		'created' => 'int',
		'updated' => 'int'
	);
	protected $data = array();
	
	public function __construct($folder_id, $name, $title, $description, $size, $mimetype, $owner_id)
	{
		global $db_x;
		$this->table_name = $db_x.$this->table_name;
		$this->data['id'] = null;
		$this->data['folder_id'] = $folder_id;
		$this->data['name'] = $name;
		$this->data['title'] = $title;
		$this->data['description'] = $description;
		$this->data['size'] = $size;
		$this->data['mimetype'] = $mimetype;
		$this->data['fullpath'] = $this->fullpath();
		$this->data['owner_id'] = $owner_id;
		$this->data['created'] = time();
		$this->data['updated'] = null;
	}
		
	protected function fullpath()
	{
		global $cfg, $db;
		$filename = sha1(mt_rand());
		$path = $cfg['files_dir'] . '/';
		
		$totalfiles = (int)$db->query("SELECT COUNT(*) FROM $this->table_name")->fetchColumn();
		$depth = floor(log10($totalfiles)) - 1;
		if ($depth < 0) $depth = 0;
		
		for ($i=0; $i < $depth; $i++)
		{
			$path .= $filename[$i] . '/';
			if (!is_dir($path))
			{
				mkdir($path);
			}
		}
		return $path . $filename;
	}
	
	public function store($tmp_name)
	{
		global $cfg;
		$res = move_uploaded_file($tmp_name, $this->data['fullpath']);
		@chmod($this->data['fullpath'], $cfg['file_perms']);
		$this->data['size'] = filesize($this->data['fullpath']);
		return $res;
	}
	
	public function save()
	{
		global $db;
		if ($this->data['id'])
		{
			$res = $db->update($this->table_name, array(
				'folder_id' => $this->data['folder_id'],
				'name' => $this->data['name'],
				'title' => $this->data['title'],
				'description' => $this->data['description'],
				'size' => $this->data['size'],
				'mimetype' => $this->data['mimetype'],
				'fullpath' => $this->data['fullpath'],
				'owner_id' => $this->data['owner_id'],
				'updated' => time()
			), "id = {$this->data['id']}");
		}
		else
		{
			$res = $db->insert($this->table_name, array(
				'folder_id' => $this->data['folder_id'],
				'name' => $this->data['name'],
				'title' => $this->data['title'],
				'description' => $this->data['description'],
				'size' => $this->data['size'],
				'mimetype' => $this->data['mimetype'],
				'fullpath' => $this->data['fullpath'],
				'owner_id' => $this->data['owner_id'],
				'created' => $this->data['created'],
				'updated' => time()
			));
			$this->data['id'] = $db->lastInsertId();
		}
		return $res;
	}
	
	public function load($id)
	{
		global $db;
		$res = $db->query("
			SELECT * FROM $this->table_name WHERE id = $id LIMIT 1
		")->fetchAll(PDO::FETCH_ASSOC);
		if ($res)
		{
			$this->data = $res;
			return true;
		}
		return false;
	}
	
	public function update($data, $skip_null = true)
	{
		$affected_cols = 0;
		foreach (array_keys($data) as $column)
		{
			if (array_key_exists($column, $this->columns))
			{
				if ($data[$column] != null || !$skip_null)
				{
					$this->data[$column] = $data[$column];
					$affected_cols++;
				}
			}
		}
		return $affected_cols;
	}
	
	public function delete()
	{
		global $db;
		@unlink($this->data['fullpath']);
		return $db->delete($this->table_name, "id = {$this->data['id']}");
	}
}

?>