<?php
    session_start();
    include "header.php";
?>
<style>
    .unknown-div {
        position: relative;
        display: inline-block;
    }

    .profile {
        transition: opacity 0.3s ease;
    }

    .profile-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
        transition: opacity 0.3s ease;
        font-size: 2rem; /* Adjust size as needed */
        color: white; /* Change color if needed */
        background-color: rgba(0, 0, 0, 0.5); /* Optional background */
        border-radius: 50%; /* Makes it circular */
        padding: 10px; /* Adjust padding */
    }

    .unknown-div:hover .profile {
        opacity: 0.5; /* Fade effect on image */
    }

    .unknown-div:hover .profile-icon {
        opacity: 1; /* Show icon on hover */
    }
</style>
<div class="settings-front-view form signup">
    <form action="#" enctype="multipart/form-data" autocomplete="off">
        <div class="error-txt"></div>
        
        <div class="unknown-div">
            <img class="profile rounded-circle" src="../lumers/php/images/<?php echo $row['img'] ?>" alt="Profile Image">
            <div class="profile-icon rounded-circle">
                <i class="fa-solid fa-camera"></i>
            </div>
            <input type="file" name="image" id="imageInput" required style="display: none;" />
        </div>
        <div>
            <div style="width:70%; display:flex; flex-direction:column; padding:15px; align-items:center">
                <div style="width:100%; display:flex; flex-direction:row; align-items:flex-start; justify-content:space-between">
                    <div>
                        <label class=" form-control-label">Username</label>
                        <div class='input-div'>               
                            <input type="text" style="margin-left:5px; border-radius:12px;" name="username" placeholder=""value='<?php echo $row['username']?>' class="form-control" >
                        </div>
                    </div>
                    
                    <div>
                        <label>Last name</label>
                        <div class='input-div'>                        
                            <input type="text" style="margin-left:5px; border-radius:12px;" name="lname" placeholder=""value='<?php echo $row['lastname']?>' class="form-control" >
                        </div>
                    </div>
                    
                
                </div>
            </div>

            <div style="width:70%; display:flex; flex-direction:column; padding:15px; align-items:center">
                <div style="width:100%; display:flex; flex-direction:row; align-items:flex-start; justify-content:space-between">
                    <div>
                        <label >Phone Number</label>
                        <div class='input-div'>               
                            <input type="text" style="margin-left:5px; border-radius:12px;" name="phone_number" placeholder=""value='<?php echo $row['contact']?>' class="form-control" >
                        </div>
                    </div>
                    
                    <div style="display: none;">
                        <label>Email</label>
                        <div class='input-div'>                      
                            <input type="text" style="margin-left:5px; border-radius:12px;" name="email" placeholder=""value='<?php echo $row['email']?>' class="form-control" >
                        </div>
                    </div>
                    
                
                </div>
            </div>
            <div style="width:70%; display:flex; flex-direction:column; padding:15px; align-items:center">
                <label for="gender">Gender</label>
                <select class="form-control allcompanys" name="gender" id="gender" style="width:55%;">
                    <option value="<?php echo $row['gender']?>"><?php echo $row['gender']?></option> <!-- Prompt option -->
                    <option value="Male">Male</option>
                    <option value="Female">Female</option> 
                </select>
            </div>


            <div id='btns'class="field button">
            <input type="submit" value="Update Profile"/> 
          </div>

            
            
            
        </div>
        
    </form>
</div>
<script src="js/profileUpdate.js"></script>
<script>
    document.querySelector('.profile').addEventListener('click', function() {
        document.getElementById('imageInput').click();
    });
</script>
<?php include "footer.php"?>