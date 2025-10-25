<?php include __DIR__.'/includes/header.php'; ?>
<section class="card page-header">
  <div class="row">
    <h2 class="no-margin">Login</h2>
  </div>
  <form id="loginForm" class="grid" novalidate>
    <div>
      <label class="label" for="email">Email</label>
      <input id="email" name="email" type="email" class="input" required placeholder="you@example.com" />
    </div>
    <div>
      <label class="label" for="password">Password</label>
      <input id="password" name="password" type="password" class="input" required placeholder="••••••••" />
    </div>
    <div class="row mt-1">
      <button class="btn btn-primary" type="submit">Login</button>
      <a class="btn btn-ghost" href="register.php">Register</a>
    </div>
  </form>
  <p class="helper">Enter your registered email and password to login.</p>
</section>
<script>
  document.body.setAttribute('data-page','login');
</script>
