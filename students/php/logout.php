<?php
session_start();    
if(isset($_SESSION['unique_id'])){  
    // Include only Supabase config
    include "../../superbase/config.php";

    $unique_id = $_SESSION['unique_id'];
    
    if(isset($unique_id)){
        $status = "Offline now";
        
        ?>
        <script>
            console.log("Updating status to offline for user: <?php echo $unique_id; ?>");
        </script>
        <?php
        
        // Use the NEW updateUserStatus function instead of updateData
        $result = updateUserStatus('registration', $unique_id, ['status' => $status]);

        // Check if update was successful
        if (isset($result['error'])) {
            // Handle error
            echo "Error updating status: " . $result['error'];
            error_log("Supabase update error: " . $result['error']);
            
            // Fallback: redirect anyway after error
            session_unset();
            session_destroy();           
            // header("location:../../lumers/login.php");
           echo "<script>window.location.href = '/lumers/login.php';</script>";
            exit();
        } else {
            // The update was successful
            echo "Status updated to offline.";
            session_unset();
            session_destroy();           
            // header("location:../../lumers/login.php");
            echo "<script>window.location.href = '/lumers/login.php';</script>";
            exit(); 
        }             
    } else {
        echo "something's wrong";
        header("location: ../users.php");
        exit();
    }
} else {
    echo "fixing something here, be patient";
    header("location: ../login.php");
    exit();
}
?>