<?php
    namespace quiz\admin;

    class PaymentUp {
        private $link;

        public function __construct($link) {
            $this->link = $link; // Store database connection
        }

        public function retrieveUsers() {
            $query = "SELECT username FROM registration";
            $result = mysqli_query($this->link, $query);
            $users = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                $users[] = $row;
            }

            return $users;
        }
        

        public function getUniqueIdByUsername($user) {
            $query = "SELECT unique_id FROM registration WHERE username = ?";
            $stmt = mysqli_prepare($this->link, $query);
            mysqli_stmt_bind_param($stmt, 's', $user);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_assoc($result);
        }

        public function addCustomerDetails($username, $email, $exam, $status) {
            $query = "INSERT INTO customer_details (username, email, exam, status) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($this->link, $query);
            mysqli_stmt_bind_param($stmt, 'ssss', $username, $email, $exam, $status);
            return mysqli_stmt_execute($stmt);
        }

        public function getCategories() {
            $query = "SELECT id, category FROM exam_category"; // Assuming 'id' and 'name' are columns
            $result = mysqli_query($this->link, $query);
            $categories = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                $categories[] = $row;
            }

            return $categories;
        }
    }