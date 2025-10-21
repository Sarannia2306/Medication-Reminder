<?php include __DIR__.'/includes/header.php'; ?>
<section class="card page-header">
  <div class="row">
    <h2 class="no-margin">Contact Us</h2>
  </div>
  <p class="helper">We'd love to hear from you. Fill in the form and we'll get back to you.</p>
  <form id="contactForm" class="grid" novalidate>
    <div>
      <label class="label" for="c_name">Your Name</label>
      <input id="c_name" name="c_name" class="input" placeholder="e.g., Alex Tan" required />
    </div>
    <div>
      <label class="label" for="c_email">Email</label>
      <input id="c_email" name="c_email" type="email" class="input" placeholder="you@example.com" required />
    </div>
    <div class="grid-full">
      <label class="label" for="c_msg">Message</label>
      <textarea id="c_msg" name="c_msg" class="input" rows="5" placeholder="How can we help?" required></textarea>
    </div>
    <div class="row">
      <button class="btn btn-primary" type="submit">Send Message</button>
      <a class="btn btn-ghost" href="index.php">Cancel</a>
    </div>
  </form>
</section>
<script>
  document.body.setAttribute('data-page','contact');
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
