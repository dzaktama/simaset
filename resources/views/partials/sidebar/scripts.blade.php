<script>
    // Alpine Store removed. 
    // Popovers now use Local State + Event Bus ($dispatch) for robustness against loading order issues.

    // Toggle Sidebar Menu handled via Alpine.js x-data in each menu file.
    // This script now only handles legacy or global overrides if needed.

    // Global Mobile Sidebar Toggle (AlpineJS helper)
    window.toggleMobile = function() {
        const mobileContainer = document.getElementById('mobile-sidebar-container');
        if(mobileContainer) {
            // Kita gunakan Dispatch Event ke Alpine atau manipulasi class manual
            // Paling aman manipulasi class 'hidden' jika tidak pakai x-show
            if(mobileContainer.classList.contains('hidden')) {
                mobileContainer.classList.remove('hidden');
            } else {
                mobileContainer.classList.add('hidden');
            }
        }
    }
    // Alias for backward compatibility (used in topbar/sidebar header)
    window.toggleMobileSidebar = window.toggleMobile;
</script>
