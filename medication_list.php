<?php include __DIR__.'/includes/header.php'; ?>
<section class="card page-header">
  <div class="row" style="justify-content:space-between; align-items:center">
    <h2 class="no-margin">Medication List</h2>
    <a class="btn btn-primary" href="add_medication.php">Add</a>
  </div>
  <hr class="sep" />
  <div id="medList" class="list" aria-live="polite"></div>
  <p class="helper">Use Edit to change details, or Delete to remove a medication.</p>
</section>
<script>
  document.body.setAttribute('data-page','list');
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
