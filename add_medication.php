<?php include __DIR__.'/includes/header.php'; ?>
<section class="card">
  <h2 id="formTitle">Add Medication</h2>
  <form id="medForm" class="grid" novalidate>
    <div>
      <label class="label" for="name">Medication Name</label>
      <input id="name" name="name" class="input" placeholder="e.g., Paracetamol" required />
    </div>
    <div>
      <label class="label" for="dosage">Dosage</label>
      <input id="dosage" name="dosage" class="input" placeholder="e.g., 500 mg" required />
    </div>
    <div class="row">
      <div>
        <label class="label" for="time">Time (first/only dose)</label>
        <input type="time" id="time" name="time" class="input" required />
      </div>
      <div>
        <label class="label" for="frequency">Frequency</label>
        <select id="frequency" name="frequency" class="input">
          <option>Once Daily</option>
          <option>Twice Daily</option>
          <option>Thrice Daily</option>
          <option>Every Morning</option>
          <option>Every Afternoon</option>
          <option>Every Night</option>
        </select>
      </div>
    </div>
    <div>
      <label class="label" for="meal">Timing (Meal Relation)</label>
      <select id="meal" name="meal" class="input">
        <option>Before Meal</option>
        <option>After Meal</option>
        <option>With Meal</option>
      </select>
    </div>
    <div class="row" style="margin-top:8px">
      <button id="submitBtn" class="btn btn-primary" type="submit">Add Medication</button>
      <a class="btn btn-ghost" href="medication_list.php">Cancel</a>
    </div>
  </form>
  <p class="helper">All data is stored locally.</p>
</section>
<script>
  document.body.setAttribute('data-page','add');
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
