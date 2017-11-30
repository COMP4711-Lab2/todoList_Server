<?php

/**
 * Generic data access model, with data stored in memory only.
 * 
 * Single "key" only, at this point.
 * 
 * Provide additional base models for different persistence choices
 * by extending this, and over-riding the load() and store() methods.
 *
 * @author		JLP
 * @copyright           Copyright (c) 2010-2017, James L. Parry
 * ------------------------------------------------------------------------
 */
class Memory_Model extends CI_Model implements DataMapper
{

	protected $_origin;  // Persistent name for this model, eg. filename
	protected $_keyfield; // name of the primary key field
	protected $_entity;  // persistence-specific name of an entity in this collection
	protected $_data; // place to hold all the data in this collection
	protected $_fields; // place to hold metadata about this collection

//---------------------------------------------------------------------------
//  Housekeeping methods
//---------------------------------------------------------------------------

	/**
	 * Constructor.
	 * @param string $origin Persistent name of a collection
	 * @param string $keyfield  Name of the primary key field
	 * @param string $entity	Entity name meaningful to the persistence
	 */
	function __construct($origin = null, $keyfield = 'id', $entity = null)
	{
		parent::__construct();

		// guess at persistent name if not specified
		if ($origin == null)
			$this->_origin = get_class($this);
		else
			$this->_origin = $origin;

		// remember the other constructor fields
		$this->_keyfield = $keyfield;
		$this->_entity = $entity;

		// start with an empty collection
		$this->_data = array(); // an array of objects
		$this->fields = array(); // an array of strings
		// and populate the collection
		//$this->load();	// UNCOMMENT THIS LINE IF PERSISTENT
	}

	/**
	 * Load the collection state appropriately, depending on persistence choice.
	 * OVER-RIDE THIS METHOD in persistence choice implementations
	 */
	protected function load()
	{
		//---------------------
		// Your code goes here
		// --------------------
		// rebuild the keys table
		$this->reindex();
	}

	/**
	 * Store the collection state appropriately, depending on persistence choice.
	 * OVER-RIDE THIS METHOD in persistence choice implementations
	 */
	protected function store()
	{
		// rebuild the keys table
		$this->reindex();
		//---------------------
		// Your code goes here
		// --------------------
	}

	// Rebuild and resort the ordered data copy
	protected function reindex()
	{
		// rebuild the ordered collection
		$results = array();
		foreach ($this->_data as $old => $record)
		{
			$key = $record->{$this->_keyfield};
			$results[$key] = $record;
		}
		// sort the collection
		ksort($results);
		// remember the new collection
		$this->_data = $results;
		// reset the cursor
		reset($this->_data);
	}

//---------------------------------------------------------------------------
//  Utility methods
//---------------------------------------------------------------------------

	/**
	 * Return the number of records in this collection.
	 * @return int The number of records in this collection
	 */
	function size()
	{
		return count($this->_data);
	}

	/**
	 * Return the field names in this collection, from the collection metadata.
	 * @return array(string) The field names in this table
	 */
	function fields()
	{
		return $this->_fields;
	}

//---------------------------------------------------------------------------
//  C R U D methods
//---------------------------------------------------------------------------
	// Create a new data object.
	// Only use this method if intending to create an empty record and then
	// populate it.
	function create()
	{
		$names = $this->_fields;
		$object = new StdClass;
		foreach ($names as $name)
			$object->$name = "";
		return $object;
	}

	// Add a record to the collection
	function add($record)
	{
		// convert object from associative array, if needed
		$record = (is_array($record)) ? (object) $record : $record;

		// update the DB table appropriately
		$key = $record->{$this->_keyfield};
		$this->_data[$key] = $record;

		$this->store();
	}

	// Retrieve an existing collection record as an object
	function get($key, $key2 = null)
	{
		return (isset($this->_data[$key])) ? $this->_data[$key] : null;
	}

	// Update a record in the collection
	function update($record)
	{
		// convert object from associative array, if needed
		$record = (is_array($record)) ? (object) $record : $record;
		// update the collection appropriately
		$key = $record->{$this->_keyfield};
		if (isset($this->_data[$key]))
		{
			$this->_data[$key] = $record;
			$this->store();
		}
	}

	// Delete a record from the DB
	function delete($key, $key2 = null)
	{
		if (isset($this->_data[$key]))
		{
			unset($this->_data[$key]);
			$this->store();
		}
	}

	// Determine if a key exists
	function exists($key, $key2 = null)
	{
		return isset($this->_data[$key]);
	}

//---------------------------------------------------------------------------
//  Aggregate methods
//---------------------------------------------------------------------------
	// Return all records as an array of objects
	function all()
	{
		return $this->_data;
	}

	// Return a "result set".
	// OVER-RIDE if this makes sense.
	function results()
	{
		return null;
	}

	// Return the most recent records as a result set.
	// OVER-RIDE if you have a better way.
	function trailing($count = 10)
	{
		return array_slice($this->_data, 0 - $count);
	}

	/**
	 *  Return filtered records as an array of records.
	 * 
	 * @param type $what	Field name to select by
	 * @param type $which	Value to select
	 * @return type
	 */
	function some($what, $which)
	{
		$results = array();
		foreach ($this->_data as $key => $record)
			if ($record[$what] == $which)
				$results[] = $record;
		return $results;
	}

	// Determine the highest key used
	function highest()
	{
		end($this->_data);
		return key($this->_data);
	}

	// Retrieve first record from a table.
	function first()
	{
		return $this->_data[0];
	}

	// Retrieve records from the beginning of a table.
	function head($count = 10)
	{
		return array_slice($this->_data, 0, $count);
	}

	// Retrieve records from the end of a table.
	function tail($count = 10)
	{
		return array_slice($this->_data, 0 - $count);
	}

	// truncate the table backing this model
	function truncate()
	{
		$this->data = array();
		;
	}

}
