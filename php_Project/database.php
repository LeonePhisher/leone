<?php
class Database{
    private $db;
    public function __construct(){
        $this->db=new PDO("mysql:host=localhost;dbname=jedo",'root',"");
        
    }
    public function insert($fullname,$email,$age,$phone,$gender,$city) {
        $query=$this->db->prepare("INSERT INTO `try`(`fullname`, `email`, `age`, `phone`, `gender`, `city`) VALUES (?,?,?,?,?,?)");
        $query->execute([$fullname,$email,$age,$phone,$gender,$city]);
    }
    public function update($fullname,$email,$age,$phone,$gender,$city,$id) {
         $query=$this->db->prepare("UPDATE `try` SET  `fullname`=?,`email`=?,`age`=?,`phone`=?,`gender`=?,`city`=? WHERE id=?");
          $query->execute([$fullname,$email,$age,$phone,$gender,$city,$id]); 
}
    public function getAll(){
        $query=$this->db->prepare("SELECT * FROM `try`");
         $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getone($id){
        $query=$this->db->prepare("SELECT * FROM `try` WHERE id=?");
         $query->execute([$id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    public function delete($id){
        $query=$this->db->prepare("DELETE  FROM `try` WHERE id=?");
         $query->execute([$id]);
    }
}
?>


