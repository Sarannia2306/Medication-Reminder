<?php include __DIR__.'/includes/header.php'; ?>
<section class="card">
  <div class="row" style="align-items:center; justify-content:space-between">
    <h2 style="margin:0">Patients</h2>
    <a class="btn btn-primary" href="#patientForm">Add Patient</a>
  </div>
</section>
<section class="card">
  <h2>Patient List</h2>
  <div id="patientsList" class="list" aria-live="polite"></div>
</section>
<section class="card">
  <h2>Add / Edit Patient</h2>
  <form id="patientForm" class="grid" novalidate>
    <input type="hidden" id="pid" name="pid" />
    <div>
      <label class="label" for="pname">Full Name</label>
      <input id="pname" name="pname" class="input" required placeholder="John Smith" />
    </div>
    <div class="row">
      <div>
        <label class="label" for="page">Age</label>
        <input id="page" name="page" class="input" placeholder="70" />
      </div>
      <div>
        <label class="label" for="pcond">Condition</label>
        <input id="pcond" name="pcond" class="input" placeholder="Hypertension" />
      </div>
    </div>
    <div>
      <label class="label" for="pcontact">Contact</label>
      <input id="pcontact" name="pcontact" class="input" placeholder="+44 1234 567890" />
    </div>
    <div class="row" style="margin-top:8px">
      <button class="btn btn-primary" type="submit">Save Patient</button>
    </div>
    <p class="helper">Selecting "View Medications" sets the active patient and opens their schedule.</p>
  </form>
</section>
<script>
  document.body.setAttribute('data-page','patients');
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
