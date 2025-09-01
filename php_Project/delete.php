<?php
require_once 'database.php';
$db=new Database();
$id=$_GET['id']??'';
$db->delete($id);
header('location:view.php');
exit;
?>