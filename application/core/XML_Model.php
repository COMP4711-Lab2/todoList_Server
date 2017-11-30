<?php

/**
 * CSV-persisted collection.
 * 
 * ------------------------------------------------------------------------
 */
class XML_Model extends Memory_Model
{
//---------------------------------------------------------------------------
//  Housekeeping methods
//---------------------------------------------------------------------------

	/**
	 * Constructor.
	 * @param string $origin Filename of the CSV file
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
		$this->load();
	}

	/**
	 * Load the collection state appropriately, depending on persistence choice.
	 * OVER-RIDE THIS METHOD in persistence choice implementations
	 */
	protected function load()
	{
		//---------------------
		$handle = $this->_origin;		
		$data = simplexml_load_file($handle);
		
		foreach($data->children() as $item) 
		{
			$record = new stdClass();
			foreach($item->children() as $category)
			{
				$this->_fields[] = $category->getName();
				$record->{$category->getName()} = (string) $category;				
			}
				$key = $record->{$this->_keyfield};
				$this->_data[$key] = $record;
		}			
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
		$this->reindex();
		
		$tasklist = new SimpleXMLElement("<xml/>");
		
		foreach ($this->_data as $item) {
			$track = $tasklist->addChild('item');
			foreach ($item as $catkey => $catvalue){
				$track->addChild($catkey,(string)$catvalue);
			}
		}
		
		$tasklist->asXML("../data/tasks.xml");
	}

}