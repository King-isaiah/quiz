<?php
// include "../../connection.php";

if (isset($_GET['username'])) {
    $username = $_GET['username'];
     function getUniqueId($link, $username) {
                                                $stmt = mysqli_prepare($link, "SELECT unique_id FROM registration WHERE username = ?");
                                                mysqli_stmt_bind_param($stmt, 's', $username);
                                                mysqli_stmt_execute($stmt);
                                                mysqli_stmt_bind_result($stmt, $uniqueId);
                                                mysqli_stmt_fetch($stmt);
                                                mysqli_stmt_close($stmt);
                                                
                                                return $uniqueId;
                                            }
    echo getUniqueId($link, $username); // Call the function and echo the result
}
?>