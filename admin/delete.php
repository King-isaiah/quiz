<?php 
    // include "../connection.php";       

    // $id = $_GET["id"];
    // $category = $_GET["category"];

    // // Sanitize input
    // $category = mysqli_real_escape_string($link, $category);

    // // Prepare the query
    // $query = "DELETE FROM questions WHERE category='$category'";

    // if (mysqli_query($link, $query)) {    
    //     mysqli_query($link, "delete from exam_category where id=$id");
        
    //     echo "Rows deleted successfully.";
    //     ?>
         <script type="text/javascript">
    //         window.location="edit_exam_category.php"
    //     </script>
         <?php
    // } else {
    //     // Deletion failed
    //     echo "<script>alert('Something went wrong: " . mysqli_error($link) . "');</script>";
    // }
?>

<?php
    include "../superbase/config.php";
    $id = $_GET["id"];
    $category = $_GET["category"];

    $questionsResult = deleteDataImproved('questions', 'category', $category);

    if (!isset($questionsResult['error'])) {    
        $examCategoryResult = deleteData('exam_category', $id);
        
        if (!isset($examCategoryResult['error'])) {
            echo "Rows deleted successfully.";
            ?>
            <script type="text/javascript">
                window.location="edit_exam_category.php"
            </script>
            <?php
        } else {
            echo "<script>alert('Something went wrong deleting exam category: " . $examCategoryResult['error'] . "');</script>";
        }
    } else {
        echo "<script>alert('Something went wrong deleting questions: " . $questionsResult['error'] . "');</script>";
    }
?>
