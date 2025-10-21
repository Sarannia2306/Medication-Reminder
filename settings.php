<?php include __DIR__.'/includes/header.php'; ?>
<section class="card">
  <h2>Accessibility & Preferences</h2>
  <div class="grid cols-2">
    <div>
      <label class="label">Theme Mode</label>
      <div class="segmented">
        <button id="themeLight" class="btn btn-secondary" type="button">🌞 Light</button>
        <button id="themeDark" class="btn btn-secondary" type="button">🌙 Dark</button>
      </div>
    </div>
    <div>
      <label class="label">Font Size</label>
      <div class="segmented">
        <button class="btn btn-secondary" type="button" data-font="small">Small</button>
        <button class="btn btn-secondary" type="button" data-font="medium">Medium</button>
        <button class="btn btn-secondary" type="button" data-font="large">Large</button>
      </div>
    </div>
  </div>
  <div class="grid cols-2">
    <div>
      <label class="label" for="contrastToggle">Contrast Mode</label>
      <div class="row" style="flex-wrap:wrap; align-items:center; gap:10px">
        <input id="contrastToggle" type="checkbox" />
        <span class="helper">High contrast for better readability</span>
      </div>
    </div>
  </div>
  <hr class="sep" />
  <h2>Notification Preferences</h2>
  <div class="grid cols-2">
    <div>
      <label class="label" for="notifEnable">Enable Daily Reminders</label>
      <input id="notifEnable" type="checkbox" />
    </div>
    <div>
      <label class="label" for="notifLead">Reminder Time</label>
      <select id="notifLead" class="input">
        <option value="30_before">30 mins before</option>
        <option value="15_before">15 mins before</option>
        <option value="on_time">On time</option>
        <option value="60_before">60 mins before</option>
      </select>
    </div>
  </div>
  <p class="helper">Preferences are simulated for UX and saved locally.</p>
</section>
<script>
  document.body.setAttribute('data-page','settings');
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
