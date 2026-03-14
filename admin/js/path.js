document.addEventListener('DOMContentLoaded', function() {
    const usernameSelect = document.getElementById('users');
   
    
    // Add event listener for changes
    usernameSelect.addEventListener('change', function() {
        const selectedUsername = this.value;
        const usernameSelected = document.getElementById('users').value;
        // alert(usernameSelected)
        console.log(usernameSelected)
        if (usernameSelected) {
            // Fetch user data from server
            fetchUserData(usernameSelected);
        } else {
            // Clear fields if no user is selected
            document.getElementById('unique_id').value = '';
            document.getElementById('student_email').value = '';
        }
    });
            
        // Function to fetch user data
        function fetchUserData(username) {
            fetch(`get_user_data.php?username=${encodeURIComponent(username)}`)
                .then(response => {
                    // First check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return response.text().then(text => {
                            throw new Error(`Expected JSON but got: ${text.substring(0, 100)}...`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('unique_id').value = data.unique_id || '';
                        document.getElementById('student_email').value = data.email || '';
                    } else {
                        throw new Error(data.message || 'Unknown server error');
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    alert("Error loading user data: " + error.message);
                    document.getElementById('unique_id').value = '';
                    document.getElementById('student_email').value = '';
                });
        }
    });



function submitForm(event) {
    event.preventDefault();     
    const formData = new FormData(document.getElementById('userForm'));
        
    
    fetch('../functions/paymentUpUpdate.php', {            
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {        
        // Handle the response data
        if (data.succ) {             
            // alert(data.succ);
            Notice('Operation completed successfully!', '#28a745');
        } else if (data.error) {
            // alert(data.error);
            Notice('An error occurred!', '#dc3545', 5000);
        } else if (data.warning) {  
            Notice(data.warning, '#ffc107');
        }
        // To reset the form or update the UI to look like the beginig
        // document.getElementById('content').innerHTML = originalContent; 
        
    })
    .catch(error => console.error('Error:', error));
}

    
    // Improved Notice function with queue system
    let notificationQueue = [];
    let isShowingNotification = false;

    function Notice(message, color, duration = 3000) {
        notificationQueue.push({message, color, duration});
        showNextNotice();
    }

    function showNextNotice() {
        if (!isShowingNotification && notificationQueue.length > 0) {
            isShowingNotification = true;
            const notice = notificationQueue.shift();
            showSingleNotice(notice.message, notice.color, notice.duration);
        }
    }

    function showSingleNotice(message, color, duration) {
        const notice = $('<div class="custom-notice"></div>')
            .text(message)
            .css({
                'background-color': color,
                'color': '#fff',
                'padding': '12px 20px',
                'margin': '10px 0',
                'border-radius': '4px',
                'position': 'fixed',
                'top': '20px',
                'right': '20px',
                'z-index': '9999',
                'box-shadow': '0 4px 8px rgba(0,0,0,0.1)',
                'opacity': '0',
                'transform': 'translateX(100%)',
                'transition': 'all 0.3s ease'
            });
        
        $('body').append(notice);
        
        // Trigger the animation
        setTimeout(() => {
            notice.css({
                'opacity': '1',
                'transform': 'translateX(0)'
            });
        }, 10);
        
        // Auto-dismiss
        setTimeout(() => {
            notice.css({
                'opacity': '0',
                'transform': 'translateX(100%)'
            });
            setTimeout(() => {
                notice.remove();
                isShowingNotification = false;
                showNextNotice();
            }, 300);
        }, duration);
        
        // Click to dismiss
        notice.on('click', function() {
            $(this).css({
                'opacity': '0',
                'transform': 'translateX(100%)'
            });
            setTimeout(() => {
                $(this).remove();
                isShowingNotification = false;
                showNextNotice();
            }, 300);
        });
    }


    function showSuccess(message) {
        Notice(message, '#28a745');
    }

    function showError(message) {
        Notice(message, '#dc3545', 5000);
    }

    function showWarning(message) {
        Notice(message, '#ffc107');
    }

    function showInfo(message) {
        Notice(message, '#17a2b8');
    }
    function remove() {             
        location.href = '../payments.php';
    }