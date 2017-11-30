<?php
// pull in the interface we are supposed to implement
// Note that it doesn't have to follow the normal CodeIgniter naming rules!
require_once 'DataMapper.php';

/**
 * Generic data access model, for an RDB.
 * 
 * This class is called MY_Model to keep CodeIgniter happy.
 *
 * @author		JLP
 * @copyright           Copyright (c) 2010-2017, James L. Parry
 * ------------------------------------------------------------------------
 */
class MY_Model extends CI_Model implements DataMapper
{

	protected $_tableName;   // Which table is this a model for?
	protected $_keyField; // name of the primary key field

//---------------------------------------------------------------------------
//  Housekeeping methods
//---------------------------------------------------------------------------

	/**
	 * Constructor.
	 * @param string $tablename Name of the RDB table
	 * @param string $keyfield  Name of the primary key field
	 */
	function __construct($tablename = null, $keyfield = 'id')
	{
		parent::__construct();

		if ($tablename == null)
			$this->_tableName = get_class($this);
		else
			$this->_tableName = $tablename;

		$this->_keyField = $keyfield;
	}

//---------------------------------------------------------------------------
//  Utility methods
//---------------------------------------------------------------------------

	/**
	 * Return the number of records in this table.
	 * @return int The number of records in this table
	 */
	function size()
	{
		$query = $this->db->get($this->_tableName);
		return $query->num_rows();
	}

	/**
	 * Return the field names in this table, from the table metadata.
	 * @return array(string) The field names in this table
	 */
	function fields()
	{
		return $this->db->list_fields($this->_tableName);
	}

//---------------------------------------------------------------------------
//  C R U D methods
//---------------------------------------------------------------------------
	// Create a new data object.
	// Only use this method if intending to create an empty record and then
	// populate it.
	function create()
	{
		$names = $this->db->list_fields($this->_tableName);
		$object = new StdClass;
		foreach ($names as $name)
			$object->$name = "";
		return $object;
	}

	// Add a record to the DB
	function add($record)
	{
		// convert object to associative array, if needed
		if (is_object($record))
		{
			$data = get_object_vars($record);
		}
		else
		{
			$data = $record;
		}
		// update the DB table appropriately
		$key = $data[$this->_keyField];
		$object = $this->db->insert($this->_tableName, $data);
	}

	// Retrieve an existing DB record as an object
	function get($key, $key2 = null)
	{
		$this->db->where($this->_keyField, $key);
		$query = $this->db->get($this->_tableName);
		if ($query->num_rows() < 1)
			return null;
		return $query->row();
	}

	// Update a record in the DB
	function update($record)
	{
		// convert object to associative array, if needed
		if (is_object($record))
		{
			$data = get_object_vars($record);
		}
		else
		{
			$data = $record;
		}
		// update the DB table appropriately
		$key = $data[$this->_keyField];
		$this->db->where($this->_keyField, $key);
		$object = $this->db->update($this->_tableName, $data);
	}

	// Delete a record from the DB
	function delete($key, $key2 = null)
	{
		$this->db->where($this->_keyField, $key);
		$object = $this->db->delete($this->_tableName);
	}

	// Determine if a key exists
	function exists($key, $key2 = null)
	{
		$this->db->where($this->_keyField, $key);
		$query = $this->db->get($this->_tableName);
		if ($query->num_rows() < 1)
			return false;
		return true;
	}

//---------------------------------------------------------------------------
//  Aggregate methods
//---------------------------------------------------------------------------
	// Return all records as an array of objects
	function all()
	{
		$this->db->order_by($this->_keyField, 'asc');
		$query = $this->db->get($this->_tableName);
		return $query->result();
	}

	// Return all records as a result set
	function results()
	{
		$this->db->order_by($this->_keyField, 'asc');
		$query = $this->db->get($this->_tableName);
		return $query;
	}

	// Return the most recent records as a result set
	function trailing($count = 10)
	{
		$start = $this->db->count_all($this->_tableName) - $count;
		if ($start < 0)
			$start = 0;
		$this->db->limit($count, $start);
		$this->db->order_by($this->_keyField, 'asc');
		$query = $this->db->get($this->_tableName);
		return $query;
	}

	// Return filtered records as an array of records
	function some($what, $which)
	{
		$this->db->order_by($this->_keyField, 'asc');
		if (($what == 'period') && ($which < 9))
		{
			$this->db->where($what, $which); // special treatment for period
		}
		else
			$this->db->where($what, $which);
		$query = $this->db->get($this->_tableName);
		return $query->result();
	}

	// Determine the highest key used
	function highest()
	{
		$key = $this->_keyField;
		$this->db->select_max($key);
		$query = $this->db->get($this->_tableName);
		$result = $query->result();
		if (count($result) > 0)
			return $result[0]->$key;
		else
			return null;
	}

	// Retrieve first record from a table.
	function first()
	{
		if ($this->size() < 1)
			return null;
		$query = $this->db->get($this->_tableName, 1, 1);
		return $query->result()[0];
	}

	// Retrieve records from the beginning of a table.
	function head($count = 10)
	{
		$this->db->limit(10);
		$this->db->order_by($this->_keyField, 'asc');
		$query = $this->db->get($this->_tableName);
		return $query->result();
	}

	// Retrieve records from the end of a table.
	function tail($count = 10)
	{
		$start = $this->db->count_all($this->_tableName) - $count;
		if ($start < 0)
			$start = 0;
		$this->db->limit($count, $start);
		$this->db->order_by($this->_keyField, 'asc');
		$query = $this->db->get($this->_tableName);
		return $query->result();
	}

	// truncate the table backing this model
	function truncate()
	{
		$this->db->truncate($this->_tableName);
	}

}

/**
 * Support for RDB persistence with a compound (two column) key.
 */
class MY_Model2 extends MY_Model
{

	protected $_keyField2;  // second part of composite primary key

	// Constructor

	function __construct($tablename = null, $keyfield = 'id', $keyfield2 = 'part')
	{
		parent::__construct($tablename, $keyfield);
		$this->_keyField2 = $keyfield2;
	}

//---------------------------------------------------------------------------
//  Record-oriented functions
//---------------------------------------------------------------------------
	// Retrieve an existing DB record as an object
	function get($key1, $key2)
	{
		$this->db->where($this->_keyField, $key1);
		$this->db->where($this->_keyField2, $key2);
		$query = $this->db->get($this->_tableName);
		if ($query->num_rows() < 1)
			return null;
		return $query->row();
	}

	// Update a record in the DB
	function update($record)
	{
		// convert object to associative array, if needed
		if (is_object($record))
		{
			$data = get_object_vars($record);
		}
		else
		{
			$data = $record;
		}
		// update the DB table appropriately
		$key = $data[$this->_keyField];
		$key2 = $data[$this->_keyField2];
		$this->db->where($this->_keyField, $key);
		$this->db->where($this->_keyField2, $key2);
		$object = $this->db->update($this->_tableName, $data);
	}

	// Delete a record from the DB
	function delete($key1, $key2)
	{
		$this->db->where($this->_keyField, $key1);
		$this->db->where($this->_keyField2, $key2);
		$object = $this->db->delete($this->_tableName);
	}

	// Determine if a key exists
	function exists($key1, $key2)
	{
		$this->db->where($this->_keyField, $key1);
		$this->db->where($this->_keyField2, $key2);
		$query = $this->db->get($this->_tableName);
		if ($query->num_rows() < 1)
			return false;
		return true;
	}

//---------------------------------------------------------------------------
//  Composite functions
//---------------------------------------------------------------------------
	// Return all records associated with a member
	function group($key)
	{
		$this->db->where($this->_keyField, $key);
		$this->db->order_by($this->_keyField, 'asc');
		$this->db->order_by($this->_keyField2, 'asc');
		$query = $this->db->get($this->_tableName);
		return $query->result();
	}

	// Delete all records associated with a member
	function delete_some($key)
	{
		$this->db->where($this->_keyField, $key);
		$object = $this->db->delete($this->_tableName);
	}

	// Determine the highest secondary key associated with a primary
	function highest_some($key)
	{
		$this->db->where($this->_keyField, $key);
		$query = $this->db->get($this->_tableName);
		$highest = -1;
		foreach ($query->result() as $record)
		{
			$key2 = $record->{$this->_keyField2};
			if ($key2 > $highest)
				$highest = $key2;
		}
		return $highest;
	}

//---------------------------------------------------------------------------
//  Aggregate functions
//---------------------------------------------------------------------------
	// Return all records as an array of objects
	function all($primary = null)
	{
		$this->db->order_by($this->_keyField, 'asc');
		$this->db->order_by($this->_keyField2, 'asc');
		$query = $this->db->get($this->_tableName);
		return $query->result();
	}

}

// Include any other persistence implementations, so that they can be used
// as base models for any in a webapp.

include_once 'RDB_Model.php';	// backed by an RDB
include_once 'Memory_Model.php';	// In-memory only
include_once 'CSV_Model.php';	// CSV persisted
include_once 'XML_Model.php';	// CSV persisted
include_once 'Entity.php';	// Entity persisted
