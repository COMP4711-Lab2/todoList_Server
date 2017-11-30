<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends Application
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/
	 * 	- or -
	 * 		http://example.com/welcome/index
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->data['pagebody'] = 'homepage';
		
		$tasks = $this->tasks->all();   // get all the tasks

		// count how many are not done
		$count = 0;
		foreach($tasks as $task) {
				if ($task->status != 2) $count++;
		}
		// and save that as a view parameter
		$this->data['remaining_tasks'] = $count;
		
		
		// process the array in reverse, until we have five
		$count = 0;
		foreach(array_reverse($tasks) as $task) {
			$task->priority = $this->app->priority($task->priority);
			$display_tasks[] = (array) $task;
			$count++;
			if ($count >= 5) break;
		}
		$this->data['display_tasks'] = $display_tasks;

		$this->render(); 
	}

}
