<?php include __DIR__.'/includes/header.php'; ?>

<div class="container">
  <div class="calendar-header">
    <h2>Medication Schedule</h2>
    <div class="calendar-navigation">
      <button id="prevMonth" class="btn btn-icon">
        <i class="fas fa-chevron-left"></i>
      </button>
      <h3 id="currentMonth">September 2023</h3>
      <button id="nextMonth" class="btn btn-icon">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
    <div class="view-options">
      <button class="btn btn-outline active" data-view="month">Month</button>
      <button class="btn btn-outline" data-view="week">Week</button>
      <button class="btn btn-outline" data-view="day">Day</button>
    </div>
  </div>

  <div class="calendar-view" id="calendarView">
    <!-- Calendar will be rendered here by JavaScript -->
  </div>

  <!-- Event Details Modal -->
  <div id="eventModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Medication Details</h3>
        <span class="close">&times;</span>
      </div>
      <div class="modal-body">
        <div id="eventDetails">
          <!-- Event details will be populated here -->
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-ghost" id="closeModal">Close</button>
        <button class="btn btn-primary" id="markTaken">Mark as Taken</button>
      </div>
    </div>
  </div>
</div>

<style>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem;
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.calendar-navigation {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.view-options {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  padding: 0.5rem;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.calendar-view {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

/* Calendar Grid */
.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 1px;
  background: #e0e0e0;
}

.calendar-header-day {
  background: #f5f5f5;
  padding: 0.75rem;
  text-align: center;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.8rem;
  color: #555;
}

.calendar-day {
  min-height: 100px;
  background: white;
  padding: 0.5rem;
  position: relative;
  border: 1px solid #eee;
}

.calendar-day.today {
  background-color: #f0f7ff;
  border-left: 3px solid #4a90e2;
}

.calendar-day.other-month {
  background-color: #f9f9f9;
  color: #999;
}

.day-number {
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.event {
  font-size: 0.8rem;
  background: #e3f2fd;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  margin-bottom: 0.25rem;
  cursor: pointer;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.event.taken {
  background: #e8f5e9;
  text-decoration: line-through;
  opacity: 0.7;
}

/* Modal Styles */
.modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1000;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.modal-header {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header .close {
  font-size: 1.5rem;
  cursor: pointer;
  color: #666;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid #eee;
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
}

@media (max-width: 768px) {
  .calendar-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .calendar-navigation {
    width: 100%;
    justify-content: space-between;
    margin: 0.5rem 0;
  }
  
  .view-options {
    width: 100%;
    justify-content: center;
  }
  
  .calendar-day {
    min-height: 80px;
  }
  
  .event {
    font-size: 0.7rem;
    padding: 0.15rem 0.3rem;
  }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize calendar with current date
  let currentDate = new Date();
  let currentView = 'month';
  
  // Sample medication data (replace with actual data from your backend)
  const medications = [
    { id: 1, name: 'Aspirin', dosage: '100mg', time: '08:00', frequency: 'daily', startDate: '2023-09-01', endDate: '2023-12-31' },
    { id: 2, name: 'Vitamin D', dosage: '1000IU', time: '12:00', frequency: 'daily', startDate: '2023-09-01', endDate: '2023-12-31' },
    { id: 3, name: 'Antihistamine', dosage: '10mg', time: '20:00', frequency: 'as needed', startDate: '2023-09-15', endDate: '2023-09-30' },
  ];
  
  // Track which medications have been taken
  const takenMeds = JSON.parse(localStorage.getItem('takenMeds') || '{}');
  
  // DOM Elements
  const calendarView = document.getElementById('calendarView');
  const currentMonthElement = document.getElementById('currentMonth');
  const prevMonthBtn = document.getElementById('prevMonth');
  const nextMonthBtn = document.getElementById('nextMonth');
  const viewButtons = document.querySelectorAll('.view-options button');
  const modal = document.getElementById('eventModal');
  const closeModal = document.querySelector('.close');
  const closeModalBtn = document.getElementById('closeModal');
  const markTakenBtn = document.getElementById('markTaken');
  const eventDetails = document.getElementById('eventDetails');
  
  let selectedEvent = null;
  
  // Event Listeners
  prevMonthBtn.addEventListener('click', () => {
    if (currentView === 'month') {
      currentDate.setMonth(currentDate.getMonth() - 1);
    } else if (currentView === 'week') {
      currentDate.setDate(currentDate.getDate() - 7);
    } else {
      currentDate.setDate(currentDate.getDate() - 1);
    }
    renderCalendar();
  });
  
  nextMonthBtn.addEventListener('click', () => {
    if (currentView === 'month') {
      currentDate.setMonth(currentDate.getMonth() + 1);
    } else if (currentView === 'week') {
      currentDate.setDate(currentDate.getDate() + 7);
    } else {
      currentDate.setDate(currentDate.getDate() + 1);
    }
    renderCalendar();
  });
  
  viewButtons.forEach(button => {
    button.addEventListener('click', () => {
      viewButtons.forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');
      currentView = button.dataset.view;
      renderCalendar();
    });
  });
  
  // Modal event listeners
  if (closeModal) closeModal.onclick = () => modal.style.display = 'none';
  if (closeModalBtn) closeModalBtn.onclick = () => modal.style.display = 'none';
  if (markTakenBtn) {
    markTakenBtn.onclick = () => {
      if (selectedEvent) {
        const eventId = selectedEvent.id + '-' + formatDate(selectedEvent.date);
        takenMeds[eventId] = !takenMeds[eventId];
        localStorage.setItem('takenMeds', JSON.stringify(takenMeds));
        renderCalendar();
        modal.style.display = 'none';
      }
    };
  }
  
  // Close modal when clicking outside
  window.onclick = (event) => {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  };
  
  // Helper Functions
  function formatDate(date) {
    const d = new Date(date);
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
  }
  
  function getEventsForDate(date) {
    const dateStr = formatDate(date);
    const dayOfWeek = date.getDay();
    const dayOfMonth = date.getDate();
    
    return medications.filter(med => {
      const medStart = new Date(med.startDate);
      const medEnd = med.endDate ? new Date(med.endDate) : new Date('2100-01-01');
      
      // Check if date is within medication date range
      if (date < medStart || date > medEnd) return false;
      
      // Check frequency
      if (med.frequency === 'daily') return true;
      if (med.frequency === 'weekdays' && dayOfWeek > 0 && dayOfWeek < 6) return true;
      if (med.frequency === 'weekends' && (dayOfWeek === 0 || dayOfWeek === 6)) return true;
      if (med.frequency === 'as needed') return true;
      
      // Add more frequency checks as needed
      
      return false;
    }).map(med => ({
      id: med.id,
      title: `${med.name} (${med.dosage})`,
      time: med.time,
      date: date,
      isTaken: takenMeds[`${med.id}-${dateStr}`] || false,
      medication: med
    }));
  }
  
  function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Update month/year display
    currentMonthElement.textContent = currentDate.toLocaleString('default', { month: 'long', year: 'numeric' });
    
    if (currentView === 'month') {
      renderMonthView(year, month);
    } else if (currentView === 'week') {
      renderWeekView();
    } else {
      renderDayView();
    }
  }
  
  function renderMonthView(year, month) {
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDay = firstDay.getDay();
    const daysInMonth = lastDay.getDate();
    const daysInLastMonth = new Date(year, month, 0).getDate();
    
    let calendarHTML = `
      <div class="calendar-grid">
        <div class="calendar-header-day">Sun</div>
        <div class="calendar-header-day">Mon</div>
        <div class="calendar-header-day">Tue</div>
        <div class="calendar-header-day">Wed</div>
        <div class="calendar-header-day">Thu</div>
        <div class="calendar-header-day">Fri</div>
        <div class="calendar-header-day">Sat</div>
    `;
    
    // Previous month days
    for (let i = startDay - 1; i >= 0; i--) {
      const day = daysInLastMonth - i;
      const date = new Date(year, month - 1, day);
      const events = getEventsForDate(date);
      
      calendarHTML += `
        <div class="calendar-day other-month">
          <div class="day-number">${day}</div>
          ${renderEvents(events, true)}
        </div>
      `;
    }
    
    // Current month days
    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
      const date = new Date(year, month, day);
      const isToday = date.toDateString() === today.toDateString();
      const events = getEventsForDate(date);
      
      calendarHTML += `
        <div class="calendar-day ${isToday ? 'today' : ''}">
          <div class="day-number">${day}</div>
          ${renderEvents(events)}
        </div>
      `;
    }
    
    // Next month days
    const daysToAdd = 42 - (daysInMonth + startDay); // 6 rows of 7 days
    for (let i = 1; i <= daysToAdd; i++) {
      const date = new Date(year, month + 1, i);
      const events = getEventsForDate(date);
      
      calendarHTML += `
        <div class="calendar-day other-month">
          <div class="day-number">${i}</div>
          ${renderEvents(events, true)}
        </div>
      `;
    }
    
    calendarHTML += '</div>';
    calendarView.innerHTML = calendarHTML;
    
    // Add event listeners to events
    document.querySelectorAll('.event').forEach(eventEl => {
      eventEl.addEventListener('click', (e) => {
        e.stopPropagation();
        const eventId = eventEl.dataset.eventId;
        const eventDate = eventEl.dataset.eventDate;
        const event = medications.find(m => m.id === parseInt(eventId));
        
        if (event) {
          selectedEvent = {
            ...event,
            date: new Date(eventDate),
            isTaken: eventEl.classList.contains('taken')
          };
          showEventDetails(selectedEvent);
        }
      });
    });
  }
  
  function renderWeekView() {
    // Implementation for week view
    calendarView.innerHTML = '<div style="padding: 1rem;">Week view coming soon</div>';
  }
  
  function renderDayView() {
    // Implementation for day view
    calendarView.innerHTML = '<div style="padding: 1rem;">Day view coming soon</div>';
  }
  
  function renderEvents(events, isOtherMonth = false) {
    if (events.length === 0) return '';
    
    return events.map(event => {
      const isTaken = event.isTaken;
      const opacity = isOtherMonth ? 'opacity-50' : '';
      
      return `
        <div 
          class="event ${isTaken ? 'taken' : ''} ${opacity}" 
          data-event-id="${event.id}" 
          data-event-date="${formatDate(event.date)}"
        >
          ${event.time} - ${event.title}
        </div>
      `;
    }).join('');
  }
  
  function showEventDetails(event) {
    if (!eventDetails) return;
    
    const formattedDate = event.date.toLocaleDateString('en-US', { 
      weekday: 'long', 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
    
    eventDetails.innerHTML = `
      <h4>${event.medication.name}</h4>
      <p><strong>Dosage:</strong> ${event.medication.dosage}</p>
      <p><strong>Time:</strong> ${event.medication.time}</p>
      <p><strong>Date:</strong> ${formattedDate}</p>
      <p><strong>Status:</strong> ${event.isTaken ? 'Taken' : 'Not taken'}</p>
      <div class="mt-2">
        <label class="checkbox-container">
          <input type="checkbox" ${event.isTaken ? 'checked' : ''} disabled>
          Mark as taken
        </label>
      </div>
    `;
    
    if (markTakenBtn) {
      markTakenBtn.textContent = event.isTaken ? 'Mark as Not Taken' : 'Mark as Taken';
      markTakenBtn.classList.toggle('btn-secondary', event.isTaken);
      markTakenBtn.classList.toggle('btn-primary', !event.isTaken);
    }
    
    modal.style.display = 'flex';
  }
  
  // Initial render
  renderCalendar();
});
</script>

<?php include __DIR__.'/includes/footer.php'; ?>
