<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Gallery extends Controller{

	public function action_index()
	{
	  $query = DB::select()->from('media')->order_by('id', 'DESC')->execute();
	  $Media = $query->as_array();
	  $View  = View::factory('gallery/index')->bind('MediaRec',$Media);	 
	  $this->response->body($View); 
	}

	public function action_upload()
	{
	  $Status      = false;
	  $Html        = '';
	  $doc_title   = "";
	  $newfilename = "";
	  $InsertId    = "";
	  $added_date  = date("Y-m-d H:i:s");
	  $doc_title = addslashes($_POST['doc_title']);
	  

	  if(empty($doc_title))
	  {
	    $Html = 'Please enter Title.';
	  }
	  else if(empty($_FILES))
	  {
	    $Html = 'Please upload image.';
	  }
	  else
	  {
		$filename     = $_FILES["file"]['name'];
        $file_type    = strtolower($_FILES["file"]["type"]);
        $templocation = $_FILES["file"]["tmp_name"];
        $ext          = strtolower(substr($filename, strrpos($filename, '.') + 1));

		$store = "uploads/";
        if(!file_exists($store)){
		   @mkdir($store, 0777); 		
		}

		if($ext !="jpg" && $ext !="png" && $ext !="jpeg" && $ext !="gif" && $ext !='svg')
		{		
		  $Html = 'There was a problem while uploading the image.Make sure it is uploaded and must be JPG/PNG/GIF/SVG file.';
		}
		else
		{		  
		  if(!preg_match("/(.+)\.(.*?)\Z/", $filename, $r)){}
		  $newfilename = $this->convert2link($r[1]).'.'.$r[2];
		  $newfilename = $this->new_name_when_exist($store,$newfilename);
		  if(move_uploaded_file($templocation, $store.$newfilename))
		  {
		    $MediaObj = ORM::factory('media');
			$MediaObj->title      = $doc_title;
			$MediaObj->thumbnail  = $newfilename;
			$MediaObj->filename   = $newfilename;
			$MediaObj->added_date = $added_date;
			$MediaObj->save();	
			$InsertId = $MediaObj->id;
			$Status = true;
			$Html   = '<tr id="skrow_'.$InsertId.'">';
			$Html  .= '<td>'.$doc_title.'</td>';
			$Html  .= '<td><div class="douc-thumb"><img src="'.URL::base().'uploads/'.$newfilename.'" alt=""></div></td>';
			$Html  .= '<td>'.$newfilename.'</td>';
			$Html  .= '<td>'.date('d-m-Y',strtotime($added_date)).'</td>';

			$Html  .= '<td>
			              <button class="btn btn-info sk-edit" data-id="'.$InsertId.'" data-img="'.$newfilename.'" data-title="'.$doc_title.'"><i class="fas fa-pen"></i></button>
                          
						  <button class="btn btn-danger sk-delete" data-id="'.$InsertId.'" data-img="'.$newfilename.'"><i class="fas fa-times"></i></button>
					  </td>';
			$Html  .= '</tr>';
		  }
		  else
		  {
		    $Html='There was a problem while uploading the image.Make sure it is uploaded and must be JPG/PNG/GIF/SVG file.';
		
		  }		
		}	  
	  }

	   echo json_encode(array('status'=>$Status,
		                      'html'=>$Html,
		                      'date'=>$added_date,
		                      'file'=>$newfilename,
		                      'title'=>$doc_title,
		                      'id'=>$InsertId));

	}

	public function action_edit()
	{
	  $Status      = false;
	  $Html        = '';
	  $doc_title   = "";
	  $newfilename = "";
	  $InsertId    = "";
	  $added_date  = date("Y-m-d H:i:s");
	  $doc_title   = addslashes($_POST['doc_title']);
	  $Id          = addslashes($_POST['old_id']);
	  $OldImg      = addslashes($_POST['old_img']);

	  if(empty($Id) || !is_numeric($Id))
	  {
	    $Html = 'Error occured,Please refresh the page and try again.';
	  }
	  else if(empty($doc_title))
	  {
	    $Html = 'Please enter Title.';
	  }
	  else if(empty($_FILES) && empty($OldImg))
	  {
	    $Html = 'Please upload image.';	   
	  }
	  else 
	  {
	    if(!empty($_FILES))
		{
		  $filename     = $_FILES["file"]['name'];
          $file_type    = strtolower($_FILES["file"]["type"]);
          $templocation = $_FILES["file"]["tmp_name"];
          $ext          = strtolower(substr($filename, strrpos($filename, '.') + 1));
		  $store = "uploads/";
          if(!file_exists($store)){
		   @mkdir($store, 0777); 		
		  }

		  if($ext !="jpg" && $ext !="png" && $ext !="jpeg" && $ext !="gif" && $ext !='svg')
		  {		
		    $Html = 'There was a problem while uploading the image.Make sure it is uploaded and must be JPG/PNG/GIF/SVG file.';
		  }
		  else
		  {
		    if(!preg_match("/(.+)\.(.*?)\Z/", $filename, $r)){}
		    $newfilename = $this->convert2link($r[1]).'.'.$r[2];
		    $newfilename = $this->new_name_when_exist($store,$newfilename);
		    if(move_uploaded_file($templocation, $store.$newfilename))
		    {
               $query = DB::update('media')->set(array('title'=>$doc_title,
					                                   'thumbnail'=>$newfilename,
					                                   'filename'=>$newfilename))->where('id', '=', $Id);
			   
               $result = $query->execute();	
			   $Status = true;			 
			   $Html  .= '<td>'.$doc_title.'</td>';
			   $Html  .= '<td><div class="douc-thumb"><img src="'.URL::base().'uploads/'.$newfilename.'" alt=""></div></td>';
			   $Html  .= '<td>'.$newfilename.'</td>';
			   $Html  .= '<td>'.date('d-m-Y',strtotime($added_date)).'</td>';
			   $Html  .= '<td>			   
			                <button class="btn btn-info sk-edit" data-id="'.$Id.'" data-img="'.$newfilename.'" data-title="'.$doc_title.'"><i class="fas fa-pen"></i></button>
						   
						    <button class="btn btn-danger sk-delete" data-id="'.$Id.'" data-img="'.$newfilename.'"><i class="fas fa-times"></i></button>
						  </td>';			 
			}
			else
			{
			  $Html='There was a problem while uploading the image.Make sure it is uploaded and must be JPG/PNG/GIF/SVG file.';	
			}
		  }
		}
		else
		{
		  $query  = DB::update('media')->set(array('title'=>$doc_title))->where('id', '=', $Id);			   
          $result = $query->execute();
		  $Status = true;		  
		  $Html  = '<td>'.$doc_title.'</td>';
		  $Html  .= '<td><div class="douc-thumb"><img src="'.URL::base().'uploads/'.$OldImg.'" alt=""></div></td>';
		  $Html  .= '<td>'.$OldImg.'</td>';
		  $Html  .= '<td>'.date('d-m-Y',strtotime($added_date)).'</td>';
		  $Html  .= '<td>
		               <button class="btn btn-info sk-edit" data-id="'.$Id.'" data-img="'.$OldImg.'" data-title="'.$doc_title.'"><i class="fas fa-pen"></i></button>
					 
					   <button class="btn btn-danger sk-delete" data-id="'.$Id.'" data-img="'.$OldImg.'"><i class="fas fa-times"></i></button>
				    </td>';		 
		}
	  }
	  echo json_encode(array('status'=>$Status,
		                      'html'=>$Html,
		                      'date'=>$added_date,
		                      'file'=>$newfilename,
		                      'title'=>$doc_title,
		                      'id'=>$InsertId));	
	}



	public function action_remove()
	{
	  $Id     = addslashes($_POST['id']);
	  $Img    = addslashes($_POST['img']);
	  $Status = false;
	  if(!empty($Id) && is_numeric($Id))
	  {
	    $query = DB::delete('media')->where('id', 'IN', array($Id));		
		$result = $query->execute();
        if($result==true)
		{
		  if(!empty($Img) && file_exists('uploads/'.$Img))
		  {
		    @unlink('uploads/'.$Img);
		  }
		}
		$Status = true;
	  }

	  echo json_encode(array('status'=>$Status));
	
	}


	function convert2link($string)
    {
		$string = strtolower($string);
		$special_chars[] = 'ö';
		$special_chars[] = 'ü';
		$special_chars[] = 'Ö';
		$special_chars[] = 'Ä';
		$special_chars[] = 'Ü';
		$special_chars[] = 'ä';
		$special_chars[] = 'ü';
		$special_chars[] = 'ö';
		$special_chars[] = 'ß';
		$special_chars[] = 'Ž';
		$special_chars[] = '?';
		$special_chars[] = '.';
		$special_chars[] = ':';
		$special_chars[] = ',';
		$special_chars[] = '_';
		$special_chars[] = '-';
		$special_chars[] = '+';
		$special_chars[] = '&';
		$special_chars[] = '/';
		$special_chars[] = '\\';
		$special_chars[] = ' ';
		$special_chars[] = '"';
		$special_chars[] = '#';
		$special_chars[] = '%';
		$special_chars[] = "'";
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '_';
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '_';
		$special_chars2[] = '_';
		$special_chars2[] = '';
		$special_chars2[] = '_';
		$special_chars2[] = '_';
		$special_chars2[] = '-';
		$special_chars2[] = '_';
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '';
		$string = str_replace($special_chars,$special_chars2,$string);
		return $string;
   }

   function new_name_when_exist($path, $filename)
   {     
    if($pos = strrpos($filename, '.')){
           $name = substr($filename, 0, $pos);
           $ext = substr($filename, $pos);
    }else{
           $name = $filename;
    }

    $newpath = $path.'/'.$filename;
    $newname = $filename;
    $counter = 0;
    while (file_exists($newpath)) {
           $newname = $name .'_'. $counter . $ext;
           $newpath = $path.'/'.$newname;
           $counter++;
     }
    return $newname;   
   }

} // End Gallery
