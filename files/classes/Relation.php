<?php

/**
 * Relation between file and folder
 */
class Relation
{
	protected $table_name = 'folders_files';
	protected $folder_id;
	protected $file_id;
	
	public function __construct($folder_id, $file_id)
	{
		$this->folder_id = $folder_id;
		$this->file_id = $file_id;
	}
	
	public function save()
	{
		$exists = (bool)$this->db->query("
			SELECT * FROM $this->table_name 
			WHERE folder_id = $this->folder_id 
			AND file_id = $this->file_id
			LIMIT 1
		");
		if (!$exists)
		{
			return $this->db->insert($this->table_name, array(
				'folder_id' => $this->folder_id,
				'file_id' => $this->file_id
			));
		}
		return false;
	}
	
	public function load($id)
	{
		$res = $this->db->query("
			SELECT * FROM $this->table_name 
			WHERE id = $id LIMIT 1
		")->fetchAll(PDO::FETCH_ASSOC);
		if ($res)
		{
			$this->folder_id = $res['folder_id'];
			$this->file_id = $res['file_id'];
			return true;
		}
		return false;
	}
	
	public function delete()
	{
		return $this->db->delete(
			$this->table_name, 
			"folder_id = $this->folder_id AND file_id = $this->file_id"
		);
	}
}

?>