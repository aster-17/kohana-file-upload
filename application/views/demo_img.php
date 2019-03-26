<html>
	<head>
		<title>Upload Avatar</title>
	</head>
	<body>
	<?php if(!empty($view->error_message)){ echo $view->error_message; } ?>
		<h1>Upload your avatar</h1>
		<form id="upload-form" action="<?php echo URL::site('welcome/upload') ?>" method="post" enctype="multipart/form-data">
			<p>Choose file:</p>
			<p><input type="file" name="avatar" id="avatar" /></p>
			<p><input type="submit" name="submit" id="submit" value="Upload" /></p>
		</form>
        <hr/>

		<form id="upload-form" action="" method="post" enctype="multipart/form-data">
			<b>Ajax file upload</b>
			<p><input type="file" name="avatar_1" id="avatar_1" /></p>
			<p><input type="hidden" name="test_ex" id="test_ex" value="one two ka four"></p>
			<p><input type="button" name="submit_1" id="submit_1" value="Upload" /></p>
		</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $("#submit_1").click(function(){
   
    //==================================  
    var fd       = new FormData();
    var files    = $("#avatar_1")[0].files[0];	  
	fd.append('file',files);   
	fd.append('test_ex',$("#test_ex").val());

	 

	 console.log(files);
   
    $.ajax({	   
	   url: "<?php echo URL::site('welcome/upload1'); ?>",
       type: 'post',
       data: fd,
       async:false,
       dataType:'json',
       contentType: false,
       processData: false,
       success: function(response){
				console.log("......success....");			
			},
	
	});


   //===================================

  });
});
</script>


	</body>
</html>