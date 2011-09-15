<?php

/**
 * Folder
 */
class Folder
{
	protected $table_name = 'folders';
	protected $columns = array(
		'id' => 'int',
		'parent' => 'int',
		'name' => 'string',
		'description' => 'string',
		'owner_id' => 'int',
		'created' => 'int',
		'updated' => 'int'
	);
	protected $data = array();

	public function __construct($name, $description, $owner_id, $parent = 0)
	{
		$this->data['id'] = null;
		$this->data['parent'] = $parent;
		$this->data['name'] = $name;
		$this->data['description'] = $description;
		$this->data['owner_id'] = $owner_id;
		$this->data['created'] = null;
		$this->data['updated'] = null;
	}
	
	public function save()
	{
		global $db;
		if ($this->data['id'])
		{
			$res = $db->update($this->table_name, array(
				'parent' => $this->data['parent'],
				'name' => $this->data['name'],
				'description' => $this->data['description'],
				'owner_id' => $this->data['owner_id'],
				'updated' => time()
			), "id = {$this->data['id']}");
		}
		else
		{
			$res = $db->insert($this->table_name, array(
				'parent' => $this->data['parent'],
				'name' => $this->data['name'],
				'description' => $this->data['description'],
				'owner_id' => $this->data['owner_id'],
				'created' => time(),
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
			SELECT *
			FROM $this->table_name
			WHERE id = $id
			LIMIT 1
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
		return $db->delete($this->table_name, "id = {$this->data['id']}");
	}
}

?>