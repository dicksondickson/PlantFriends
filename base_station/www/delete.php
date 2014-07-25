<?php
/**
 *
 *
 *
*/
require('main.php');

if(!isset($_GET['id']) or !is_numeric($_GET['id'])) {
	header('Location: admin.php');
}
$n = $admin->getNode($_GET['id']);
?>
<!DOCTYPE html>
<html>
<head>
	<title>PlantFriends - Administration page</title>
	<link href="//maxcdn.bootstrapcdn.com/bootswatch/3.2.0/cosmo/bootstrap.min.css" rel="stylesheet">
	<style type="text/css">
	body {
		padding-top: 20px;
		padding-bottom: 20px;
	}
	.navbar {
		margin-bottom: 20px;
	}
	</style>
</head>
<body>
	<div class="container">
		<div class="navbar navbar-default">
			<ul class="nav navbar-nav">
				<li><a href="admin.php">Show all nodes</a></li>
				<li><a href="add.php">Add a new node</a></li>
			</ul>
		</div>
		<h2 class="page-header">Delete node</h2>
		<form method="POST" action="admin.php">
			<div class="form-group">
				<label for="nodeid">NodeID</label>
				<input class="form-control" type="number" readonly name="nodeid" value="<?php echo $n->NodeID; ?>"></input>
			</div>
			<div class="form-group">
				<label for="alias">Alias</label>
				<input class="form-control" type="text" readonly name="alias" value="<?php echo $n->Alias; ?>"></input>
			</div>
			<p><strong>Are you sure?</strong></p>
			<p><input class="btn btn-default" type="submit" name="deletenode" value="Yes, delete it"> <a href="admin.php"><button class="btn btn-default">No!</button></a>
		</form>
	</div>
	</div>
</body>
</html>