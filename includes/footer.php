</main>

<footer class="site-footer">
  <p>&copy; <?= date('Y') ?> QuickBite. Local food, instant delight.</p>
</footer>

<script src="<?= $basePath ?>js/main.js"></script>
<?php if (!empty($pageScripts)): foreach ($pageScripts as $script): ?>
<script src="<?= $basePath . $script ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>
