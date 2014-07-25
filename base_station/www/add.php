<?php
/**
 *
 *
 *
*/

require('main.php');
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
				<li class="active"><a href="add.php">Add a new node</a></li>
			</ul>
		</div>
		<h2 class="page-header">Add a new node</h2>
		<form method="POST" action="admin.php">
			<div class="form-group">
				<label for="nodeid">NodeID</label>
				<input class="form-control" type="number" name="nodeid"></input>
			</div>
			<div class="form-group">
				<label for="alias">Alias</label>
				<input class="form-control" type="text" name="alias"></input>
			</div>
			<div class="form-group">
				<label for="location">Location</label>
				<input class="form-control" type="text" name="location"></input>
			</div>
			<div class="form-group">
				<label for="plant">Plant</label>
				<input class="form-control" type="text" name="plant"></input>
			</div>
			<div class="form-group">
				<label for="comments">Comment</label>
				<input class="form-control" type="text" name="comments"></input>
			</div>
			<div class="form-group">
				<input class="btn btn-default" type="submit" name="addnode" value="Add Sensor Node"></input>
			</div>
		</form>
	</div>
</body>
</html>