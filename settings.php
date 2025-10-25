<?php include __DIR__.'/includes/header.php'; ?>
<section class="card page-header">
  <h2>Settings</h2>
  <hr class="sep" />
  
  <div class="settings-grid">
    <!-- Theme Settings -->
    <div class="settings-section">
      <h3>Appearance</h3>
      <div class="setting">
        <label class="label">Theme</label>
        <div class="segmented-control">
          <button id="themeLight" class="segmented-control-button">Light</button>
          <button id="themeDark" class="segmented-control-button">Dark</button>
        </div>
      </div>
      
      <div class="setting">
        <label class="label">Font Size</label>
        <div class="segmented-control">
          <button data-font="small" class="segmented-control-button">A-</button>
          <button data-font="medium" class="segmented-control-button">A</button>
          <button data-font="large" class="segmented-control-button">A+</button>
        </div>
      </div>
      
      <div class="setting">
        <label class="label">High Contrast</label>
        <label class="switch">
          <input type="checkbox" id="contrastToggle">
          <span class="slider"></span>
        </label>
      </div>
    </div>
    
    <!-- Notification Settings -->
    <div class="settings-section">
      <h3>Notifications</h3>
      <div class="setting">
        <label class="label">Enable Notifications</label>
        <label class="switch">
          <input type="checkbox" id="notifEnable">
          <span class="slider"></span>
        </label>
      </div>
      
      <div class="setting">
        <label class="label">Enable Sound</label>
        <label class="switch">
          <input type="checkbox" id="notifSound" checked>
          <span class="slider"></span>
        </label>
      </div>
      
      <div class="setting">
        <label class="label" for="reminderBefore">Remind Before (minutes)</label>
        <input type="number" id="reminderBefore" class="input" min="0" max="120" value="15">
      </div>
      
      <div class="setting">
        <label class="label" for="snoozeDuration">Snooze Duration (minutes)</label>
        <input type="number" id="snoozeDuration" class="input" min="1" max="60" value="5">
      </div>
      
      <button id="testNotification" class="btn btn-secondary">Test Notification</button>
    </div>
    
    <!-- Medication Reminders -->
    <div class="settings-section">
      <h3>Daily Medication Reminders</h3>
      
      <div id="medicationRemindersList" class="medication-reminders">
        <!-- Reminders will be listed here -->
      </div>
      
      <div class="add-reminder-form">
        <h4>Add New Reminder</h4>
        <div class="form-group">
          <label class="label" for="medName">Medication Name</label>
          <input type="text" id="medName" class="input" placeholder="e.g., Paracetamol">
        </div>
        
        <div class="form-group">
          <label class="label" for="medDosage">Dosage</label>
          <input type="text" id="medDosage" class="input" placeholder="e.g., 1 pill, 5ml">
        </div>
        
        <div class="form-group">
          <label class="label" for="reminderTime">Time</label>
          <input type="time" id="reminderTime" class="input" value="08:00">
        </div>
        
        <button id="addReminderBtn" class="btn btn-primary">Add Reminder</button>
      </div>
    </div>
  </div>
</section>

<!-- Reminder Item Template -->
<template id="reminderItemTemplate">
  <div class="reminder-item" data-id="">
    <div class="reminder-details">
      <span class="med-name"></span>
      <span class="med-dosage"></span>
      <span class="reminder-time"></span>
    </div>
    <div class="reminder-actions">
      <button class="btn btn-ghost btn-sm edit-reminder">Edit</button>
      <button class="btn btn-danger btn-sm delete-reminder">Delete</button>
    </div>
  </div>
</template>

<style>
.settings-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin-top: 1.5rem;
}

.settings-section {
  background: var(--card-bg);
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.setting {
  margin-bottom: 1.5rem;
}

.setting:last-child {
  margin-bottom: 0;
}

.segmented-control {
  display: flex;
  background: var(--bg-secondary);
  border-radius: 6px;
  padding: 4px;
  margin-top: 0.5rem;
}

.segmented-control-button {
  flex: 1;
  padding: 0.5rem;
  border: none;
  background: none;
  cursor: pointer;
  border-radius: 4px;
  transition: all 0.2s;
}

.segmented-control-button.active {
  background: var(--primary);
  color: white;
}

.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
  margin-left: auto;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 24px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: var(--primary);
}

input:focus + .slider {
  box-shadow: 0 0 1px var(--primary);
}

input:checked + .slider:before {
  transform: translateX(26px);
}

/* Medication Reminders Styles */
.medication-reminders {
  margin-bottom: 1.5rem;
  max-height: 300px;
  overflow-y: auto;
}

.reminder-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: var(--bg-secondary);
  border-radius: 6px;
  margin-bottom: 0.75rem;
}

.reminder-details {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.med-name {
  font-weight: 600;
  color: var(--text-primary);
}

.med-dosage {
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.reminder-time {
  font-size: 0.875rem;
  color: var(--primary);
  font-weight: 500;
}

.reminder-actions {
  display: flex;
  gap: 0.5rem;
}

.add-reminder-form {
  background: var(--bg-secondary);
  padding: 1.25rem;
  border-radius: 8px;
  margin-top: 1.5rem;
}

.add-reminder-form h4 {
  margin-top: 0;
  margin-bottom: 1rem;
  color: var(--text-primary);
}

.form-group {
  margin-bottom: 1rem;
}

.form-group:last-child {
  margin-bottom: 0;
}

.btn-sm {
  padding: 0.25rem 0.75rem;
  font-size: 0.875rem;
}

@media (max-width: 768px) {
  .settings-grid {
    grid-template-columns: 1fr;
  }
  
  .reminder-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }
  
  .reminder-actions {
    width: 100%;
    justify-content: flex-end;
  }
}
</style>

<script>
document.body.setAttribute('data-page','settings');

// Medication Reminders Management
document.addEventListener('DOMContentLoaded', function() {
  const remindersList = document.getElementById('medicationRemindersList');
  const addReminderBtn = document.getElementById('addReminderBtn');
  const medNameInput = document.getElementById('medName');
  const medDosageInput = document.getElementById('medDosage');
  const reminderTimeInput = document.getElementById('reminderTime');
  const reminderTemplate = document.getElementById('reminderItemTemplate');
  
  // Load reminders from localStorage
  let reminders = JSON.parse(localStorage.getItem('medicationReminders') || '[]');
  
  // Render reminders
  function renderReminders() {
    remindersList.innerHTML = '';
    
    if (reminders.length === 0) {
      remindersList.innerHTML = '<p class="empty-state">No reminders set. Add one below.</p>';
      return;
    }
    
    // Sort reminders by time
    const sortedReminders = [...reminders].sort((a, b) => {
      return a.time.localeCompare(b.time);
    });
    
    sortedReminders.forEach((reminder, index) => {
      const reminderItem = reminderTemplate.content.cloneNode(true);
      const item = reminderItem.querySelector('.reminder-item');
      
      item.setAttribute('data-id', reminder.id);
      item.querySelector('.med-name').textContent = reminder.name;
      item.querySelector('.med-dosage').textContent = reminder.dosage;
      
      // Format time (convert 24h to 12h with AM/PM)
      const [hours, minutes] = reminder.time.split(':');
      const time = new Date();
      time.setHours(parseInt(hours), parseInt(minutes));
      const formattedTime = time.toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit',
        hour12: true 
      });
      
      item.querySelector('.reminder-time').textContent = `Daily at ${formattedTime}`;
      
      // Add event listeners for edit and delete
      item.querySelector('.edit-reminder').addEventListener('click', () => {
        editReminder(reminder.id);
      });
      
      item.querySelector('.delete-reminder').addEventListener('click', () => {
        deleteReminder(reminder.id);
      });
      
      remindersList.appendChild(reminderItem);
    });
  }
  
  // Add new reminder
  function addReminder() {
    const name = medNameInput.value.trim();
    const dosage = medDosageInput.value.trim();
    const time = reminderTimeInput.value;
    
    if (!name || !dosage || !time) {
      alert('Please fill in all fields');
      return;
    }
    
    const newReminder = {
      id: 'rem-' + Date.now(),
      name,
      dosage,
      time,
      enabled: true
    };
    
    reminders.push(newReminder);
    saveReminders();
    
    // Clear form
    medNameInput.value = '';
    medDosageInput.value = '';
    reminderTimeInput.value = '08:00';
    
    // Update UI
    renderReminders();
    
    // Schedule notifications
    scheduleReminderNotifications();
  }
  
  // Edit reminder
  function editReminder(id) {
    const reminder = reminders.find(r => r.id === id);
    if (!reminder) return;
    
    // Fill the form with reminder data
    medNameInput.value = reminder.name;
    medDosageInput.value = reminder.dosage;
    reminderTimeInput.value = reminder.time;
    
    // Remove the old reminder
    deleteReminder(id, false);
    
    // Focus on the name field
    medNameInput.focus();
  }
  
  // Delete reminder
  function deleteReminder(id, save = true) {
    if (confirm('Are you sure you want to delete this reminder?')) {
      reminders = reminders.filter(r => r.id !== id);
      if (save) {
        saveReminders();
        renderReminders();
      }
    }
  }
  
  // Save reminders to localStorage
  function saveReminders() {
    localStorage.setItem('medicationReminders', JSON.stringify(reminders));
  }
  
  // Schedule notifications for all reminders
  function scheduleReminderNotifications() {
    // Clear existing notifications
    if ('serviceWorker' in navigator && 'Notification' in window && Notification.permission === 'granted') {
      navigator.serviceWorker.getRegistration().then(registration => {
        if (registration) {
          registration.getNotifications().then(notifications => {
            notifications.forEach(notification => notification.close());
          });
        }
      });
    }
    
    // Schedule new notifications
    if (prefs?.notifications?.enabled) {
      reminders.forEach(reminder => {
        if (reminder.enabled) {
          scheduleSingleReminder(reminder);
        }
      });
    }
  }
  
  // Schedule a single reminder
  function scheduleSingleReminder(reminder) {
    const [hours, minutes] = reminder.time.split(':').map(Number);
    const now = new Date();
    const reminderTime = new Date();
    
    // Set the reminder time for today
    reminderTime.setHours(hours, minutes, 0, 0);
    
    // If the time has already passed today, schedule for tomorrow
    if (reminderTime <= now) {
      reminderTime.setDate(reminderTime.getDate() + 1);
    }
    
    // Calculate time until reminder (in ms)
    const timeUntilReminder = reminderTime - now;
    
    // Only schedule if it's in the future
    if (timeUntilReminder > 0) {
      setTimeout(() => {
        showReminderNotification(reminder);
        
        // Schedule the next occurrence for tomorrow
        scheduleSingleReminder(reminder);
      }, timeUntilReminder);
    }
  }
  
  // Show notification for a reminder
  function showReminderNotification(reminder) {
    if (!prefs?.notifications?.enabled) return;
    
    requestNotificationPermission().then(hasPermission => {
      if (!hasPermission) return;
      
      const options = {
        body: `Time to take ${reminder.name} (${reminder.dosage})`,
        icon: '/path/to/icon.png',
        tag: `med-reminder-${reminder.id}`,
        requireInteraction: true,
        actions: [
          { action: 'snooze', title: 'Snooze 5 min' },
          { action: 'taken', title: 'Mark as Taken' }
        ]
      };
      
      const notification = new Notification(`💊 ${reminder.name} Reminder`, options);
      
      notification.onclick = () => {
        window.focus();
        notification.close();
      };
      
      notification.onaction = (event) => {
        if (event.action === 'taken') {
          // Log the medication as taken
          logMedicationIntake(reminder, 'taken');
        } else if (event.action === 'snooze') {
          // Snooze for the configured duration
          const snoozeMs = (prefs.notifications.snoozeDuration || 5) * 60 * 1000;
          setTimeout(() => {
            showReminderNotification(reminder);
          }, snoozeMs);
        }
      };
      
      // Auto-close after 5 minutes if not interacted with
      setTimeout(() => {
        notification.close();
      }, 5 * 60 * 1000);
      
      // Play sound if enabled
      if (prefs.notifications.sound) {
        const audio = new Audio('/path/to/notification-sound.mp3');
        audio.play().catch(e => console.log('Could not play sound:', e));
      }
    });
  }
  
  // Log medication intake
  function logMedicationIntake(reminder, status) {
    const logEntry = {
      id: 'log-' + Date.now(),
      reminderId: reminder.id,
      name: reminder.name,
      dosage: reminder.dosage,
      time: new Date().toISOString(),
      status: status // 'taken' or 'missed'
    };
    
    // Save to logs
    const logs = JSON.parse(localStorage.getItem('medicationLogs') || '[]');
    logs.push(logEntry);
    localStorage.setItem('medicationLogs', JSON.stringify(logs));
    
    // Update dashboard if it's open
    if (typeof updateDashboard === 'function') {
      updateDashboard();
    }
  }
  
  // Event listeners
  addReminderBtn.addEventListener('click', addReminder);
  
  // Allow submitting with Enter key
  [medNameInput, medDosageInput, reminderTimeInput].forEach(input => {
    input.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        addReminder();
      }
    });
  });
  
  // Initial render
  renderReminders();
  
  // Initial schedule of notifications
  if (prefs?.notifications?.enabled) {
    scheduleReminderNotifications();
  }
});
</script>

<?php include __DIR__.'/includes/footer.php'; ?>
