<?php
session_start();
require_once 'database.php';
$db=new Database();
$id=$_GET['id']??'';
$user=$db->getOne($id)??'';
$error=$_SESSION['error']??'';
session_unset();


?>

<!DOCTYPE html>
<html>
<head><title>Edit User</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body {
    background-color: #121212;
    color: #f8f9fa;
  }
  .form-control, .form-select {
    background-color: #1f1f1f;
    color: #f8f9fa;
    border: 1px solid #333;
  }
  .form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,.25);
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
  <div class="card p-4 shadow-sm">
    <h4 class="text-warning mb-4">Edit User</h4>
    <form action="up_pro.php" method="POST">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Full Name</label>
          <input type="hidden" name="id" class="form-control" value="<?= $user['id'] ?>">
        </div>
          <label class="form-label">Full Name</label>
          <input type="text" name="fullname" class="form-control" value="<?= $user['fullname'] ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= $user['email']?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Age</label>
          <input type="number" name="age" class="form-control" value="<?= $user['age']?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Phone</label>
          <input type="tel" name="phone" class="form-control" value="<?= $user['phone']?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-select">
            <option value="Male" <?= ($user['gender']??'')=='Male'?'selected':'' ?>>Male</option>
            <option value="Female" <?= ($user['gender']??'')=='Female'?'selected':''?>>Male</option>
            <option>Female</option></select>
        </div>
        <div class="col-md-6">
          <label class="form-label">City</label>
          <select id="cities" class="form-control" name="city" value="Lagos">
             <option  class="form-control" value="" >--Select City--</option>
            <option class="form-control" value="Accra"<?= ($user['city']??'')=='Accra'?'selected':'' ?> >Accra</option>
            <option class="form-control" value="Kumasi"<?= ($user['city']??'')=='Kumasi'?'selected':'' ?>>Kumasi</option>
            <option class="form-control" value="Ho"<?= ($user['city']??'')=='Ho'?'selected':'' ?>>Ho</option>
            </select>
        </div>
      </div>
      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-warning">Update</button>
        <a href="view.php" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
