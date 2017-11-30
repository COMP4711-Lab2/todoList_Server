<?php

/**
 * Base model backed by an RDB.
 * 
 * This is effectively a marker interface, providing naming consistent
 * with the other base models.
 * 
 * @author		JLP
 * @copyright           Copyright (c) 2010-2017, James L. Parry
 * ------------------------------------------------------------------------
 */
class RDB_Model extends MY_Model
{

	/**
	 * Constructor.
	 * @param string $tablename Name of the RDB table
	 * @param string $keyfield  Name of the primary key field
	 */
	function __construct($tablename = null, $keyfield = 'id')
	{
		parent::__construct($tablename = null, $keyfield = 'id');
	}

}

/**
 * Support for RDB persistence with a compound (two column) key.
 */
class RDB_Model2 extends MY_Model2
{

	protected $_keyField2;  // second part of composite primary key

	// Constructor

	function __construct($tablename = null, $keyfield = 'id', $keyfield2 = 'part')
	{
		parent::__construct($tablename, $keyfield, $keyfield2);
	}

}
