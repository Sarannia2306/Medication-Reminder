<?php include __DIR__.'/includes/header.php'; ?>
<section class="card page-header">
  <div class="row" style="align-items:center; justify-content:space-between">
    <div class="row" style="align-items:center; gap: 1rem;">
      <h2 class="no-margin">Medication List</h2>
      <span id="patientBadge" class="status upcoming" aria-live="polite"></span>
    </div>
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
  // Update patient badge
  const badge = document.getElementById('patientBadge');
  if(badge) {
    const prefs = JSON.parse(localStorage.getItem('meditrack:prefs') || '{}');
    const patients = JSON.parse(localStorage.getItem('meditrack:patients') || '[]');
    let label = 'Patient';
    
    if (prefs.activePatientId) {
      const patient = patients.find(p => p.id === prefs.activePatientId);
      if (patient) label = patient.name;
    } else {
      label = prefs.activePatient === 'patientB' ? 'Patient B' : 'Patient A';
    }
    
    badge.textContent = `Active: ${label}`;
  }
  // Load medications from localStorage
  const meds = JSON.parse(localStorage.getItem('meditrack:medications') || '[]');
  const medList = document.getElementById('medList');
  
  // Handle delete confirmation
  let medicationToDelete = null;
  const confirmModal = document.getElementById('confirmModal');
  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
  const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
  
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
        <div class="medication-item">
          <div class="medication-info">
            <div style="display: flex; align-items: center; gap: 8px;">
              <h3>${med.name || 'Unnamed Medication'}</h3>
              <span class="dosage">${med.dosage ? (med.dosage.match(/\d+mg/i) ? med.dosage : `${med.dosage}mg`) : 'No dosage'}</span>
            </div>
            <div class="details">
              <span><i class="bi bi-clock"></i> ${nextDose}</span>
              <span><i class="bi bi-arrow-repeat"></i> ${med.frequency || 'Once Daily'}</span>
              <span><i class="bi bi-${med.meal === 'With Meal' ? 'cup' : 'egg'}"></i> ${med.meal || 'No meal timing'}</span>
            </div>
          </div>
          <div class="medication-actions">
            <a href="add_medication.php?edit=${med.id}" class="btn btn-ghost" style="min-width: 100px;">
              <i class="bi bi-pencil"></i> Edit
            </a>
            <button class="btn btn-danger delete-btn" data-id="${med.id}" style="min-width: 110px;">
              <i class="bi bi-trash"></i> Delete
            </button>
          </div>
        </div>
      `;
    });
    
    medList.innerHTML = html;
    
    // Event delegation is now handled at the document level
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
    if (!id) return;
    const meds = JSON.parse(localStorage.getItem('meditrack:medications') || '[]');
    const updatedMeds = meds.filter(med => med.id !== id);
    localStorage.setItem('meditrack:medications', JSON.stringify(updatedMeds));
    renderMedications();
  }
  
  // Handle delete button clicks using event delegation
  document.addEventListener('click', function(e) {
    if (e.target.closest('.delete-btn')) {
      const button = e.target.closest('.delete-btn');
      medicationToDelete = button.getAttribute('data-id');
      confirmModal.style.display = 'flex';
    }
  });
  
  // Initial render
  renderMedications();
});
</script>

<style>
  .medication-item {
    background: #ffffff;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
    border: 1px solid #e0e0e0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
  }

  .medication-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .medication-item h3 {
    margin: 0;
    color: #212121;
    font-size: 16px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    line-height: 1.4;
  }

  .dosage {
    color: #616161;
    font-size: 14px;
    font-weight: 400;
    background: #f5f5f5;
    padding: 2px 8px;
    border-radius: 10px;
    display: inline-block;
  }

  .medication-item .details {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    color: #757575;
    font-size: 14px;
  }

  .details span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #f8f9fa;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 13px;
    color: #5f6368;
    border: 1px solid #e0e0e0;
  }

  .medication-actions {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    border: none;
    border-radius: 12px;
    padding: 12px 16px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: var(--shadow);
    font-size: 14px;
    min-width: 100px;
    text-align: center;
    transition: all 0.2s ease;
    text-decoration: none;
  }

  .btn-ghost {
    background: transparent;
    color: var(--primary);
    border: 2px solid var(--primary);
  }

  .btn-ghost:hover {
    background: rgba(33, 150, 243, 0.1);
  }

  .btn-danger {
    background: var(--danger);
    color: white;
  }

  .btn-danger:hover {
    background: #e53935;
  }

  .btn-primary {
    background: var(--primary);
    color: white;
  }

  .btn-primary:hover {
    background: var(--primary-600);
  }

  .empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #9e9e9e;
    background: #fafafa;
    border-radius: 12px;
    border: 2px dashed #e0e0e0;
  }

  .empty-state h3 {
    margin: 16px 0 8px;
    color: #424242;
    font-size: 18px;
    font-weight: 600;
  }

  .empty-state p {
    margin: 0 0 20px;
    color: #757575;
    font-size: 14px;
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
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
      margin-top: 12px;
      padding-top: 12px;
      border-top: 1px solid #f0f0f0;
    }
    
    .btn {
      flex: 1;
      text-align: center;
    }
    .medication-item {
      position: relative;
      overflow: hidden;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .medication-item:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .medication-actions {
      width: 100%;
      justify-content: flex-end;
    }

    .details i {
      font-size: 14px;
      color: #757575;
    }
  }
</style>

<?php include __DIR__.'/includes/footer.php'; ?>
