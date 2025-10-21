<?php
// Shared footer include
?>
  </main>
  <footer class="app-footer">
    <div class="footer-grid container">
      <div class="footer-brand">
        <div class="brand"><span class="brand-icon" aria-hidden="true">💊</span><span class="brand-text">MediTrack</span></div>
        <p class="helper">Care made simple.</p>
      </div>
      <div class="footer-social">
        <a href="contact.php" class="social" aria-label="Email">✉️</a>
        <a href="help.php" class="social" aria-label="Help">❓</a>
        <a href="about.php" class="social" aria-label="Info">ℹ️</a>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <span id="year"></span> MediTrack</span>
      <span>No real medical advice</span>
    </div>
  </footer>
  <div id="toast" class="toast" role="status" aria-live="polite"></div>
  <div id="modal" class="modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-describedby="modalDesc">
    <div class="modal-content">
      <h2 id="modalTitle">Confirm</h2>
      <p id="modalDesc">Are you sure?</p>
      <div class="modal-actions">
        <button id="modalCancel" class="btn btn-secondary">Cancel</button>
        <button id="modalConfirm" class="btn btn-danger">Confirm</button>
      </div>
    </div>
  </div>
</body>
</html>
