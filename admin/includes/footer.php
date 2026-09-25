    </div><!-- /.admin-content -->
</div><!-- /.admin-main -->

<script>
// Sidebar toggle for mobile
document.querySelector('.admin-menu-toggle')?.addEventListener('click', () => {
    document.getElementById('admin-sidebar').classList.toggle('open');
});

// Auto-dismiss admin flash after 5s
document.querySelectorAll('.admin-flash-stack .flash').forEach(el => {
    setTimeout(() => {
        el.style.transition = 'opacity .4s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
    }, 5000);
});

// Confirm-delete helper
document.querySelectorAll('form[data-confirm]').forEach(f => {
    f.addEventListener('submit', ev => {
        if (!confirm(f.dataset.confirm)) ev.preventDefault();
    });
});
</script>
</body>
</html>