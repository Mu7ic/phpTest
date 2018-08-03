<?php require "Control.php"; ?>
<html>
<head>	

<style type="text/css">
	.date{
		float: left;
		width: 150px;
	}
	.tabl{
		float: left;
		width: 950px;
	}
	.bl{
		border: 1px solid #000;
	}
</style>
</head>
<body>
<div class="content">
<div class="date">
	<form method="post" action="">
	<input type="date" name="date">
	<input type="submit" value="Поиск">
	</form>
</div>
<div class="tabl">
<?php
//https://api.vk.com/method/messages.getDialogs?v=5.41&access_token=98dc54197c9003c79fe93db07737f67c1c4e6101123b0b7cbed9ce6dafa5fad11291bfab2aba1cfc9f200&count=10&offset=0

//98dc54197c9003c79fe93db07737f67c1c4e6101123b0b7cbed9ce6dafa5fad11291bfab2aba1cfc9f200
if(isset($_POST['date']) && $_POST['date']!=""){
	$obj=new Messages;
	echo $obj->getMessages($_POST['date']);
}

//---------------------------------------------------
?>
</div>
</div>	
</body>
</html>

