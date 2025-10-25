<?php include __DIR__.'/includes/header.php'; ?>
<section class="card page-header">
  <div class="row" style="align-items:center; justify-content:space-between">
    <h2 id="formTitle" class="no-margin">Add Medication</h2>
    <div id="patientInfo" class="patient-info">
      <i class="bi bi-person-fill"></i>
      <span id="patientName">Loading patient...</span>
    </div>
  </div>
  <form id="medForm" class="grid" novalidate>
    <input type="hidden" id="medId" name="id" value="">
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
        <select id="frequency" name="frequency" class="input" required>
          <option value="Once Daily">Once Daily</option>
          <option value="Twice Daily">Twice Daily</option>
          <option value="Thrice Daily">Thrice Daily</option>
          <option value="Every Morning">Every Morning</option>
          <option value="Every Afternoon">Every Afternoon</option>
          <option value="Every Night">Every Night</option>
        </select>
      </div>
    </div>
    <div>
      <label class="label" for="meal">Timing (Meal Relation)</label>
      <select id="meal" name="meal" class="input" required>
        <option value="Before Meal">Before Meal</option>
        <option value="After Meal">After Meal</option>
        <option value="With Meal">With Meal</option>
      </select>
    </div>
    <div class="row mt-1">
      <button id="submitBtn" class="btn btn-primary" type="submit">Add Medication</button>
      <a class="btn btn-ghost" href="medication_list.php">Cancel</a>
    </div>
  </form>
</section>
<script>
  document.body.setAttribute('data-page','add');
  
  // Display current patient information
  function updatePatientInfo() {
    const pts = JSON.parse(localStorage.getItem('meditrack:patients') || '[]');
    const prefs = JSON.parse(localStorage.getItem('meditrack:prefs') || '{}');
    let patientName = 'Unknown Patient';
    
    if (prefs.activePatientId) {
      const patient = pts.find(p => p.id === prefs.activePatientId);
      if (patient) {
        patientName = patient.name;
      }
    } else {
      patientName = prefs.activePatient === 'patientB' ? 'Patient B' : 'Patient A';
    }
    
    document.getElementById('patientName').textContent = patientName;
  }
  
  // Update patient info on page load
  updatePatientInfo();
  
  // Check if we're editing an existing medication
  document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const editId = urlParams.get('edit');
    
    if (editId) {
      // Load medication data for editing
      const meds = JSON.parse(localStorage.getItem('meditrack:medications') || '[]');
      const medToEdit = meds.find(med => med.id === editId);
      
      if (medToEdit) {
        // Update form title
        document.getElementById('formTitle').textContent = 'Edit Medication';
        document.getElementById('submitBtn').textContent = 'Update Medication';
        
        // Fill in the form with medication data
        document.getElementById('medId').value = medToEdit.id;
        document.getElementById('name').value = medToEdit.name || '';
        document.getElementById('dosage').value = medToEdit.dosage || '';
        
        // Set time (take the first time if multiple exist)
        if (medToEdit.times && medToEdit.times.length > 0) {
          // Convert stored time to 24-hour format if needed
          let storedTime = medToEdit.times[0] || '';
          // Ensure proper time format (HH:MM)
          if (storedTime && storedTime.includes(' ')) {
            // Handle AM/PM format if present
            const [timePart, period] = storedTime.split(' ');
            let [hours, minutes] = timePart.split(':');
            hours = parseInt(hours, 10);
            if (period === 'PM' && hours < 12) hours += 12;
            if (period === 'AM' && hours === 12) hours = 0;
            storedTime = `${String(hours).padStart(2, '0')}:${minutes}`;
          }
          document.getElementById('time').value = storedTime;
        }
        
        // Set frequency and meal timing
        if (medToEdit.frequency) {
          document.getElementById('frequency').value = medToEdit.frequency;
        }
        if (medToEdit.meal) {
          document.getElementById('meal').value = medToEdit.meal;
        }
      }
    }
    
    // Handle form submission
    const form = document.getElementById('medForm');
    if (form) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = {
          id: document.getElementById('medId').value || crypto?.randomUUID?.() || 'id-' + Math.random().toString(36).slice(2),
          name: document.getElementById('name').value.trim(),
          dosage: document.getElementById('dosage').value.trim(),
          time: document.getElementById('time').value,
          frequency: document.getElementById('frequency').value,
          meal: document.getElementById('meal').value,
          times: [],
          history: []
        };
        
        // Calculate times based on frequency
        const baseTime = formData.time;
        if (baseTime) {
          // Ensure baseTime is in HH:MM format
          let [hours, minutes] = baseTime.split(':');
          hours = parseInt(hours, 10);
          minutes = parseInt(minutes, 10);
          
          // Create a date object for today with the specified time
          const now = new Date();
          const baseDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), hours, minutes, 0, 0);
          
          switch(formData.frequency) {
            case 'Twice Daily':
              const secondDose = new Date(baseDate);
              secondDose.setHours(baseDate.getHours() + 12);
              
              formData.times = [
                `${String(baseDate.getHours()).padStart(2, '0')}:${String(baseDate.getMinutes()).padStart(2, '0')}`,
                `${String(secondDose.getHours()).padStart(2, '0')}:${String(secondDose.getMinutes()).padStart(2, '0')}`
              ];
              break;
              
            case 'Thrice Daily':
              const second = new Date(baseDate);
              second.setHours(baseDate.getHours() + 8);
              
              const third = new Date(second);
              third.setHours(second.getHours() + 8);
              
              formData.times = [
                `${String(baseDate.getHours()).padStart(2, '0')}:${String(baseDate.getMinutes()).padStart(2, '0')}`,
                `${String(second.getHours()).padStart(2, '0')}:${String(second.getMinutes()).padStart(2, '0')}`,
                `${String(third.getHours()).padStart(2, '0')}:${String(third.getMinutes()).padStart(2, '0')}`
              ];
              break;
              
            case 'Every Morning':
              formData.times = ['08:00'];
              break;
              
            case 'Every Afternoon':
              formData.times = ['13:00'];
              break;
              
            case 'Every Night':
              formData.times = ['20:00'];
              break;
              
            default: // Once Daily or any other case
              formData.times = [baseTime];
          }
        }
        
        // Get existing medications
        const meds = JSON.parse(localStorage.getItem('meditrack:medications') || '[]');
        
        // Check if we're updating an existing medication
        const existingIndex = meds.findIndex(med => med.id === formData.id);
        
        if (existingIndex >= 0) {
          // Update existing medication
          meds[existingIndex] = { ...meds[existingIndex], ...formData };
        } else {
          // Add new medication
          meds.push(formData);
        }
        
        // Save back to localStorage
        localStorage.setItem('meditrack:medications', JSON.stringify(meds));
        
        // Show success message and redirect
        alert('Medication saved successfully!');
        window.location.href = 'medication_list.php';
      });
    }
  });
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
