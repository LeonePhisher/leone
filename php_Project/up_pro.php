<?php
require_once 'database.php';
$db=new Database();
 
session_start();
$error=[];

$id=$_POST['id']??'';
$fullname=$_POST['fullname']??'';
$email=$_POST['email']??'';
$age=$_POST['age']??'';
$phone=$_POST['phone']??'';
$gender=$_POST['gender']??'';
$city=$_POST['city']??'';

if(empty($fullname)||!preg_match("/^['A-Za-z\s']+$/",$fullname)){
    $error['fullname']="invalid name!";
}
if(empty($phone)|| !preg_match("/^['0-9']+$/",$phone)){
    $error['phone']="invalid name!";
}
if(empty($age)||!preg_match("/^['0-9']+$/",$age)){
    $error['age']="invalid name!";
}
if(empty($email)||!filter_var($email,FILTER_VALIDATE_EMAIL)){
    $error ['email']="invalid email";
}
if(empty($city)|| !in_array($city,['Accra','Kumasi','Ho'])){
    $error ['city']="invalid city";
}
if(empty($gender)){
    $error ['gender']="invalid gender";
}
if(!empty($error)){
    $_SESSION['error']=$error;
    header('location:index.php');
    exit;
}else {
    $db->update($fullname,$email,$age,$phone,$gender,$city,$id);
    header('location:view.php');
    exit;
}

?>