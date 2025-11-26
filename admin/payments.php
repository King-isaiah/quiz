<?php 
    session_start();
    include "header.php";
    // include "../connection.php";
    include "../superbase/config.php"; 
?>
<style>
    .circling-button {
        align-items: center; 
        cursor: pointer;
        margin-top: .5rem;
        width: 30px;  
        height: 30px; 
        background-color: #007bff; 
        border-radius: 50%; 
        display: flex;
        justify-content: center;
        align-items: center;
        text-decoration: none; 
        color: white; 
        font-size: 24px;
        transition: background-color 0.3s;
    }

    .circling-button:hover {
        background-color:rgb(0, 179, 57); 
    }
 
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); 
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000; 
    }

    .module {
        background-color: #f5f3f3ff; 
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        width: 600px; 
        color: rgba(0, 0, 0, 0.5);
    }

    .form-group {
        margin-bottom: 15px; 
    }

    label {
        display: block;
        margin-bottom: 5px; 
    }

    .form-group input[type="text"],
    .form-group select {
        width: 100%; 
        padding: 10px;
        border: 1px solid #ccc; 
        border-radius: 4px; 
    }
    .space-center{
        margin-left: 15em;
    }
    button {
        padding: 10px 15px;
        background-color: #007bff; 
        color: white; 
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #0056b3; 
    } 
    .top{
        padding-left: 34em;
        cursor: pointer;
    }
   @media (max-width: 470px) {
        .top {
            padding-left: 22rem; 
        }
        .circling-button { 
            margin-top: 0.5rem;
            width: 30px;  
            height: 30px; 
            font-size: 16px; 
            transition: background-color 0.3s; 
        }
    }
   @media (max-width: 670px) {
        .top {
            padding-left: 24rem; 
        }
        
    }
   @media (max-width: 770px) {
        .top {
            padding-left: 26rem; 
        }
        .circling-button { 
            margin-top: 0.5rem;          
            width: 30px;  
            height: 30px; 
            font-size: 16px; 
            transition: background-color 0.3s; 
        }
    }
</style>
<div id="content">
    <div class="breadcrumbs">
        <div class="col-sm-12">
            <div class="page-header" style="display: flex; justify-content:space-between; width: 100%">
                
                <div class="page-title">
                    <h1>Users Paid</h1>                   
                </div>
               
               <a href="path/path.php" class="circling-button" >
					<i class="fa fa-plus"></i>
				</a>
            
            </div>
        </div>
    </div>

    <div class="content mt-3">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <center>
                                <h1>Payment Records</h1>
                                <?php
                                    // Display current database mode
                                    $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';
                                    $dbMode = $useLocal ? 'Local MySQL' : 'Supabase';
                                    echo "<p style='color: #666; font-size: 12px; margin-bottom: 15px;'>Current Database: <strong>$dbMode</strong></p>";
                                ?>
                                <form action="" method="post">
                                    <div style="display: flex; align-items: center; gap: 10px;">                                    
                                        <input type="date" id="dateSearch" name="dateSearch" 
                                        placeholder="Select Date" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px; width: 200px;">
                                    
                                        <button type="button" id="searchButton" onclick="searchByDate()" 
                                        style="background-color: #006df0; color: white; border: 1px solid #0056b3; padding: 10px 15px; border-radius: 4px; 
                                        cursor: pointer;">
                                            Search
                                        </button>

                                        <?php 
                                            function generateCourseSelect($link) {
                                                // Check database preference
                                                $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';
                                                
                                                if ($useLocal) {
                                                    // Use Local MySQL
                                                    $res = mysqli_query($link, "SELECT * FROM exam_category");

                                                    if (!$res) {
                                                        die("Database query failed: " . mysqli_error($link));
                                                    }
                                                
                                                    $selectHTML = '<select name="examSubject" id="category" style="padding: 10px; border-radius: 4px;">';
                                                    $selectHTML .= "<option value=''>Select Exam</option>";
                                                
                                                    while ($row = mysqli_fetch_assoc($res)) {
                                                        $name = $row['category'];                                        
                                                        $selectHTML .= "<option value=\"$name\">$name</option>";                                       
                                                    }

                                                    mysqli_free_result($res);
                                                } else {
                                                    // Use Supabase
                                                    $response = fetchData('exam_category');
                                                    
                                                    $selectHTML = '<select name="examSubject" id="category" style="padding: 10px; border-radius: 4px;">';
                                                    $selectHTML .= "<option value=''>Select Exam</option>";
                                                    
                                                    if (is_array($response) && count($response) > 0 && !isset($response['error'])) {
                                                        foreach ($response as $row) {
                                                            $name = $row['category'];                                        
                                                            $selectHTML .= "<option value=\"$name\">$name</option>";                                       
                                                        }
                                                    } else {
                                                        $selectHTML .= "<option value=''>No exams available</option>";
                                                    }
                                                }

                                                $selectHTML .= '</select>';
                                                return $selectHTML;
                                            }

                                            echo generateCourseSelect($link); 
                                        ?>
                                    </div>
                                </form>
                            </center>

                            <table id="examTable" class="table table-bordered display" style="width:100%">
                                <thead>
                                    <tr style="background-color: #006df0; color: white;">
                                        <th>User</th>
                                        <th>Users Id</th>
                                        <th>Exam</th>
                                        <th>Year of Exam</th>
                                        <th>Day of Payment</th>
                                        <th>Status</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be filled by DataTables -->
                                </tbody>
                            </table>

                        </div>
                    </div> <!-- .card -->
                </div>
                <!--/.col-->
            </div><!-- .animated -->
        </div><!-- .content -->
    </div>
</div>

   
<script src="js/jquery-3.6.0.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script>
   
function searchByDate() {
    const date = document.getElementById('dateSearch').value;
    const exam = document.getElementById('category').value;

    if (!exam) {
        alert("Please choose an exam.");
        return;
    }

    console.log("Searching for exam:", exam);

    fetch('functions/fetch_payment_records.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json' 
        },
        body: JSON.stringify({ exam: exam }) 
    })
    .then(response => {
        console.log("Response status:", response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json(); 
    })
    .then(data => {
        console.log("Data received:", data);
        
        // Check for error in response
        if (data.error) {
            alert('Error: ' + data.error);
            return;
        }
        
        // Get DataTable instance
        const table = $('#examTable').DataTable();
        
        // Clear existing data
        table.clear();
        
        // Add new rows
        if (data && data.length > 0) {
            data.forEach(record => {
                table.row.add([
                    record.username || 'N/A',
                    record.unique_id || 'N/A',
                    record.exam || 'N/A',
                    record.year || 'N/A',
                    record.day_of_payment || 'N/A',
                    record.status || 'N/A',
                    record.email || 'N/A'
                ]);
            });
        } else {
            table.row.add(['No data found', '', '', '', '', '', '']);
        }
        
        // Redraw table
        table.draw();
    })
    .catch(error => {
        console.error("There was a problem with the fetch operation:", error);
        alert('Error fetching data: ' + error.message);
    });
}

    let originalContent = '';
    
    function add() {
        originalContent = document.getElementById('content').innerHTML;  
        let content = `
            <div class="overlay">
                <div class="module">
                    <div class='top' onclick="remove()">
                        <i class="fa fa-times"></i>
                    </div>
                    <form id="userForm" onsubmit="submitForm(event)">
                        <div class="form-group">
                            <label for="users">Students/Users:</label>
                            <select id="users" name="username"></select>
                        </div>
                        <div class="form-group">
                            <label for="category">Category/Exam:</label>
                            <select id="category" name="category"></select>
                        </div>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select id="status" name="status">
                                <option value="success">Success</option>
                                <option value="null">Null</option>
                            </select>
                        </div>
                        <button type="submit" class='space-center'>Submit</button>
                    </form>
                </div>
            </div>
        `;

        document.getElementById('content').innerHTML = content; 
        fetchData();
    }

    function submitForm(event) {
        event.preventDefault(); 

        const formData = new FormData(document.getElementById('userForm'));

        fetch('functions/paymentUpUpdate.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.succ) {
                alert(data.succ);
            } else if (data.error) {
                alert(data.error);
            } else if (data.warning) {
                alert(data.warning);
            }
            document.getElementById('content').innerHTML = originalContent; 
        })
        .catch(error => console.error('Error:', error));
    }

    function fetchData() {
        // Fetch users based on database preference
        const useLocal = getCookie('useLocalDB') === 'true';
        
        if (useLocal) {
            // Use existing route system for local
            let options = {
                url: '../app/route.php',
                methodType: 'GET', 
                data: {
                    request: JSON.stringify({
                        handler: 'functions//PaymentUp@retrieveUsers',
                    })
                }
            };
            
            resolver(xhr(options), rev => {
                const usersSelect = document.getElementById('users');
                usersSelect.innerHTML = ''; 
                
                rev.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.username; 
                    option.textContent = user.username;
                    usersSelect.appendChild(option);
                });
            });
        } else {
            // Use Supabase for users
            fetch('functions/fetch_users.php')
            .then(response => response.json())
            .then(users => {
                const usersSelect = document.getElementById('users');
                usersSelect.innerHTML = '';
                
                users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.username; 
                    option.textContent = user.username;
                    usersSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching users:', error));
        }

        // Fetch categories based on database preference
        if (useLocal) {
            // Local categories
            fetch('/get_categories') 
                .then(response => response.json())
                .then(data => {
                    const categorySelect = document.getElementById('category');
                    categorySelect.innerHTML = '';
                    data.categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id; 
                        option.textContent = category.category; 
                        categorySelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching categories:', error));
        } else {
            // Supabase categories
            fetch('functions/fetch_categories.php')
            .then(response => response.json())
            .then(data => {
                const categorySelect = document.getElementById('category');
                categorySelect.innerHTML = '';
                data.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id; 
                    option.textContent = category.category; 
                    categorySelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching categories:', error));
        }
    }

    function remove(){
        document.getElementById('content').innerHTML = originalContent;
    }

    // Utility function to get cookie
    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
</script>
<script src="js/main.js"></script>
<?php include "footer.php"; ?>