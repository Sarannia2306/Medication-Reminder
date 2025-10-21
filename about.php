<?php include __DIR__.'/includes/header.php'; ?>
<section class="card page-header">
  <div class="row">
    <h2 class="no-margin">About MediTrack</h2>
  </div>
  <p class="helper">MediTrack is a simple medication tracking system that helps patients and caregivers manage daily medicines easily. It keeps track of doses, shows reminders, and records whether medicines are taken or missed.

Caregivers can add and manage patient details, update medication schedules, and monitor progress. With an easy-to-use design and customizable settings, MediTrack makes medication management clear, organized, and stress-free.</p>
</section>
<section class="card">
  <h2>Quick Guide</h2>
  <div class="list">
    <div class="item">
      <div class="item-left"><div class="item-icon"><i class="bi bi-people"></i></div><div><div class="item-title">Add a Patient</div><div class="item-meta">Go to Patients, fill details, and save. Then click View Medications.</div></div></div>
    </div>
    <div class="item">
      <div class="item-left"><div class="item-icon"><i class="bi bi-capsule"></i></div><div><div class="item-title">Add a Medication</div><div class="item-meta">From Patient Medications, use Add Medication. Choose frequency, enter one time, we auto-generate the rest.</div></div></div>
    </div>
    <div class="item">
      <div class="item-left"><div class="item-icon"><i class="bi bi-check-circle"></i></div><div><div class="item-title">Mark Taken/Missed</div><div class="item-meta">On Home or Patient pages, use buttons next to reminders. Status is recorded per-dose.</div></div></div>
    </div>
  </div>
</section>
<script>
  document.body.setAttribute('data-page','about');
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
