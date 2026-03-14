// const spinnerOverlay = document.getElementById("spinnerOverlay");
// const toastContainer = document.getElementById("toastContainer");


// // Function to show spinner
// function showSpinner() {
//     spinnerOverlay.style.display = 'flex';
// }

// // Function to hide spinner
// function hideSpinner() {
//     spinnerOverlay.style.display = 'none';
// }

// // Function to show toast notification
// function showToast(message, type = 'success') {
//     const toast = document.createElement('div');
//     toast.className = `toast ${type}`;
    
//     const icons = {
//         success: '✓',
//         error: '✕',
//         warning: '⚠'
//     };
    
//     toast.innerHTML = `
//         <div style="display: flex; align-items: center;">
//             <span class="toast-icon">${icons[type]}</span>
//             <span class="toast-message">${message}</span>
//         </div>
//         <button class="toast-close">&times;</button>
//     `;
    
//     // Add close functionality
//     const closeBtn = toast.querySelector('.toast-close');
//     closeBtn.onclick = () => {
//         toast.remove();
//     };
    
//     toastContainer.appendChild(toast);
    
//     // Show toast with animation
//     setTimeout(() => {
//         toast.classList.add('show');
//     }, 100);
    
//     // Auto remove after 5 seconds
//     setTimeout(() => {
//         if (toast.parentNode) {
//             toast.classList.remove('show');
//             setTimeout(() => {
//                 if (toast.parentNode) {
//                     toast.remove();
//                 }
//             }, 300);
//         }
//     }, 5000);
// }

// Function to show spinner
function showSpinner() {
    const spinnerOverlay = document.getElementById('spinnerOverlay');
    if (spinnerOverlay) {
        spinnerOverlay.style.display = 'flex';
    } else {
        console.warn('Spinner overlay element not found');
    }
}

// Function to hide spinner
function hideSpinner() {
    const spinnerOverlay = document.getElementById('spinnerOverlay');
    if (spinnerOverlay) {
        spinnerOverlay.style.display = 'none';
    } else {
        console.warn('Spinner overlay element not found');
    }
}

// Function to show toast notification
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    
    // Create toast container if it doesn't exist
    if (!toastContainer) {
        createToastContainer();
    }
    
    const finalToastContainer = document.getElementById('toastContainer');
    if (!finalToastContainer) {
        console.error('Toast container could not be created');
        alert(message); // Fallback
        return;
    }
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const icons = {
        success: '✓',
        error: '✕',
        warning: '⚠'
    };
    
    toast.innerHTML = `
        <div style="display: flex; align-items: center;">
            <span class="toast-icon">${icons[type]}</span>
            <span class="toast-message">${message}</span>
        </div>
        <button class="toast-close">&times;</button>
    `;
    
    // Add close functionality
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.onclick = () => {
        toast.remove();
    };
    
    finalToastContainer.appendChild(toast);
    
    // Show toast with animation
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.classList.remove('show');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Function to create toast container if it doesn't exist
function createToastContainer() {
    if (!document.getElementById('toastContainer')) {
        const toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container';
        toastContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
        `;
        document.body.appendChild(toastContainer);
    }
}

// Function to create spinner overlay if it doesn't exist
function createSpinnerOverlay() {
    if (!document.getElementById('spinnerOverlay')) {
        const spinnerOverlay = document.createElement('div');
        spinnerOverlay.id = 'spinnerOverlay';
        spinnerOverlay.className = 'spinner-overlay';
        spinnerOverlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        `;
        
        const spinner = document.createElement('div');
        spinner.className = 'spinner';
        spinner.style.cssText = `
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        `;
        
        // Add spin animation if not exists
        if (!document.querySelector('style#spinner-animation')) {
            const style = document.createElement('style');
            style.id = 'spinner-animation';
            style.textContent = `
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);
        }
        
        spinnerOverlay.appendChild(spinner);
        document.body.appendChild(spinnerOverlay);
    }
}

// Initialize elements when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    createToastContainer();
    createSpinnerOverlay();
});

// Also initialize if script is loaded after DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        createToastContainer();
        createSpinnerOverlay();
    });
} else {
    createToastContainer();
    createSpinnerOverlay();
}

// header.js - Enhanced with reusable components

// ===== DATABASE TOGGLE COMPONENT =====
function initDatabaseToggle() {
    const toggleContainer = document.getElementById('databaseToggleContainer');
    if (!toggleContainer) return;
    
    const useLocal = getCookie('useLocalDB') === 'true';
    const toggleHtml = `
        <div class="form-group mb-3">
            <label class="db-toggle-switch">
                <input type="checkbox" id="databaseToggle" ${useLocal ? 'checked' : ''}>
                <span class="db-toggle-slider db-toggle-round"></span>
            </label>
            <span id="databaseStatus" class="ml-2 ${useLocal ? 'text-success' : 'text-primary'}">
                ${useLocal ? 'Using Local MySQL' : 'Using Supabase'}
            </span>
        </div>
    `;
    toggleContainer.innerHTML = toggleHtml;
    
    document.getElementById('databaseToggle').addEventListener('change', toggleDatabaseConnection);
}

function toggleDatabaseConnection() {
    const useLocal = document.getElementById('databaseToggle').checked;
    setCookie('useLocalDB', useLocal, 30);
    window.location.reload();
}

// ===== SEARCH AND PAGINATION COMPONENT =====
function initTableSearchPagination(config) {
    const {
        tableId,
        searchId,
        paginationId,
        recordsPerPage = 7,
        searchColumns = [] // Array of column indices to search in
    } = config;
    
    let currentData = [];
    let filteredData = [];
    let currentPage = 1;
    
    // Initialize if elements exist
    const searchInput = document.getElementById(searchId);
    const table = document.getElementById(tableId);
    const pagination = document.getElementById(paginationId);
    
    if (!searchInput || !table || !pagination) return;
    
    // Extract data from table
    function extractTableData() {
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        return rows.map(row => {
            const cells = Array.from(row.querySelectorAll('td, th'));
            return {
                element: row,
                searchText: cells.map(cell => cell.textContent.toLowerCase()).join(' '),
                visible: true
            };
        });
    }
    
    // Apply search filter
    function applySearch(filter) {
        currentData.forEach(item => {
            const matches = item.searchText.includes(filter.toLowerCase());
            item.element.style.display = matches ? '' : 'none';
            item.visible = matches;
        });
        filteredData = currentData.filter(item => item.visible);
        currentPage = 1;
        renderPagination();
    }
    
    // Render pagination
    function renderPagination() {
        const totalPages = Math.ceil(filteredData.length / recordsPerPage);
        
        if (totalPages <= 1) {
            pagination.innerHTML = '';
            showAllRows();
            return;
        }
        
        let paginationHtml = `
            <nav aria-label="Page navigation">
                <ul class="custom-pagination justify-content-center">
                    <li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage - 1}">&laquo;</a>
                    </li>
        `;
        
        for (let page = 1; page <= totalPages; page++) {
            paginationHtml += `
                <li class="page-item ${page === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${page}">${page}</a>
                </li>
            `;
        }
        
        paginationHtml += `
                    <li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage + 1}">&raquo;</a>
                    </li>
                </ul>
            </nav>
            <div class="text-center text-muted mt-2">
                Page ${currentPage} of ${totalPages} (Showing ${filteredData.length} records)
            </div>
        `;
        
        pagination.innerHTML = paginationHtml;
        
        // Add event listeners to pagination links
        pagination.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                if (page && page !== currentPage) {
                    currentPage = page;
                    renderPage();
                    renderPagination();
                }
            });
        });
        
        renderPage();
    }
    
    // Show all rows (when no pagination needed)
    function showAllRows() {
        filteredData.forEach(item => {
            item.element.style.display = '';
        });
    }
    
    // Render current page
    function renderPage() {
        const startIndex = (currentPage - 1) * recordsPerPage;
        const endIndex = startIndex + recordsPerPage;
        const pageData = filteredData.slice(startIndex, endIndex);
        
        // Hide all rows first
        currentData.forEach(item => {
            item.element.style.display = 'none';
        });
        
        // Show only current page rows
        pageData.forEach(item => {
            item.element.style.display = '';
        });
    }
    
    // Initialize
    currentData = extractTableData();
    filteredData = [...currentData];
    
    // Search event listener
    searchInput.addEventListener('input', function() {
        applySearch(this.value);
    });
    
    // Initial render
    renderPagination();
}

// ===== ACTIVE MENU HIGHLIGHTING =====
function setActiveMenu() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    const menuItems = document.querySelectorAll('#main-menu a');
    
    // Remove any existing active classes first
    menuItems.forEach(item => {
        item.classList.remove('active-menu-item');
        const parentLi = item.closest('li');
        if (parentLi) {
            parentLi.classList.remove('active-menu-parent');
        }
    });
    
    // Find and highlight the exact matching menu item
    let foundExactMatch = false;
    
    // First pass: look for exact matches
    menuItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && currentPage === href) {
            item.classList.add('active-menu-item');
            const parentLi = item.closest('li');
            if (parentLi) {
                parentLi.classList.add('active-menu-parent');
            }
            foundExactMatch = true;
        }
    });
    
    // Second pass: if no exact match, look for partial matches
    if (!foundExactMatch) {
        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href && currentPage.includes(href.replace('.php', ''))) {
                item.classList.add('active-menu-item');
                const parentLi = item.closest('li');
                if (parentLi) {
                    parentLi.classList.add('active-menu-parent');
                }
            }
        });
    }
}

// ===== UTILITY FUNCTIONS =====
function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
}

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
// ===== GLOBAL DATABASE TOGGLE - ALWAYS ACTIVE =====
function initGlobalDatabaseToggle() {
    const useLocal = getCookie('useLocalDB') === 'true';
    const toggle = document.getElementById('globalDatabaseToggle');
    const status = document.getElementById('globalDatabaseStatus');
    
    if (toggle && status) {
        toggle.checked = useLocal;
        status.textContent = useLocal ? 'Using Local MySQL' : 'Using Supabase';
        status.className = useLocal ? 'ml-2 text-success' : 'ml-2 text-primary';
        
        toggle.addEventListener('change', function() {
            const useLocal = this.checked;
            setCookie('useLocalDB', useLocal, 30);
            status.textContent = useLocal ? 'Using Local MySQL' : 'Using Supabase';
            status.className = useLocal ? 'ml-2 text-success' : 'ml-2 text-primary';
            window.location.reload();
        });
    }
}

// ===== GLOBAL COMPONENT MANAGEMENT =====
function setupGlobalTableComponents(config) {
    const {
        tableId,
        searchPlaceholder = "Search...",
        recordsPerPage = 7,
        searchColumns = []
    } = config;
    
    // Enable search and pagination (toggle is always active)
    enableGlobalSearch(searchPlaceholder);
    enableGlobalPagination();
    
    // Initialize search and pagination
    initTableSearchPagination({
        tableId: tableId,
        searchId: 'globalTableSearch',
        paginationId: 'globalPaginationContainer',
        recordsPerPage: recordsPerPage,
        searchColumns: searchColumns
    });
}

function enableGlobalSearch(placeholder = "Search...") {
    const searchContainer = document.getElementById('globalSearchContainer');
    const searchInput = document.getElementById('globalTableSearch');
    
    if (searchContainer && searchInput) {
        searchContainer.style.display = 'block';
        searchInput.placeholder = placeholder;
    }
}

function enableGlobalPagination() {
    const paginationContainer = document.getElementById('globalPaginationContainer');
    if (paginationContainer) {
        paginationContainer.style.display = 'block';
        paginationContainer.style.padding = '20px 0';
        paginationContainer.style.background = '#f8f9fa';
        paginationContainer.style.borderTop = '1px solid #dee2e6';
    }
}

// ===== INITIALIZE ALL COMPONENTS WHEN DOM LOADS =====
document.addEventListener('DOMContentLoaded', function() {
    // Initialize global database toggle (always active)
    initGlobalDatabaseToggle();
    
    // Initialize active menu highlighting
    setActiveMenu();
    
    // Initialize existing spinner and toast functions
    createToastContainer();
    createSpinnerOverlay();
});