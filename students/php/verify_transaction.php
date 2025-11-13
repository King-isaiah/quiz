<?php
  session_start();
  include "../../superbase/config.php";
  
  // Check if reference is provided
  if(!isset($_GET['reference']) || $_GET['reference'] === ''){
      header("Location: javascript://history.go(-1)");
      exit;
  }
  
  $ref = $_GET['reference'];
  $unique = $_SESSION['unique_id'] ?? null;
  $exam = $_SESSION['category'] ?? null;

  // Check if session data exists
  if (!$unique || !$exam) {
      echo "Error: Session data missing. Please try the payment again.";
      exit;
  }
?>

<?php
  // Verify payment with Paystack
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/".rawurlencode($ref),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer sk_test_fed7c9b7acc9d32e5ad35af2e5e11fc2965436a1",
      "Cache-Control: no-cache",
    ),
  ));
  
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);
  
  if ($err) {
    // echo "cURL Error #:" . $err;
    exit;
  }
  
  $result = json_decode($response);
  
  // Check if payment was successful
  if($result->data->status === "success"){
    // Payment successful - store in Supabase
    $status = $result->data->status;
    $reference = $result->data->reference;
    $lname = $result->data->customer->last_name;
    $fname = $result->data->customer->first_name;
    $fulllname = $lname.' '.$fname;
    $Cus_email = $result->data->customer->email;
    date_default_timezone_set('Africa/Lagos');
    $Date_time = date('m/d/Y h:i:s a', time());

    // Include config to use Supabase functions
    // include_once "config.php";
    
    // Prepare data for Supabase
    $paymentData = [
        'unique_id' => (int)$unique,
        'status' => $status,
        'exam' => $exam,
        'reference' => $reference,
        'fullname' => $fulllname,
        'date_purchased' => $Date_time,
        'email' => $Cus_email
    ];
    
    // Insert into Supabase using your createData function
    $insertResult = createData('customer_details', $paymentData);
    
    if (isset($insertResult['error'])) {
        // Handle Supabase insertion error
        // echo "Error saving payment details: " . $insertResult['error'];
        // You might want to log this error for debugging
        // error_log("Supabase insertion error: " . $insertResult['error']);
    } else {
        // Success - redirect to success page
        header("Location: success.php?status=success");
        exit;
    }
    
  } else {
    // Payment failed
    header("Location: error.html");
    exit;
  }
?>