</div><!-- /#right-panel -->
                                <!-- Right Panel -->

    <script>
// Hamburger Menu Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const mainMenu = document.getElementById('main-menu');
    
    if (navbarToggler && mainMenu) {
        navbarToggler.addEventListener('click', function() {
            mainMenu.classList.toggle('show');
            
            // Toggle between bars and times icon
            const icon = this.querySelector('i');
            if (mainMenu.classList.contains('show')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Close menu when clicking on a link (optional)
        const menuLinks = mainMenu.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                mainMenu.classList.remove('show');
                navbarToggler.querySelector('i').classList.remove('fa-times');
                navbarToggler.querySelector('i').classList.add('fa-bars');
            });
        });
        
        // Close menu when clicking outside (optional)
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.navbar-header') && !event.target.closest('#main-menu')) {
                mainMenu.classList.remove('show');
                navbarToggler.querySelector('i').classList.remove('fa-times');
                navbarToggler.querySelector('i').classList.add('fa-bars');
            }
        });
    }
    
    // Your existing code for active menu items
    const currentPage = window.location.pathname.split("/").pop();
    const menuItems = document.querySelectorAll("#main-menu .nav li a");
    
    menuItems.forEach(item => {
        const href = item.getAttribute("href");
        if (href === currentPage) {
            item.classList.add("actives");
        }
    });
});
</script>
   
</body>
</html>
