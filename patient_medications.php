<?php include __DIR__.'/includes/header.php'; ?>
<section class="card">
  <div class="row" style="align-items:center; justify-content:space-between">
    <h2 id="pmHeader" style="margin:0">Patient Medications</h2>
    <div class="row" style="flex:0 0 auto">
      <a id="pmAdd" class="btn btn-primary" href="#">Add Medication</a>
    </div>
  </div>
</section>
<section class="card">
  <h2>Schedule</h2>
  <div id="pmList" class="list" aria-live="polite"></div>
</section>
<script>
  document.body.setAttribute('data-page','patient_meds');
  // Wire the Add button to include the current patientId
  (function(){
    const params = new URLSearchParams(location.search);
    const pid = params.get('patientId');
    const add = document.getElementById('pmAdd');
    if(add){ add.href = pid ? `add_medication.php?patientId=${encodeURIComponent(pid)}` : 'add_medication.php'; }
  })();
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
