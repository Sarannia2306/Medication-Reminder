<?php include __DIR__.'/includes/header.php'; ?>
<section class="grid cols-2">
  <div class="card">
    <h2>Adherence Summary</h2>
    <div class="kpis">
      <div class="kpi"><div class="value" id="cgTaken">0</div><div class="label">Taken</div></div>
      <div class="kpi"><div class="value" id="cgMissed">0</div><div class="label">Missed</div></div>
      <div class="kpi"><div class="value" id="cgTotal">0</div><div class="label">Logged</div></div>
      <div class="kpi"><div class="value" id="cgRate">0%</div><div class="label">Adherence</div></div>
    </div>
  </div>
  <div class="card">
    <h2>Caregiver Tools</h2>
    <p class="helper">This view simulates what a caregiver might see: recent activity and adherence.</p>
    <div class="row" style="margin-top:8px">
      <div>
        <label class="label" for="patientSel">Select patient</label>
        <select id="patientSel" class="input">
          <option value="patientA">Patient A</option>
          <option value="patientB">Patient B</option>
        </select>
        <p class="helper">Each patient uses a different meditrack:medications patient.</p>
      </div>
    </div>
  </div>
</section>
<section class="card">
  <h2>Recent Activity</h2>
  <div id="caregiverFeed" class="list"></div>
</section>
<script>
  document.body.setAttribute('data-page','caregiver');
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
