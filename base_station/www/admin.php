<?php
//
// Plant Friends Admin Interface
// Insert and delete sensor nodes in the NodeIndex table
// Extremely alpha, use with care.
//
// Put it in your /var/www/admin dir.
//
// Dickson Chow
// http://dicksonchow.com
//
// First Release: June 24, 2014.
// Updated: June 26, 2014.
//
// MIT License
// http://opensource.org/licenses/mit-license.php
//
require('main.php');

if (isset($_POST["addnode"])) {
	$nodeid = $_POST['nodeid'];
	$alias = $_POST['alias'];
	$location = $_POST['location'];
	$plant = $_POST['plant'];
	$comment = $_POST['comments'];

	$admin->addNode($nodeid, $alias, $location, $plant, $comment);
	$message = "Node added with success";
} elseif (isset($_POST["updatenode"])) {
	$nodeid = $_POST['nodeid'];
	$alias = $_POST['alias'];
	$location = $_POST['location'];
	$plant = $_POST['plant'];
	$comment = $_POST['comments'];

	$admin->updateNode($nodeid, $alias, $location, $plant, $comment);
	$message = "Node updated with success";
} elseif (isset($_POST["deletenode"])) {
	$nodeid = $_POST['nodeid'];
	$alias = $_POST['alias'];

	$admin->deleteNode($nodeid, $alias);
	$message = "Node deleted with success";
}

$nodes = $admin->getAllNodes();
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
				<li class="active"><a href="admin.php">Show all nodes</a></li>
				<li><a href="add.php">Add a new node</a></li>
			</ul>
		</div>
		<?php if(isset($message)): ?>
			<div class="alert alert-success"><?php echo $message; ?></div>
		<?php endif; ?>
		<h2>Crazy rough administration page.</h2>
		<hr>
		<h3>Node List</h3>
		<table class="table table-bordered">
			<colgroup>
				<col style="width:20%"></col>
				<col style="width:20%"></col>
				<col style="width:20%"></col>
				<col style="width:20%"></col>
				<col style="width:20%"></col>
			</colgroup>
			<thead>
				<th>Node ID</th>
				<th>Alias</th>
				<th>Location</th>
				<th>Plant type</th>
				<th>Actions</th>
			</thead>
			<tbody>
			<?php foreach ($nodes as $n): ?>
				<tr>
					<td><?php echo $n['NodeID']; ?></td>
					<td><?php echo $n['Alias']; ?></td>
					<td><?php echo $n['Location']; ?></td>
					<td><?php echo $n['Plant']; ?></td>
					<td><a href="update.php?id=<?php echo $n['NodeID']; ?>">Update</a> | <a href="delete.php?id=<?php echo $n['NodeID']; ?>">Delete</a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</body>
</html>