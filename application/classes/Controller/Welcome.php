<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

	public function action_index()
	{   
	  
	  $view = View::factory('demo_img');
	  $this->response->body($view);
	}

	public function action_upload1()
	{
	  print_r($_FILES);
	  print_r($_POST);
	}

	public function action_upload()
	{
		$view = View::factory('demo_success');
		$error_message = NULL;
		$filename = NULL;
		
		if($this->request->method() == Request::POST)
		{
		  if(isset($_FILES['avatar']))
		  {
		    $filename = $this->_save_image($_FILES['avatar']);
		  }
		}

        echo '<pre>';
		print_r($filename);
		exit();
		
		if(!$filename)
		{
		  $error_message = 'There was a problem while uploading the image.Make sure it is uploaded and must be JPG/PNG/GIF file.';
		}
		
		$view->uploaded_file = $filename;
		$view->error_message = $error_message;
		$this->response->body($view);
	}

	protected function _save_image($image)
	{
		if (
			! Upload::valid($image) OR
			! Upload::not_empty($image) OR
			! Upload::type($image, array('jpg', 'jpeg', 'png', 'gif')))
		{
			return FALSE;
		}
		
		$directory = DOCROOT.'uploads/';
		
		
		if ($file = Upload::save($image, NULL, $directory))
		{
			$filename = strtolower(Text::random('alnum', 20)).'.jpg';
			
			Image::factory($file)
				->resize(200, 200, Image::AUTO)
				->save($directory.$filename);

			// Delete the temporary file
			unlink($file);
			
			return $filename;
		}
		
		return FALSE;
	}




} // End Welcome
