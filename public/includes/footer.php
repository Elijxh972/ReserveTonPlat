</main>
    <footer class="site-footer">
        <div class="footer-inner">
            <p class="footer-brand">RéserveTonPlat</p>
            <p class="footer-desc">Système de réservation du CROUS Schœlcher — Université des Antilles</p>
            <p class="footer-legal">Accès réservé aux etudiants du campus de Schœlcher.</p>
            <p class="footer-copy">&copy; <?= date('Y') ?> RéserveTonPlat. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
    function openCancelModal() {
        const modal = document.getElementById("cancelModal");
        modal.style.display = "flex";
    }

    function closeCancelModal() {
        const modal = document.getElementById("cancelModal");
        modal.style.display = "none";
    }

    function confirmCancel() {
        document.querySelector('form[action="../src/decommander_process.php"]').submit();
    }
    </script>
</body>
</html>