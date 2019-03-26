<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>Simple - File Uploading App</title>
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
      <link rel="stylesheet" href="<?php echo URL::base().'assets/css/style.css'; ?>">
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
   </head>
   <body>
      <div class="main-wrraper">
         <div class="inner-wrraper">
            <div class="card">
               <div class="card-header">
                  <h3>Documents info</h3>
                  <button class="btn btn-success" id="myBtn"><i class="fas fa-plus"></i> Add Document</button>
               </div>
               <div class="card-body">
                  <table>
                     <thead>
                        <tr>
                           <th>Title</th>
                           <th>Thumbnail</th>
                           <th>File Name</th>
                           <th>Date Added</th>
                           <th>Actions</th>
                        </tr>
                     </thead>
                     <tbody class="sk-new-row">
					   
					   <?php if(!empty($MediaRec)){ ?>
					   <?php foreach($MediaRec as $Media){ ?>

                        <tr id="skrow_<?=$Media['id']?>">
                           <td><?=stripslashes($Media['title'])?></td>
                           <td>
                              <div class="douc-thumb">
							  <?php if(!empty($Media['thumbnail']) && file_exists('uploads/'.$Media['thumbnail'])){ ?>
							  <img src="<?php echo URL::base().'uploads/'.$Media['thumbnail']; ?>" alt="<?=$Media['filename']?>">
							  <?php }else{ ?>
                              <img src="<?php echo URL::base().'assets/images/thumb.png'; ?>" alt="<?=$Media['filename']?>">
							  <?php } ?>

							  </div>
                           </td>
                           <td><?=stripslashes($Media['filename'])?></td>
                           <td><?=stripslashes(date("d-m-Y",strtotime($Media['added_date'])))?></td>
                           <td>
							  <button class="btn btn-info sk-edit" data-id="<?=$Media['id']?>" data-img="<?=stripslashes($Media['thumbnail'])?>" data-title="<?=stripslashes($Media['title'])?>"><i class="fas fa-pen"></i></button>
							  
							  <button class="btn btn-danger sk-delete" data-id="<?=$Media['id']?>" data-img="<?=stripslashes($Media['thumbnail'])?>" ><i class="fas fa-times"></i></button>
                           </td>
                        </tr>
						<?php } ?>
						<?php } ?> 					
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>

      <!-- Your Document | Popup - Start-->
      <div id="myModal" class="modal">
         <div class="modal-content">
            <h3><span class="main-head">Add</span> Your Document <span class="close">&times;</span></h3>
			<div class="errormsg"></div>
            <form action="" method="post" accept="" id="document_info">
               <div class="form-wrrap">
                  <label>Document Title</label>
                  <input type="text" name="doc_title" id="doc_title" placeholder="Enter document title">
				  <input type="hidden" name="qazxswedc" id="qazxswedc">
				  <input type="hidden" name="rfvtgbyhn" id="rfvtgbyhn">
               </div>
               <div class="form-wrrap">
                  <label>Document Title</label>
                  <div class="input-container">
                     <input type="file" id="doc_file" name="doc_file">
                     <button class="browse-btn" type="button">
                     Browse Files
                     </button>
                     <span class="file-info">Upload a file</span>
                  </div>
               </div>
               <div class="form-wrrap text-center">
                  <button class="btn btn-success sk-custom-save file-upload-process" type="button">Save Document</button>
				  <button class="btn btn-success sk-custom-edit file-edit-process" type="button">Update Document</button>
               </div>
            </form>
         </div>
      </div>
	  <!-- Your Document | Popup - End-->

      <script>
         var modal = document.getElementById('myModal');
         var btn   = document.getElementById("myBtn");
         var span  = document.getElementsByClassName("close")[0];         
         
		 btn.onclick = function(){ 
			 $('.main-head').html('Add');
			 $("#doc_title").val('');
             fileInfo.innerHTML = '';
			 $('.sk-custom-save').show();
		     $('.sk-custom-edit').hide();
			 $("#qazxswedc").val('');
			 $("#rfvtgbyhn").val('');
			 modal.style.display = "block"; 
		 
		 }

         span.onclick = function() { modal.style.display = "none"; }
         window.onclick = function(event){		 
           if(event.target == modal) {
		     modal.style.display = "none";
		   }
		 }


		 // input type file 
		const uploadButton = document.querySelector('.browse-btn');
		const fileInfo = document.querySelector('.file-info');
		const realInput = document.getElementById('doc_file');

		uploadButton.addEventListener('click', (e) => {
		  realInput.click();
		});

		realInput.addEventListener('change', () => {
		  const name = realInput.value.split(/\\|\//).pop();
		  const truncated = name.length > 20 
			? name.substr(name.length - 20) 
			: name;
		  
		  fileInfo.innerHTML = truncated;
		});

		//File Upload Process Start
		$(".file-upload-process").click(function(){     
		       console.log('in.........');
			   var fd    = new FormData();
               var files = $("#doc_file")[0].files[0];	  
	           fd.append('file',files);   
	           fd.append('doc_title',$("#doc_title").val());
			   $('.errormsg').html('');
		       $.ajax({			     
	             url: "<?php echo URL::site('gallery/upload'); ?>",
                 type: 'post',
                 data: fd,
                 async:false,
                 dataType:'json',
                 contentType: false,
                 processData: false,
                 success: function(data){
				   if(data.status==true){
				     $(".sk-new-row").prepend(data.html);
				     document.getElementById("document_info").reset();
				     fileInfo.innerHTML = '';
					 modal.style.display = "none";					 
				   }else{
				    $('.errormsg').html("<div class='alert-msg'>"+ data.html +"<div>");
				   }
				 },			   
			   });		
		});

		//Update Process Start
		$(".file-edit-process").click(function(){     
		       var id = $("#qazxswedc").val();
			   var fd    = new FormData();
               var files = $("#doc_file")[0].files[0];	  
	           fd.append('file',files);   
	           fd.append('doc_title',$("#doc_title").val());
			   fd.append('old_id',$("#qazxswedc").val());
			   fd.append('old_img',$("#rfvtgbyhn").val());
			   $('.errormsg').html('');
		       $.ajax({			     
	             url: "<?php echo URL::site('gallery/edit'); ?>",
                 type: 'post',
                 data: fd,
                 async:false,
                 dataType:'json',
                 contentType: false,
                 processData: false,
                 success: function(data){
				   if(data.status==true){
				     $("#skrow_"+id).html(data.html);
				     document.getElementById("document_info").reset();
				     fileInfo.innerHTML = '';
					 modal.style.display = "none";					 
				   }else{
				    $('.errormsg').html("<div class='alert-msg'>"+ data.html +"<div>");
				   }
				 },			   
			   });		
		});

       $( ".sk-new-row" ).on( "click", ".sk-delete" , function(){		
			 var Id  = $(this).data('id');
			 var Img = $(this).data('img');			
			 if(Id != '' && Id > 0)
			 {
			   $.ajax({			     
	             url: "<?php echo URL::site('gallery/remove'); ?>",
                 type: 'post',
                 data: { id : Id, img : Img },
                 async:false,
                 dataType:'json',
                 
                 success: function(data){
				   if(data.status==true){
				         $("#skrow_"+Id).hide();								 
				   }else{
				         alert('Error Occured,Please refresh the page and try again.');
				   }
				 },			   
			   });
			 }		
		});


		$( ".sk-new-row" ).on( "click", ".sk-edit" , function(){		  
		  $('.main-head').html('Update');
		  var Title = $(this).data('title');
		  var Img   = $(this).data('img');
		  var Id    = $(this).data('id');
          $("#doc_title").val(Title);
          fileInfo.innerHTML = Img;		 
		
		  $('.sk-custom-save').hide();
		  $('.sk-custom-edit').show();

		  $("#qazxswedc").val(Id);
		  $("#rfvtgbyhn").val(Img);
		  modal.style.display = "block";		
		});

      </script>  
   </body>
</html>