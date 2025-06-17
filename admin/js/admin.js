document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');

    if (sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            content.classList.toggle('active');
        });
    }

    // Active menu item
    const currentLocation = location.pathname;
    const menuItems = document.querySelectorAll('#sidebar ul li a');
    
    menuItems.forEach(item => {
        if (item.getAttribute('href') === currentLocation) {
            item.parentElement.classList.add('active');
        }
    });

    // Dropdown functionality
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdownMenu = this.nextElementSibling;
            dropdownMenu.classList.toggle('show');
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.matches('.dropdown-toggle')) {
            const dropdowns = document.querySelectorAll('.dropdown-menu.show');
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });

    // Table row hover effect
    const tableRows = document.querySelectorAll('tbody tr');
    
    tableRows.forEach(row => {
        row.addEventListener('mouseover', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        row.addEventListener('mouseout', function() {
            this.style.backgroundColor = '';
        });
    });

    // View button click handler
    const viewButtons = document.querySelectorAll('.btn-primary');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const bookingId = row.querySelector('td:first-child').textContent;
            // Add your view booking logic here
            console.log('Viewing booking:', bookingId);
        });
    });

    // Form validation
    const addRoomForm = document.getElementById('addRoomForm');
    if (addRoomForm) {
        addRoomForm.addEventListener('submit', function(e) {
            const required = ['room_number', 'room_type', 'floor_number', 'capacity', 'price_per_night'];
            let isValid = true;

            required.forEach(field => {
                const input = this.querySelector(`[name="${field}"]`);
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    }

    // Add form validation for edit form
    const editRoomForm = document.getElementById('editRoomForm');
    if (editRoomForm) {
        editRoomForm.addEventListener('submit', function(e) {
            const required = ['room_number', 'room_type', 'floor_number', 'capacity', 'price_per_night'];
            let isValid = true;

            required.forEach(field => {
                const input = this.querySelector(`[name="${field}"]`);
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    }

    // Sidebar toggle for mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('hidden');
            document.body.classList.toggle('sidebar-open');
        });
    }

    // Hide sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.add('hidden');
                document.body.classList.remove('sidebar-open');
            }
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('hidden');
        } else {
            sidebar.classList.add('hidden');
        }
    });
});

function loadRoomData(roomId) {
    fetch(`get_room.php?id=${roomId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const room = data.data;
                document.getElementById('edit_room_id').value = room.id;
                document.getElementById('edit_room_number').value = room.room_number;
                document.getElementById('edit_room_type').value = room.room_type;
                document.getElementById('edit_floor_number').value = room.floor_number;
                document.getElementById('edit_capacity').value = room.capacity;
                document.getElementById('edit_price_per_night').value = room.price_per_night;
                document.getElementById('edit_description').value = room.description;

                // Clear all checkboxes first
                const checkboxes = document.querySelectorAll('#edit_facilities input[type="checkbox"]');
                checkboxes.forEach(checkbox => checkbox.checked = false);

                // Check the facilities that the room has
                if (room.facilities) {
                    room.facilities.forEach(facilityId => {
                        const checkbox = document.querySelector(`#edit_facilities input[value="${facilityId}"]`);
                        if (checkbox) checkbox.checked = true;
                    });
                }

                // Show the modal
                const editModal = new bootstrap.Modal(document.getElementById('editRoomModal'));
                editModal.show();
            } else {
                alert(data.error || 'Failed to load room data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load room data');
        });
}