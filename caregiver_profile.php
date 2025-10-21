<?php include __DIR__.'/includes/header.php'; ?>
<section class="card page-header">
  <div class="row" style="align-items:center; justify-content:space-between">
    <h2 class="no-margin">Caregiver Profile</h2>
    <button id="logoutBtnProfile" class="btn btn-danger" type="button">Logout</button>
  </div>
  <form id="profileForm" class="grid" novalidate>
    <div>
      <label class="label" for="p_name">Full Name</label>
      <input id="p_name" name="p_name" class="input" required />
    </div>
    <div class="row">
      <div>
        <label class="label" for="p_email">Email</label>
        <input id="p_email" name="p_email" type="email" class="input" required />
      </div>
      <div>
        <label class="label" for="p_phone">Phone</label>
        <input id="p_phone" name="p_phone" class="input" />
      </div>
    </div>
    <div class="row">
      <div>
        <label class="label" for="p_org">Organization</label>
        <input id="p_org" name="p_org" class="input" />
      </div>
      <div>
        <label class="label" for="p_exp">Experience (yrs)</label>
        <input id="p_exp" name="p_exp" type="number" min="0" class="input" />
      </div>
    </div>
    <div class="row mt-1">
      <button class="btn btn-primary" type="submit">Save Changes</button>
      <button id="resetApp" class="btn btn-danger" type="button">Reset App</button>
    </div>
  </form>
  <p class="helper">Your profile is saved locally in your browser.</p>
</section>
<script>
  document.body.setAttribute('data-page','profile');
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
