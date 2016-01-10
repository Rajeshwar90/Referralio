<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/pathtotheclass/upload_class.php';

$error = '';
$image = '';
$copy_link = '';
		
if (isset($_POST['Submit'])) {
	$my_upload = new file_upload;
	$my_upload->upload_dir = $_SERVER['DOCUMENT_ROOT']."/modal-upload/files/";
	$my_upload->extensions = array(".png", ".jpg", ".gif"); // allowed extensions
	$my_upload->rename_file = true;
	$my_upload->the_temp_file = $_FILES['upload']['tmp_name'];
	$my_upload->the_file = $_FILES['upload']['name'];
	$my_upload->http_error = $_FILES['upload']['error'];
	if ($my_upload->upload()) {
		$image = $my_upload->file_copy;
		$copy_link = ' | <a id="closelink" href="#" onclick="self.parent.tb_remove();">Pass file name</a>';
	} 
	$error = $my_upload->show_error_string();
}
?>
<html>
<head>
<title>Image upload</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="jquery.min.js"></script>
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<script type="text/javascript">
$(document).ready(function() { 
	
	$("#closelink").click(function() {
		$("#myfile", top.document).val('<?php echo $image; ?>');
	});
	
});
</script>
</head>
<body>
	<div id="uploadcontainer">
		<h1>Image Upload</h1>
		<form action="upload.php" method="post" enctype="multipart/form-data">
			<p>
				<label for="upload">Select file</label>
				<input name="upload" type="file" size="15" />
				<br />
				<input type="submit" name="Submit" value="Upload" />
			</p>
		</form>
		<p><?php echo $error; ?></p>
		<p><a href="#" onclick="self.parent.tb_remove();">Cancel</a><?php echo $copy_link; ?></p>
	</div>
</body>
</html>
