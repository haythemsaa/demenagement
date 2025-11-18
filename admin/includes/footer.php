            </div> <!-- .content-area -->
        </main> <!-- .main-content -->
    </div> <!-- .admin-wrapper -->

    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        }

        // Confirmation de suppression
        function confirmDelete(message) {
            return confirm(message || 'Êtes-vous sûr de vouloir supprimer cet élément ?');
        }

        // Auto-hide success messages
        document.addEventListener('DOMContentLoaded', function() {
            const successMessages = document.querySelectorAll('.alert-success');
            successMessages.forEach(function(msg) {
                setTimeout(function() {
                    msg.style.opacity = '0';
                    setTimeout(function() {
                        msg.remove();
                    }, 300);
                }, 5000);
            });
        });
    </script>
</body>
</html>
