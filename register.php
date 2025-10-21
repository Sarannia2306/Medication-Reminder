<?php include __DIR__.'/includes/header.php'; ?>
<section class="card page-header">
  <div class="row">
    <h2 class="no-margin">Register</h2>
  </div>
  <form id="registerForm" class="grid" novalidate>
    <div>
      <label class="label" for="r_name">Full Name</label>
      <input id="r_name" name="name" class="input" required placeholder="Jane Doe" />
    </div>
    <div>
      <label class="label" for="r_email">Email</label>
      <input id="r_email" name="email" type="email" class="input" required placeholder="you@example.com" />
    </div>
    <div class="row">
      <div>
        <label class="label" for="r_phone">Phone</label>
        <input id="r_phone" name="phone" class="input" placeholder="+44 1234 567890" />
      </div>
      <div>
        <label class="label" for="r_org">Organization</label>
        <input id="r_org" name="org" class="input" placeholder="Clinic/Hospital" />
      </div>
    </div>
    <div class="row">
      <div>
        <label class="label" for="r_password">Password</label>
        <input id="r_password" name="password" type="password" class="input" required placeholder="••••••••" />
      </div>
      <div>
        <label class="label" for="r_exp">Experience (yrs)</label>
        <input id="r_exp" name="experience" type="number" min="0" class="input" placeholder="0" />
      </div>
    </div>
    <div class="row mt-1">
      <button class="btn btn-primary" type="submit">Create Account</button>
      <a class="btn btn-ghost" href="login.php">Back to Login</a>
    </div>
  </form>
  <p class="helper">Data is stored locally in your browser for this prototype.</p>
</section>
<script>
  document.body.setAttribute('data-page','register');
</script>
