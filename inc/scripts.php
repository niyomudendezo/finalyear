<footer class="footer-copyright">
    <p>
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($sys_org); ?> &mdash; Road Damage Detection System
    </p>
</footer>
</section>

<script src="assets/vendor/jquery/jquery.js"></script>
<script src="assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
<script src="assets/vendor/nanoscroller/nanoscroller.js"></script>
<script src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="assets/vendor/magnific-popup/magnific-popup.js"></script>
<script src="assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>
<script src="assets/vendor/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
<script src="assets/vendor/jquery-appear/jquery.appear.js"></script>
<script src="assets/vendor/select2/select2.js"></script>
<script src="assets/vendor/jquery-datatables/media/js/jquery.dataTables.js"></script>
<script src="assets/vendor/jquery-datatables-bs3/assets/js/datatables.js"></script>
<script src="assets/vendor/morris/morris.js"></script>
<script src="assets/vendor/raphael/raphael.js"></script>
<script src="assets/javascripts/theme.js"></script>
<script src="assets/javascripts/theme.custom.js"></script>
<script src="assets/javascripts/theme.init.js"></script>
<script src="assets/javascripts/tables/examples.datatables.default.js"></script>
<script src="assets/javascripts/ui-elements/examples.modals.js"></script>
<script>
(function(){
    var current=(location.pathname.split('/').pop()||'home.php').split('?')[0];
    document.querySelectorAll('#menu li').forEach(function(li){li.classList.remove('nav-active');});
    document.querySelectorAll('#menu a[href]').forEach(function(a){
        if(a.getAttribute('href').split('?')[0]===current){
            var li=a.closest('li'); if(li) li.classList.add('nav-active');
            var parent=a.closest('.nav-parent'); if(parent) parent.classList.add('nav-expanded','nav-active');
        }
    });
})();
</script>
</body>
</html>
