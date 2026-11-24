</main>
<?php if (!($isAuthPage ?? false)): ?>
<footer class="app-footer py-4 mt-5">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-2">
        <span>© <?= date('Y') ?> Belleza Citas</span>
        <span>Sistema de reservas · Aplicación web progresiva</span>
    </div>
</footer>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(APP_URL) ?>/assets/js/app.js"></script>
</body>
</html>
