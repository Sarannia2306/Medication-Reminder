<?php include __DIR__.'/includes/header.php'; ?>
<section class="card page-header">
  <div class="row" style="justify-content:space-between; align-items:center">
    <h2 class="no-margin">Medication List</h2>
    <a class="btn btn-primary" href="add_medication.php">Add Medication</a>
  </div>
  <hr class="sep" />
  <div id="medList" class="list" aria-live="polite">
    <!-- Medications will be loaded here by JavaScript -->
  </div>
  <p class="helper">Use Edit to change details, or Delete to remove a medication.</p>
</section>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal" style="display: none;">
  <div class="modal-content">
    <h3>Confirm Deletion</h3>
    <p>Are you sure you want to delete this medication? This action cannot be undone.</p>
    <div class="modal-actions">
      <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
      <button id="cancelDeleteBtn" class="btn btn-ghost">Cancel</button>
    </div>
  </div>
</div>

<script>
document.body.setAttribute('data-page','list');

document.addEventListener('DOMContentLoaded', function() {
  // Load medications from localStorage
  const meds = JSON.parse(localStorage.getItem('meditrack:medications') || '[]');
  const medList = document.getElementById('medList');
  
  // Handle delete confirmation
  let medicationToDelete = null;
  const confirmModal = document.getElementById('confirmModal');
  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
  const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
  
  // Function to get patient name by ID
  function getPatientName(patientId) {
    const pts = JSON.parse(localStorage.getItem('meditrack:patients') || '[]');
    const prefs = JSON.parse(localStorage.getItem('meditrack:prefs') || '{}');
    
    // If no patient ID is provided, use the current active patient
    if (!patientId) {
      if (prefs.activePatientId) {
        const patient = pts.find(p => p.id === prefs.activePatientId);
        return patient ? patient.name : 'Unknown Patient';
      }
      return prefs.activePatient === 'patientB' ? 'Patient B' : 'Patient A';
    }
    
    // If patient ID is provided, find the patient
    const patient = pts.find(p => p.id === patientId);
    return patient ? patient.name : 'Unknown Patient';
  }

  // Function to render the medication list
  function renderMedications() {
    const meds = JSON.parse(localStorage.getItem('meditrack:medications') || '[]');
    
    if (meds.length === 0) {
      medList.innerHTML = `
        <div class="empty-state">
          <i class="bi bi-capsule-pill" style="font-size: 2.5rem; color: #6c757d; margin-bottom: 1rem;"></i>
          <h3>No Medications Found</h3>
          <p>You haven't added any medications yet.</p>
          <a href="add_medication.php" class="btn btn-primary">Add Your First Medication</a>
        </div>
      `;
      return;
    }
    
    let html = '';
    
    meds.forEach(med => {
      const nextDose = med.times && med.times.length > 0 ? med.times[0] : 'Not set';
      
      html += `
        <div class="medication-item" data-id="${med.id}">
            </div>
          </div>
          <div class="medication-actions">
            <a href="add_medication.php?edit=${med.id}" class="btn btn-ghost">
              <i class="bi bi-pencil"></i> Edit
            </a>
            <button class="btn btn-danger delete-btn" data-id="${med.id}">
              <i class="bi bi-trash"></i> Delete
            </button>
          </div>
        </div>
        <div class="medication-details">
          <div class="medication-dosage">
            <i class="bi bi-capsule"></i> ${med.dosage}
          </div>
          <div class="medication-time">
            <i class="bi bi-clock"></i> ${med.times ? med.times.join(', ') : 'No time set'}
          </div>
          <div class="medication-frequency">
            <i class="bi bi-arrow-repeat"></i> ${med.frequency || 'Once Daily'}
          </div>
          <div class="medication-meal">
            <i class="bi bi-${med.meal === 'With Meal' ? 'cup-straw' : 'egg-fried'}"></i> 
            ${med.meal || 'Before Meal'}
          </div>
        </div>
      </div>
    `).join('');
    
    // Add event listeners to delete buttons
    document.querySelectorAll('.delete-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        medicationToDelete = id;
        confirmModal.style.display = 'flex';
      });
    });
  }
  
  // Handle delete confirmation
  if (confirmDeleteBtn) {
    confirmDeleteBtn.addEventListener('click', function() {
      if (medicationToDelete) {
        deleteMedication(medicationToDelete);
        confirmModal.style.display = 'none';
        medicationToDelete = null;
      }
    });
  }
  
  // Handle cancel delete
  if (cancelDeleteBtn) {
    cancelDeleteBtn.addEventListener('click', function() {
      confirmModal.style.display = 'none';
      medicationToDelete = null;
    });
  }
  
  // Close modal when clicking outside
  window.addEventListener('click', function(event) {
    if (event.target === confirmModal) {
      confirmModal.style.display = 'none';
      medicationToDelete = null;
    }
  });
  
  // Function to delete a medication
  function deleteMedication(id) {
    const meds = JSON.parse(localStorage.getItem('meditrack:medications') || '[]');
    const updatedMeds = meds.filter(med => med.id !== id);
    localStorage.setItem('meditrack:medications', JSON.stringify(updatedMeds));
    renderMedications();
  }
  
  // Initial render
  renderMedications();
});
</script>

<style>
.patient-tag {
  display: inline-flex;
  align-items: center;
  background: #f0f4f8;
  border-radius: 12px;
  padding: 2px 8px 2px 4px;
  font-size: 0.8rem;
  color: #4a5568;
  margin-top: 4px;
}

.patient-tag i {
  margin-right: 4px;
  font-size: 0.9em;
  color: #4a5568;
}

.medication-item {
  background: white;
  border-radius: 8px;
  padding: 1.25rem;
  margin-bottom: 1rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.medication-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
}

.medication-header > div:first-child {
  flex: 1;
}

.medication-info h3 {
  margin: 0 0 0.5rem 0;
  color: #333;
  font-size: 1.25rem;
}

.dosage {
  color: #666;
  margin: 0 0 0.75rem 0;
  font-weight: 500;
}

.details {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  color: #666;
  font-size: 0.9rem;
}

.details span {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}

.medication-actions {
  display: flex;
  gap: 0.5rem;
  flex-shrink: 0;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.2s;
  border: 1px solid transparent;
}

.btn-ghost {
  background: #f8f9fa;
  color: #333;
  border-color: #dee2e6;
}

.btn-ghost:hover {
  background: #e9ecef;
}

.btn-danger {
  background: #dc3545;
  color: white;
  border-color: #dc3545;
}

.btn-danger:hover {
  background: #c82333;
  border-color: #bd2130;
}

.btn-primary {
  background: #0d6efd;
  color: white;
  border-color: #0d6efd;
}

.btn-primary:hover {
  background: #0b5ed7;
  border-color: #0a58ca;
}

.empty-state {
  text-align: center;
  padding: 2rem;
  color: #6c757d;
}

.empty-state h3 {
  margin: 0.5rem 0;
  color: #333;
}

.empty-state p {
  margin-bottom: 1.5rem;
}

/* Modal styles */
.modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  max-width: 500px;
  width: 90%;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal h3 {
  margin-top: 0;
  color: #333;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .medication-item {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .medication-actions {
    width: 100%;
    justify-content: flex-end;
  }
  
  .details {
    flex-direction: column;
    gap: 0.5rem;
  }
}
</style>

<?php include __DIR__.'/includes/footer.php'; ?>
