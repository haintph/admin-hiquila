<script src="/admin/assets/js/config.js"></script>
<script src="/admin/assets/js/vendor.js"></script>

<!-- App Javascript (Require in all Page) -->
<script src="/admin/assets/js/app.js"></script>

<!-- Vector Map Js -->
<script src="/admin/assets/vendor/jsvectormap/js/jsvectormap.min.js"></script>
<script src="/admin/assets/vendor/jsvectormap/maps/world-merc.js"></script>
<script src="/admin/assets/vendor/jsvectormap/maps/world.js"></script>

<!-- Dashboard Js -->
<script src="/admin/assets/js/pages/dashboard.js"></script>

<!-- Thêm vào <head> hoặc trước </body> -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleVariants(dishId) {
        let row = document.getElementById('variants-' + dishId);
        if (row.style.display === "none") {
            row.style.display = "table-row";
        } else {
            row.style.display = "none";
        }
    }
</script>