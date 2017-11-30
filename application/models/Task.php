<?php

class Task extends Entity {
	private $Task;
	private $Priority;
	private $Size;
	private $Group;
	
	function setTask($value)
	{
		if(strlen($value) > 64){
			return false;
		}
		
		for($i = 0; $i < strlen($value); $i++){
			if(!(is_numeric($value[$i]) || 
				ctype_alpha($value[$i]) || ctype_space ($value[$i]))) {
				return false;
			}
		}
		
		$this->Task = $value;
		return true;
		
	}
	function setPriority($value)
	{
		switch($value){
			case 1:
				return $this->Priority = $value;
				break;
			case 2:
				return $this->Priority = $value;
				break;
			case 3:
				return $this->Priority = $value;
				break;					
		}
		return -1;
	}
	
	function setSize($value)
	{
		switch($value){
			case 1:
				return $this->Size = $value;
				break;
			case 2:
				return $this->Size = $value;
				break;
			case 3:
				return $this->Size = $value;
				break;					
		}
		return -1;
	}
	
	function setGroup($value)
	{
            switch($value){
				case 1:
					return $this->Group = $value;
					break;
				case 2:
					return $this->Group = $value;
					break;
				case 3:
					return $this->Group = $value;
					break;		
				case 4:
					return $this->Group = $value;
					break;					
			}
			return -1;
	}
	
	/*
	// provide form validation rules
	public function rules()
	{
		$config = array(
			['field' => 'task', 'label' => 'TODO task', 'rules' => 'alpha_numeric_spaces|max_length[64]'],
			['field' => 'priority', 'label' => 'Priority', 'rules' => 'integer|less_than[4]'],
			['field' => 'size', 'label' => 'Task size', 'rules' => 'integer|less_than[4]'],
			['field' => 'group', 'label' => 'Task group', 'rules' => 'integer|less_than[5]'],
		);
		return $config;
	}
         */
}
