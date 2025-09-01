<?php
require_once 'database.php';
$db=new Database();
$booking=$db->getAll();

?>
<!DOCTYPE html>
<html>
<head>
  <title>View Users</title>
<!-- Dark Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body {
    background-color: #121212;
    color: #f8f9fa;
  }

  .card {
    background-color: #1e1e1e;
    color: #f8f9fa;
    border: none;
  }
  .btn-primary, .btn-warning, .btn-success, .btn-info, .btn-danger {
    color: #fff;
  }
  .table {
    color: #f8f9fa;
  }
  .table-light {
    background-color: #2c2c2c;
  }
  .alert-info {
    background-color: #2a2a2a;
    color: #9bd0ff;
    border: 1px solid #0dcaf0;
  }
</style>
</head>
<body>
<div class="container mt-5">
  <div class="d-flex justify-content-between mb-3">
    <h4 class="text-primary">Users</h4>
    <a href="form.php" class="btn btn-success">+ Add New</a>
  </div>

  <form method="GET" action="/search" class="input-group mb-3">
    <input type="text" name="query" class="form-control" placeholder="Search users...">
    <button class="btn btn-outline-primary" type="submit">Search</button>
  </form>

  <!-- Show if database is empty -->
  <!-- Replace with server-side condition -->
  <div class="alert alert-info">No users found in the database.</div>

  <!-- Table of data (render if data exists) -->
  <table class="table table-bordered table-hover shadow-sm">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Age</th>
        <th>Phone</th>
        <th>Gender</th>
        <th>City</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($booking as $data): ?>
      <tr>
        <td><?= $data['id']??'' ?></td>
        <td><?= $data['fullname']??'' ?></td>
        <td><?= $data['email']??'' ?></td>
        <td><?= $data['age']??'' ?></td>
        <td><?= $data['phone']??'' ?></td>
        <td><?= $data['gender']??'' ?></td>
        <td><?= $data['city']??'' ?></td>
        <td>
          <a href="update.php?id=<?=$data['id'] ??''?>" class="btn btn-sm btn-info" >Update</a>
          <a href="delete.php?id=<?=$data['id'] ??''?>" class="btn btn-sm btn-danger" onclick="return confirm('do u want to delete')">Delete</a>
        </td>
      </tr>
    </tbody>
    <?php endforeach; ?>
  </table>
</div>
</body>
</html>
