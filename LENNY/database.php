<?php
 class Database {
    private $db;

    public function __construct($host, $user, $pass, $database) {
        try {
            $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
            
            $this->db = new PDO($dsn, $user, $pass);
            
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }


    public function saveBooking($data) {
        $query = $this->db->prepare(
            "INSERT INTO cars (Fullname, email, Phone, Vehicle_type, Pickup_date, Return_date, Comment) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        return $query->execute([
            $data['Fullname'], 
            $data['email'], 
            $data['Phone'], 
            $data['Vehicle_type'], 
            $data['Pickup_date'], 
            $data['Return_date'], 
            $data['Comment']
        ]);
    }

    // Method to get a single booking by ID
    public function getBooking($id) {
        
        $query = $this->db->prepare("SELECT * FROM cars WHERE id = ?");
        $query->execute([$id]);
      return $query->fetch(PDO::FETCH_ASSOC);
    }

    // Method to get all bookings
    public function getAllBookings() {
        $query = $this->db->query("SELECT * FROM cars");
        
        // Return all results as array of associative arrays
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to update an existing booking
    public function updateBooking($id, $data) {
        // Prepare update statement with placeholders
        $query = $this->db->prepare(
            "UPDATE cars SET 
                Fullname = ?, 
                email = ?, 
                Phone = ?, 
                Vehicle_type = ?, 
                Pickup_date = ?, 
                Return_date = ?, 
                Comment = ? 
            WHERE id = ?"
        );
        
        return $query->execute([
            $data['Fullname'], 
            $data['email'], 
            $data['Phone'], 
            $data['Vehicle_type'], 
            $data['Pickup_date'], 
            $data['Return_date'], 
            $data['Comment'],
            $id
        ]);
    }

    // Method to delete a booking
    public function deleteBooking($id) {
        // Prepare delete statement
        $query = $this->db->prepare("DELETE FROM cars WHERE id = ?");
        
        // Execute with the ID parameter
        return $query->execute([$id]);
    }
}
?>