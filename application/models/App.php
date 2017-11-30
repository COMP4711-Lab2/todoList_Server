<?php

/**
 * Domain-specific lookup tables
 */
class App extends CI_Model
{

	// task flags
	private $flags = [
		1 => 'Urgent',
	];
	// task groups
	private $groups = [
		1	 => 'house',
		2	 => 'school',
		3	 => 'work',
		4	 => 'family'
	];
	// task priorities
	private $priorities = [
		1	 => 'low',
		2	 => 'medium',
		3	 => 'high'
	];
	// task sizes
	private $sizes = [
		1	 => 'small',
		2	 => 'medium',
		3	 => 'large'
	];
	// task ststus
	private $statuses = [
		1	 => 'in progress',
		2	 => 'complete',
	];

	public function __construct()
	{
		parent::__construct();
	}

	public function flag($which = null)
	{
		return isset($which) ?
			(isset($this->flags[$which]) ? $this->flags[$which] : '') :
			$this->flags;
	}

	public function group($which = null)
	{
		return isset($which) ?
			(isset($this->groups[$which]) ? $this->groups[$which] : 'Unknown') :
			$this->groups;
	}

	public function priority($which = null)
	{
		return isset($which) ?
			(isset($this->priorities[$which]) ? $this->priorities[$which] : 'Unknown') :
			$this->priorities;
	}

	public function size($which = null)
	{
		return isset($which) ?
			(isset($this->sizes[$which]) ? $this->sizes[$which] : 'Unknown') :
			$this->sizes;
	}

	public function status($which = null)
	{
		return isset($which) ?
			(isset($this->statuses[$which]) ? $this->statuses[$which] : '') :
			$this->statuses;
	}

}
