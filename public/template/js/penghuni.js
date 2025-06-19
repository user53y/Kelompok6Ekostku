document.addEventListener('DOMContentLoaded', function() {
    // Mobile nav toggle
    const mobileNav = document.getElementById('mobileNav');
    const toggleBtn = document.getElementById('toggleNav');

    // Profile dropdown
    const profileToggle = document.querySelector('.profile-toggle');
    const profileMenu = document.querySelector('.profile-menu');

    // Notification dropdown
    const notifIcon = document.querySelector('.notification-icon');
    const notifDropdown = document.querySelector('.notification-dropdown');

    // Toggle functions
    function toggleDropdown(element, event) {
        event.stopPropagation();
        element.classList.toggle('show');
    }

    // Event listeners
    if (profileToggle) {
        profileToggle.addEventListener('click', (e) => toggleDropdown(profileMenu, e));
    }

    if (notifIcon) {
        notifIcon.addEventListener('click', (e) => toggleDropdown(notifDropdown, e));
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (profileMenu && !profileToggle.contains(e.target)) {
            profileMenu.classList.remove('show');
        }
        if (notifDropdown && !notifIcon.contains(e.target)) {
            notifDropdown.classList.remove('show');
        }
    });

    // Mobile nav
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileNav.classList.toggle('show');
        });
    }

    // Close mobile nav when clicking outside
    document.addEventListener('click', function(e) {
        if (mobileNav && !mobileNav.contains(e.target) && !toggleBtn.contains(e.target)) {
            mobileNav.classList.remove('show');
        }
    });
});
