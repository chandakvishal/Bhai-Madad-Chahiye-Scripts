<?php
class DB_Connect {
    protected $con;
    // constructor
    function __construct() {
    }
    // destructor
    function __destruct() {
        // $this->close();
    }
    // Connecting to database
    public function connect() {
        require_once 'config.php';
        // connecting to mysql
        $con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

      // $query = "INSERT INTO users(unique_id, fullname, phone, email, username, encrypted_password, salt, created_at) VALUES(1, 'vishal', '74180365', 'asda', 'uname', 'encrypted_password', 'salt', NOW());";
      // echo "<pre>Debug: $query</pre>\m";
      // $result = mysqli_query($con, $query);
      // if ( false===$result ) {
      //     printf("error: %s\n", mysqli_error($con));
      // }
      // else {
      //     echo 'done.';
      // }

        // // selecting database
        // mysql_select_db();

        // return database handler
      return $con;
  }
    // Closing database connection
  public function close() {
    mysql_close();
}
}
?>