<?php
// includes/kids-footer.php — Closes the shared layout and loads scripts
$extraJs = $extraJs ?? [];
?>
    </main>

    <script src="js/kids.js" defer></script>
    <?php foreach ($extraJs as $js): ?>
        <script src="js/<?php echo htmlspecialchars($js, ENT_QUOTES, 'UTF-8'); ?>.js" defer></script>
    <?php endforeach; ?>
</body>
</html>
