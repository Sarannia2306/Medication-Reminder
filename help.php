<?php include __DIR__.'/includes/header.php'; ?>
<section class="card">
  <h2>Help</h2>
  <div class="grid cols-2">
    <div>
      <h3>Getting Started</h3>
      <p class="helper">Add patients, then add medications for each patient. Use the Caregiver page to switch active patients and view adherence.</p>
      <ul>
        <li>Use <strong>Patients</strong> to create a patient profile.</li>
        <li>Use <strong>Add</strong> to add medications (time, frequency, meal relation).</li>
        <li>Use <strong>List</strong> to mark doses as taken or missed.</li>
        <li>Open <strong>Caregiver</strong> to see KPIs and switch patients.</li>
      </ul>
    </div>
    <div>
      <h3>Tips</h3>
      <ul>
        <li>Use <strong>Settings</strong> to switch Light/Dark, font size, and contrast mode.</li>
        <li>"Every Morning/Afternoon/Night" map to 08:00/14:00/21:00 automatically.</li>
        <li>All data is stored locally in your browser for this prototype.</li>
      </ul>
    </div>
  </div>
</section>
<script>
  document.body.setAttribute('data-page','help');
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
