<?php
/**
 * Footer HTML commun.
 * Variable attendue (optionnel) :
 *   $no_footer = true  → ne pas afficher la zone footer (utile pour login)
 *   $extra_scripts (string)
 */
?>
<?php if (empty($no_footer)): ?>
<footer class="zen-footer">
    <img src="assets/logo.svg" alt="<?= h(APP_NAME) ?>">
    <p class="slogan">« <?= h(APP_SLOGAN) ?> »</p>
    <p style="margin-top:14px;">© <?= APP_YEAR ?> <strong><?= h(APP_NAME) ?></strong> — Solution de microfinance moderne</p>
    <p style="margin-top:6px; font-size:0.82rem;">Projet étudiant · Spring Boot · Oracle · JWT · PHP — Groupe de 5</p>
</footer>
<?php endif; ?>

<script src="js/api.js"></script>

<script>
// Animation reveal au scroll (toutes les pages)
(function() {
    const els = document.querySelectorAll(".reveal");
    if (!els.length || !("IntersectionObserver" in window)) return;
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add("visible"); obs.unobserve(e.target); } });
    }, { threshold: 0.12 });
    els.forEach(el => obs.observe(el));
})();
</script>

<?= $extra_scripts ?? '' ?>
</body>
</html>
