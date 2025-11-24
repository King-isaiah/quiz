<?php session_start()?>
<?php include "header.php"?>


<div class="dashboard-front-view">
    <div class="dashboardcontain" id= 'wrapper'></div>
    <h3 class="admin-name"> 👋Welcome <?php echo $row['username']?>!</h3>
    
    <div class="info">
    
        <div class="text">
           <div style="margin-left:3em; margin-right:5em;"> 
                <a><img class="avatar rounded-circle" src="../lumers/php/images/<?php echo $row['img'] ?>" alt="" /></a>
            </div>
           <div class='textdiv'>
                <h3 class='margin'><?php echo $row['lastname'] .  " " . $row['firstname'] ?></h3>
                <button class='btn1' style="color: white;">
                    <a href="Account-Settings.php" style="cursor: pointer;">Edit Profile</a>
                </button>
            </div>
            
        </div>
    </div>
    <div class="dashboardThird">
        <div class="top">
            <h1>Over 3 million people read books daily</h1>
            <h6>Lures is a platform that rewards you for doing so</h6>
        </div>
        <div class="bottom">
            <div class="icon">
                <i  class="fa-solid fa-user-check"></i>
                <h5>1.Register</h5>
                
            </div>
            <div class="icon">
                <i  class="fa-solid fa-book-open"></i>
                <h5>2.Read a book</h5>
            </div>
            <div class="icon">
                <i class="fa-solid fa-hand-holding-dollar"></i>
                <h5>3.Write an Exam</h5>
            </div>
            <div class="icon">
                <i class="fa-solid fa-pen-to-square"></i>
                <h5>4.Win cash prize</h5>
            </div>
        </div>
    </div>
</div>    


            
             
  

<?php include "footer.php"?>