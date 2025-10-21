<?php include __DIR__.'/includes/header.php'; ?>
<section class="card">
  <h2>Login</h2>
  <form id="loginForm" class="grid" novalidate>
    <div>
      <label class="label" for="email">Email</label>
      <input id="email" name="email" type="email" class="input" required placeholder="you@example.com" />
    </div>
    <div>
      <label class="label" for="password">Password</label>
      <input id="password" name="password" type="password" class="input" required placeholder="••••••••" />
    </div>
    <div class="row" style="margin-top:8px">
      <button class="btn btn-primary" type="submit">Login</button>
      <a class="btn btn-ghost" href="register.php">Register</a>
    </div>
  </form>
  <p class="helper">This is a front-end demo. Credentials are stored locally.</p>
</section>
<script>
  document.body.setAttribute('data-page','login');
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
