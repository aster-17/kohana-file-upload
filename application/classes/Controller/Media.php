<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Media extends Controller_Template {

	public function action_index()
	{
	  $Media = ORM::factory('Media')->find_all();
	  $View  = View::factory('media')->bind('media',$Media);	 
	  //$this->response->body($View);
	  $this->template->content = $View;
	}
} // End Media
