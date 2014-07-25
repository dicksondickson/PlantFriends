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
	<link href="/bootstrap.min.css" rel="stylesheet">
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
		<h2 class="page-header">Update the node</h2>
		<form method="POST" action="admin.php">
			<div class="form-group">
				<label for="nodeid">NodeID</label>
				<input class="form-control" type="number" readonly name="nodeid" value="<?php echo $n->NodeID; ?>"></input>
			</div>
			<div class="form-group">
				<label for="alias">Alias</label>
				<input class="form-control" type="text" name="alias" value="<?php echo $n->Alias; ?>"></input>
			</div>
			<div class="form-group">
				<label for="location">Location</label>
				<input class="form-control" type="text" name="location" value="<?php echo $n->Location; ?>"></input>
			</div>
			<div class="form-group">
				<label for="plant">Plant</label>
				<input class="form-control" type="text" name="plant" value="<?php echo $n->Plant; ?>"></input>
			</div>
			<div class="form-group">
				<label for="comments">Comment</label>
				<input class="form-control" type="text" name="comments" value="<?php echo $n->Comments; ?>"></input>
			</div>
			<div class="form-group">
				<input class="btn btn-default" type="submit" name="updatenode" value="Update Sensor Node"></input>
			</div>
		</form>
	</div>
	</div>
</body>
</html>