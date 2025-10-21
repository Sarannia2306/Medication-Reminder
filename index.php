<?php include __DIR__.'/includes/header.php'; ?>
<section class="card">
  <div class="row" style="align-items:center; justify-content:space-between">
    <h2 style="margin:0">Dashboard</h2>
    <span id="patientBadge" class="status upcoming" aria-live="polite"></span>
  </div>
</section>
<section class="grid cols-2">
  <div class="card">
    <h2>Next Dose</h2>
    <div id="nextDoseBox" class="item"></div>
  </div>
  <div class="card">
    <h2>Quick Actions</h2>
    <div class="row">
      <a class="btn btn-primary" href="add_medication.php">Add Medication</a>
      <a class="btn btn-secondary" href="medication_list.php">View List</a>
    </div>
    <p class="helper">Mark the next dose as Taken or Missed directly.</p>
  </div>
</section>

<section class="card">
  <h2>Upcoming Reminders</h2>
  <div id="reminders" class="list"></div>
</section>

<section class="card">
  <h2>Today at a Glance</h2>
  <div class="kpis">
    <div class="kpi"><div class="value" id="kpiTaken">0</div><div class="label">Taken</div></div>
    <div class="kpi"><div class="value" id="kpiMissed">0</div><div class="label">Missed</div></div>
    <div class="kpi"><div class="value" id="kpiTotal">0</div><div class="label">Logged</div></div>
    <div class="kpi"><div class="value" id="kpiRate">0%</div><div class="label">Adherence</div></div>
  </div>
</section>
<script>
  document.body.setAttribute('data-page','home');
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
