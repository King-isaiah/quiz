<?php 
    include "header.php";   
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Exam Inventory</title>
    <link rel="stylesheet" href="css/dataTables.bootstrap5.css" /> 
    <link type="image/x-icon" rel="icon" href="images/bilicon.ico" />
    <link href="js/datatables/datatables.min.css" rel="stylesheet">

    <style type="text/css">
        .container {
            width: 100%;
            max-width: 500px;
            position: relative;
        }
        
        .content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            transition: filter 0.5s ease;
        }
        
        .content.blurred {
            filter: blur(5px);
            pointer-events: none;
            user-select: none;
        }
        
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 1;
            visibility: visible;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        .modal-overlay.hidden {
            opacity: 0;
            visibility: hidden;
        }
        
        .warning-modal {
            background: white;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            transform: scale(1);
            transition: transform 0.5s ease;
            position: relative;
        }
        
        .modal-overlay.hidden .warning-modal {
            transform: scale(0.7);
        }
        
        .warning-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .warning-icon {
            font-size: 50px;
            color: #ff9800;
            margin-bottom: 15px;
        }
        
        .warning-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .warning-content {
            margin-bottom: 25px;
        }
        
        .warning-list {
            list-style-type: none;
            margin: 15px 0;
        }
        
        .warning-list li {
            padding: 8px 0;
            border-bottom: 1px dashed #eee;
            display: flex;
            align-items: center;
        }
        
        .warning-list li i {
            color: #ff9800;
            margin-right: 10px;
            font-size: 18px;
        }
        
        .continue-btn {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px rgba(37, 117, 252, 0.4);
        }
        
        .continue-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(37, 117, 252, 0.6);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            border-color: #6a11cb;
            outline: none;
        }
        
        .form-submit {
            text-align: center;
            margin-top: 30px;
        }
        
        .form-submit button {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px rgba(37, 117, 252, 0.4);
        }
        
        .form-submit button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(37, 117, 252, 0.6);
        }
        
        .price-display {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            color: #6a11cb;
            font-size: 18px;
        }
        .form-container {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: #007bff;
            outline: none;
        }

        .form-submit {
            text-align: center;
        }

        .form-submit button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        .form-submit button:hover {
            background-color: #0056b3;
        }
        .main-amount{
            font-size:20px;
            font-weight: bold;
            color: #27324E;
            letter-spacing: 2px;
            border: 1px solid green;
            background-color: #f9f9f9;
            padding: 10px;
            text-decoration: none;
            display: inline-block;
        }

        tr.pointer { cursor: pointer; }

        tr.pointer:hover { background: #0000ff17; }
        .select2-selection__rendered {
            line-height: 31px !important;
        }
        .select2-container .select2-selection--single {
            height: 35px !important;
        }
        .select2-selection__arrow {
            height: 34px !important;
        }
        .drawer {
            width: 350px;
            height: auto;
            position: absolute;
            z-index: 100;
            padding-top:50px;
            top: 0;
            right: -350px;
            background-color: #d1d6dc;
            transition: right 0.3s ease-in-out;
        }

        .drawer.open {
            right: 0;
        }
        .hidden{
            display:none
        }
        
        /* Add some error styling */
        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            text-align: center;
        }
        
        .no-exams {
            text-align: center;
            padding: 20px;
            font-size: 18px;
            color: #666;
        }
        
        /* Payment form container styling */
        .payment-form-container {
            transition: filter 0.5s ease;
        }
        
        .payment-form-container.blurred {
            filter: blur(5px);
            pointer-events: none;
            user-select: none;
        }
        
        .content-wrapper {
            position: relative;
        }
    </style>
</head>

<body>

<?php
    // Check if user is logged in
    if (!isset($_SESSION['unique_id'])) {
        echo "<div class='error-message'>Please log in to view your exams.</div>";
        exit;
    }
?>

    <main class="main-content">       
        <form action="">
            <div class="exam-from">
                <table id="exam_inventory" class="display table stripe" >
                    <thead>
                        <tr>
                            <th>Paid</th>
                            <th>Year</th>                            
                            <th>Exam Participation</th>
                            <th>Exam Name</th>                                    
                            <th>Exam Time</th>                                    
                            <th>Amount to Pay</th>                                                                                                
                            <th>Date Of Upload</th>
                            <th>Make Payment</th>
                            <th>Write-Exam</th>
                        </tr>
                    </thead>
                    <tbody> 
                        <?php
                            $unique = $_SESSION['unique_id'];                                            
                            $count = 0;
                            
                            // Fetch exam categories from Supabase
                            $examCategories = fetchData('exam_category');
                            
                            // Check for errors in fetching exam categories
                            if (isset($examCategories['error'])) {
                                echo "<tr><td colspan='9' class='error-message'>Error loading exams: " . htmlspecialchars($examCategories['error']) . "</td></tr>";
                            } elseif (empty($examCategories)) {
                                echo "<tr><td colspan='9' class='no-exams'>No exams available at the moment.</td></tr>";
                            } else {
                                foreach ($examCategories as $row) {
                                    $count++;
                                    $examName = $row["category"]; 
                                    
                                    // Check exam results from Supabase
                                    $results = fetchData('exam_results?unique_id=eq.' . $unique . '&exam_type=eq.' . $examName);
                                    $participated = 'No'; 
                                    if (!isset($results['error']) && count($results) > 0) {
                                        $participated = 'Yes';
                                    }

                                    // Check payment status from Supabase
                                    $results1 = fetchData('customer_details?unique_id=eq.' . $unique . '&exam=eq.' . $examName . '&status=eq.success');
                                    $participated1 = 'No';
                                    if (!isset($results1['error']) && count($results1) > 0) {
                                        $participated1 = 'Yes';
                                    }

                                    // Show ALL exams
                        ?>
                        <tr>
                            <td><?php echo $participated1; ?></td>
                            <td><?php echo $row["year"]; ?></td>
                            <td><?php echo $participated; ?></td>
                            <td><?php echo $row["category"]; ?></td>                                    
                            <td><?php echo $row["exam_time_in_minutes"]; ?> minutes</td>
                            <td>N<?php echo $row["price"]; ?></td>
                            <td><?php echo $row["created_at"]; ?></td>
                            <td>
                                <button onclick="formPayment('<?php echo $row['category']; ?>', '<?php echo $row['price']; ?>', '<?php echo $participated1; ?>')">
                                    <i class="fa-solid fa-money-bill-1-wave"></i>
                                </button>
                            </td>
                            <td>
                                <?php
                                if ($participated1 === 'Yes' && $participated === 'Yes') {
                                    echo 'Exam Done'; 
                                } elseif ($participated1 === 'Yes' && $participated === 'No') {            
                                    echo '<button><a class="linkbtn" href="../Exam/select_exam.php?category=' . $row['category'] . '&exam-minutes=' . $row["exam_time_in_minutes"] . '">Start Exam</a></button>';
                                } else {
                                    echo 'Make Payment';
                                } 
                                ?>
                            </td>
                        </tr>
                        <?php
                                }
                            }
                        ?>                                            
                    </tbody>                       
                </table>            
            </div>
        </form>
        
    </main>
    
    <script>
        // Initialize DataTable with pagination and search
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing DataTable with pagination and search...');
            try {
                $('#exam_inventory').DataTable({
                    "pageLength": 5,
                    "responsive": true,
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "lengthMenu": [5,10, 25, 50, 100],
                    "language": {
                        "search": "Search exams:",
                        "lengthMenu": "Show _MENU_ exams per page",
                        "zeroRecords": "No matching exams found",
                        "info": "Showing _START_ to _END_ of _TOTAL_ exams",
                        "infoEmpty": "No exams available",
                        "infoFiltered": "(filtered from _MAX_ total exams)",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        }
                    },
                    "columnDefs": [
                        { "orderable": true, "targets": [0, 1, 2, 3, 4, 5, 6] },
                        { "orderable": false, "targets": [7, 8] } // Payment and Write-Exam columns not sortable
                    ],
                    "order": [[3, 'asc']] // Default sort by Exam Name
                });
                console.log('DataTable initialized successfully with pagination and search');
            } catch (error) {
                console.error('DataTable error:', error);
            }
        });
        
        function setPrice(price) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "php/sert_price.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log('Price set:', xhr.responseText);
                }
            };
            xhr.send("price=" + encodeURIComponent(price));
        }
        
        function setCategory(category) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "php/set_session.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log('Category set:', xhr.responseText);
                }
            };
            xhr.send("category=" + encodeURIComponent(category));
        }
        
        let wrapper = document.getElementsByClassName('main-content')[0];
        
        function formPayment(category, price, participated) {
            console.log('Payment form called:', category, price, participated);
            
            setCategory(category);
            setPrice(price);
            
            let content = '';
            if (participated === 'Yes') {
                content = '<div class="error-message">You have already paid for this exam. Please go back.</div>';
            } else {
                content = ` 
                    <div class="content-wrapper">
                        <div class="modal-overlay" id="modal-overlay">
                            <div class="warning-modal">
                                <div class="warning-header">
                                    <div class="warning-icon">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                    <h2 class="warning-title">Important Payment Instructions</h2>
                                    <p>Please read the following instructions carefully to ensure a successful transaction</p>
                                </div>
                                    
                                <div class="warning-content">
                                    <ul class="warning-list">
                                        <li><i class="fas fa-check-circle"></i> Ensure your card is authorized for online transactions</li>
                                        <li><i class="fas fa-check-circle"></i> Check that you have sufficient funds in your account</li>
                                        <li><i class="fas fa-check-circle"></i> Do not refresh the page during the payment process</li>
                                        <li><i class="fas fa-check-circle"></i> Do not leave the payment page until you are directed or shown succesfully paid</li>
                                        <li><i class="fas fa-check-circle"></i> Please use the email you use to login in for payment so it can be tracked incase of payment problems</li>
                                        <li><i class="fas fa-check-circle"></i> Pls take the above instruction very carefully to prevent stories that touch</li>
                                        <li><i class="fas fa-check-circle"></i> Keep your card details and OTP (if required) ready</li>
                                        <li><i class="fas fa-check-circle"></i> You will receive a confirmation email after successful payment</li>
                                    </ul>
                                    
                                    <p style="color: #ff9800; font-weight: 600; text-align: center;">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        For security reasons, this page will timeout after 15 minutes of inactivity
                                    </p>
                                </div>
                                    
                                <button class="continue-btn" id="continue-btn">
                                    I Understand - Continue to Payment
                                </button>
                            </div>
                        </div>

                        <div class="payment-form-container blurred" id="payment-form-container">
                            <form action="" id="paymentForm" class="form-container">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email-address" required />
                                </div>
                                <div class="form-group">
                                    <label for="first-name">First Name</label>
                                    <input type="text" id="first-name" required />
                                </div>
                                <div class="form-group">
                                    <label for="last-name">Last Name</label>
                                    <input type="text" id="last-name" required />
                                </div>
                                <div class="form-group" style="display:none">
                                    <label for="price">Amount</label>
                                    <input type="text" id='amount' value='${price}' required/>
                                </div>
                                <div class="form-submit">
                                    <button type="submit">Pay N${price}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;
            }
    
            wrapper.innerHTML = content;
            
            // Set up event listeners
            const continueBtn = document.getElementById('continue-btn');
            const paymentFormContainer = document.getElementById('payment-form-container');
            const modalOverlay = document.getElementById('modal-overlay');
            
            if (continueBtn && paymentFormContainer && modalOverlay) {
                continueBtn.addEventListener('click', function() {
                    modalOverlay.style.display = 'none';
                    paymentFormContainer.classList.remove('blurred');
                });
            }
            
            const paymentForm = document.getElementById('paymentForm');
            if (paymentForm) {
                paymentForm.addEventListener("submit", payWithPaystack, false);
            }
        }

        function payWithPaystack(e) {
            e.preventDefault();
            console.log('Starting Paystack payment...');
            
            // Check if Paystack is loaded
            if (typeof PaystackPop === 'undefined') {
                alert('Payment system is loading. Please wait a moment and try again.');
                return;
            }
            
            let handler = PaystackPop.setup({
                key: 'pk_test_3b8594df674ac3641ee10e25e9a3ceed43227c89',
                email: document.getElementById("email-address").value,
                amount: document.getElementById("amount").value * 100,
                firstname: document.getElementById("first-name").value,
                lastname: document.getElementById("last-name").value,
                ref: 'ESHIOZE' + Math.floor((Math.random() * 100000000) + 1),
                
                onClose: function(){
                    alert('Transaction Canceled.');
                    window.location = "http://localhost/quiz/students/Exam-Inventry.php?transction=cancel";
                },
                callback: function(response){
                    let message = 'Payment complete! Reference: ' + response.reference;
                    alert(message);
                    window.location = "http://localhost/quiz/students/php/verify_transaction.php?reference=" + response.reference;
                }
            });
            handler.openIframe();
        }
    </script>
</body>
</html>

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>

<?php include "footer.php"?>