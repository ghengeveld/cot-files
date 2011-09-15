<?php

/**
 * Rights for folder sharing
 */
class Rights
{
	protected $table_name = 'folders_rights';
	protected $columns = array(
		'folder_id' => 'int',
		'user_id' => 'int',
		'rights' => 'string',
		'created' => 'int',
		'updated' => 'int'
	);
	protected $folder_id;
	protected $user_id;
	protected $rights;
	
	public function __construct($folder_id, $user_id, $rights)
	{
		$this->folder_id = $folder_id;
		$this->user_id = $user_id;
		$this->rights = $rights;
	}
	
	public function save()
	{
		$exists = (bool)$this->db->query("
			SELECT * FROM $this->table_name 
			WHERE folder_id = $this->folder_id 
			AND user_id = $this->user_id
			LIMIT 1
		");
		if ($exists)
		{
			return $this->db->update($this->table_name, array(
				'rights' => $this->rights,
				'updated' => time()
			), "folder_id = $this->folder_id 
				AND user_id = $this->user_id"
			);
		}
		else
		{
			$res = $this->db->insert($this->table_name, array(
				'folder_id' => $this->folder_id,
				'user_id' => $this->user_id,
				'rights' => $this->rights,
				'created' => time(),
				'updated' => time()
			));
		}
		return false;
	}
	
	public function load($id)
	{
		$res = $this->db->query("
			SELECT * FROM $this->table_name 
			WHERE folder_id = $this->folder_id
			AND user_id = $this->user_id
			LIMIT 1
		")->fetchAll(PDO::FETCH_ASSOC);
		if ($res)
		{
			$this->folder_id = $res['folder_id'];
			$this->user_id = $res['user_id'];
			$this->rights = $res['rights'];
			return true;
		}
		return false;
	}
	
	public function delete()
	{
		return $this->db->delete(
			$this->table_name, 
			"folder_id = $this->folder_id 
			AND user_id = $this->user_id"
		);
	}
}

?>