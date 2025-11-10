<?php include "header.php"?>

<div class="dashboard-front-view">
    <div class="book-header">
        <img src="../img/replace.jpg" alt="">
        
    </div>
    <div class="book-content">
        <h5 style="margin-bottom: 2rem;"><i class="fa-regular fa-file"></i>Book  Chapters Summary  <button id="downloadBtn">Download PDF <i class="fa-solid fa-download"></i></button></h5>
        <div class="summary-container">
            <div class="summary" style='background-color:purple;'>
                <h4>Chapter 1</h4>
                <h6>Lorem ipsum dolor sit amet, consectetur adipiscing elit.  </h6>
                <h6> In id neque quis tortor interdum aliquam nec ut nisi.</h6>
                <h6>Aliquam rhoncus sed est ut varius.</h6>
                <h6>Maecenas mollis enim a felis fringilla sagittis.</h6>
            </div>
            <div style='background-color:orange; margin-left:1rem;' class="summary">
                <h4>Chapter 2</h4>
                <h6>Lorem ipsum dolor sit amet, consectetur adipiscing elit.  </h6>
                <h6> In id neque quis tortor interdum aliquam nec ut nisi.</h6>
                <h6>Aliquam rhoncus sed est ut varius.</h6>
                <h6>Maecenas mollis enim a felis fringilla sagittis.</h6>
            </div>
            <div class="summary" style='background-color:aqua; margin-left:1rem;'>
                <h4>Chapter 3</h4>
                <h6>Lorem ipsum dolor sit amet, consectetur adipiscing elit.  </h6>
                <h6> In id neque quis tortor interdum aliquam nec ut nisi.</h6>
                <h6>Aliquam rhoncus sed est ut varius.</h6>
                <h6>Maecenas mollis enim a felis fringilla sagittis.</h6>
            </div>
            <div class="summary" style='background-color:blueviolet;  margin-left:1rem;'>
                <b><h4>Chapter 4</h4></b>
                <h6>Lorem ipsum dolor sit amet, consectetur adipiscing elit.  </h6>
                <h6> In id neque quis tortor interdum aliquam nec ut nisi.</h6>
                <h6>Aliquam rhoncus sed est ut varius.</h6>
                <h6>Maecenas mollis enim a felis fringilla sagittis.</h6>
            </div>
        </div>
    </div>
</div>
<?php include "footer.php"?>

<script>
        document.getElementById('downloadBtn').addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.text("Hello, this is your PDF!", 10, 10);
            doc.save("sample.pdf"); // Filename for downloaded PDF
        });
    </script>