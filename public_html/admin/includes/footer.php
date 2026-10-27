    <!-- Toast Container -->
    <div id="toastContainer" class="fixed bottom-6 right-6 z-[100] flex flex-col gap-3"></div>

    <!-- Admin Core JavaScript -->
    <script>
        // ==========================================
        // SIDEBAR TOGGLE
        // ==========================================
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // ==========================================
        // DARK MODE TOGGLE
        // ==========================================
        function toggleDarkMode() {
            const html = document.documentElement;
            html.classList.toggle('dark');
            const isDark = html.classList.contains('dark');
            localStorage.setItem('ve_dark_mode', isDark ? 'true' : 'false');
        }

        // Check saved dark mode preference
        if (localStorage.getItem('ve_dark_mode') === 'true') {
            document.documentElement.classList.add('dark');
        }

        // ==========================================
        // NOTIFICATION DROPDOWN
        // ==========================================
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            const profileDropdown = document.getElementById('profileDropdown');
            if (profileDropdown) profileDropdown.classList.add('hidden');
            dropdown.classList.toggle('hidden');
        }

        // ==========================================
        // PROFILE DROPDOWN
        // ==========================================
        function toggleProfile() {
            const dropdown = document.getElementById('profileDropdown');
            const notifDropdown = document.getElementById('notificationDropdown');
            if (notifDropdown) notifDropdown.classList.add('hidden');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdowns on outside click
        document.addEventListener('click', function(e) {
            const notifDropdown = document.getElementById('notificationDropdown');
            const profileDropdown = document.getElementById('profileDropdown');
            
            if (notifDropdown && !e.target.closest('[onclick="toggleNotifications()"]') && !e.target.closest('#notificationDropdown')) {
                notifDropdown.classList.add('hidden');
            }
            if (profileDropdown && !e.target.closest('[onclick="toggleProfile()"]') && !e.target.closest('#profileDropdown')) {
                profileDropdown.classList.add('hidden');
            }
        });

        // ==========================================
        // TOAST NOTIFICATIONS
        // ==========================================
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-times-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            const colors = {
                success: 'bg-emerald-500',
                error: 'bg-spice-chili-500',
                warning: 'bg-spice-turmeric-500',
                info: 'bg-blue-500'
            };

            const toast = document.createElement('div');
            toast.className = `flex items-center gap-3 px-5 py-3.5 rounded-xl ${colors[type]} text-white shadow-xl transform translate-x-full transition-transform duration-300`;
            toast.innerHTML = `
                <i class="fas ${icons[type]}"></i>
                <span class="text-[13px] font-medium">${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-2 opacity-70 hover:opacity-100">
                    <i class="fas fa-times text-xs"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full');
            });

            // Auto remove
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // ==========================================
        // CONFIRM DELETE MODAL (SweetAlert2)
        // ==========================================
        function confirmDelete(itemName) {
            return Swal.fire({
                title: 'Delete ' + itemName + '?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#B9412E',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-6',
                    cancelButton: 'rounded-xl px-6'
                }
            });
        }

        // ==========================================
        // KEYBOARD SHORTCUT - CMD+K SEARCH
        // ==========================================
        document.addEventListener('keydown', function(e) {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('header input[type="text"]');
                if (searchInput) searchInput.focus();
            }
        });
    </script>
</body>
</html>
