<?php 
    class Post {
        private $conn;
        private $table = 'posts';

        public function __construct($db) {
            $this->conn = $db;
        }

        public function read() {
            $query = 'SELECT '
        }
    }
?>