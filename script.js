/*
  MediTrack front-end prototype
  - Uses localStorage to simulate data persistence.
  - Pages identify themselves with body data-page attr.
  - Provides: add/edit/delete, reminders, mark taken/missed, caregiver summary, theme & accessibility.
*/
(() => {
  const BASE_STORE_KEY = 'meditrack:medications';
  const PREF_KEY = 'meditrack:prefs';
  const PATIENTS_KEY = 'meditrack:patients';
  const CAREGIVER_SESSION = 'meditrack:caregiver_session';
  const CAREGIVER_PROFILE = 'meditrack:caregiver_profile';
  const TODAY = () => new Date();

  // --- Utilities ---
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const pad = n => String(n).padStart(2, '0');
  const toTimeStr = (d) => `${pad(d.getHours())}:${pad(d.getMinutes())}`;
  const parseTime = (hhmm) => { const [h, m] = hhmm.split(':').map(Number); const d = new Date(); d.setHours(h, m, 0, 0); return d; };
  const uuid = () => crypto?.randomUUID?.() || 'id-' + Math.random().toString(36).slice(2);

  function setFieldError(input, message){
    if(!input) return;
    clearFieldError(input);
    input.classList.add('invalid');
    const err = document.createElement('div');
    err.className = 'field-error';
    err.textContent = message;
    input.insertAdjacentElement('afterend', err);
  }
  function clearFieldError(input){
    if(!input) return;
    input.classList.remove('invalid');
    const next = input.nextElementSibling;
    if(next && next.classList.contains('field-error')) next.remove();
  }
  function clearFormErrors(form){
    if(!form) return;
    Array.from(form.querySelectorAll('.invalid')).forEach(el=> el.classList.remove('invalid'));
    Array.from(form.querySelectorAll('.field-error')).forEach(el=> el.remove());
  }
  function validateEmailFormat(email){
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  // --- State ---
  let prefs = load(PREF_KEY, { 
    theme: 'light', 
    fontScale: 1, 
    activePatient: 'patientA', 
    activePatientId: null,
    notifications: {
      enabled: true,
      sound: true,
      reminderBefore: 15, // minutes before to remind
      morningTime: '08:00',
      afternoonTime: '13:00',
      eveningTime: '20:00',
      snoozeDuration: 5 // minutes
    }
  });
  
  // Track active notifications
  let activeNotifications = [];
  let reminderTimeouts = [];
  function getStoreKey(){
    // Prefer new model using activePatientId, fallback to old A/B for backward compatibility
    const pid = prefs?.activePatientId;
    if(pid){ return `${BASE_STORE_KEY}_${pid}`; }
    const legacy = prefs?.activePatient || 'patientA';
    return `${BASE_STORE_KEY}_${legacy}`;
  }

  // --- Notification Functions ---
  function requestNotificationPermission() {
    if (!('Notification' in window)) {
      console.log('This browser does not support desktop notifications');
      return Promise.resolve(false);
    }
    
    if (Notification.permission === 'granted') {
      return Promise.resolve(true);
    }
    
    if (Notification.permission !== 'denied') {
      return Notification.requestPermission().then(permission => {
        return permission === 'granted';
      });
    }
    
    return Promise.resolve(false);
  }
  
  function scheduleMedicationReminders() {
    // Clear any existing reminders
    clearAllReminders();
    
    if (!prefs.notifications.enabled) return;
    
    const now = new Date();
    const meds = load(getStoreKey(), []);
    
    meds.forEach(med => {
      if (!med.times || med.times.length === 0) return;
      
      med.times.forEach(timeStr => {
        if (!timeStr) return;
        
        // Parse the time (format: "HH:MM")
        const [hours, minutes] = timeStr.split(':').map(Number);
        
        // Create a date for today with the medication time
        const medTime = new Date();
        medTime.setHours(hours, minutes, 0, 0);
        
        // If the time has already passed today, schedule for tomorrow
        if (medTime <= now) {
          medTime.setDate(medTime.getDate() + 1);
        }
        
        // Calculate reminder time (medication time minus reminderBefore minutes)
        const reminderTime = new Date(medTime);
        reminderTime.setMinutes(reminderTime.getMinutes() - (prefs.notifications.reminderBefore || 15));
        
        // Calculate time until reminder
        const timeUntilReminder = reminderTime - now;
        
        // Only schedule if it's in the future
        if (timeUntilReminder > 0) {
          const timeoutId = setTimeout(() => {
            showMedicationReminder(med, medTime);
          }, timeUntilReminder);
          
          reminderTimeouts.push(timeoutId);
          
          // Schedule the next reminder for the next day
          const nextDayTimeoutId = setTimeout(() => {
            scheduleMedicationReminders();
          }, timeUntilReminder + (24 * 60 * 60 * 1000));
          
          reminderTimeouts.push(nextDayTimeoutId);
        }
      });
    });
  }
  
  function showMedicationReminder(med, medTime) {
    if (!prefs.notifications.enabled) return;
    
    requestNotificationPermission().then(hasPermission => {
      if (!hasPermission) return;
      
      const options = {
        body: `Time to take ${med.name} (${med.dosage})`,
        icon: '/path/to/icon.png',
        tag: `med-reminder-${med.id}-${medTime.getTime()}`,
        requireInteraction: true,
        actions: [
          { action: 'snooze', title: 'Snooze 5 min' },
          { action: 'taken', title: 'Mark as Taken' }
        ]
      };
      
      const notification = new Notification(`💊 ${med.name} Reminder`, options);
      
      notification.onclick = (event) => {
        // Handle notification click
        window.focus();
        notification.close();
      };
      
      notification.onaction = (event) => {
        if (event.action === 'taken') {
          // Mark as taken in the app
          markStatus(med.id, 'taken', medTime);
        } else if (event.action === 'snooze') {
          // Snooze for 5 minutes
          setTimeout(() => {
            showMedicationReminder(med, new Date(medTime.getTime() + (5 * 60 * 1000)));
          }, prefs.notifications.snoozeDuration * 60 * 1000);
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
      
      // Track active notification
      activeNotifications.push({
        id: `med-reminder-${med.id}-${medTime.getTime()}`,
        notification,
        timeoutId: setTimeout(() => {
          // Auto-mark as missed after 1 hour if not taken
          markStatus(med.id, 'missed', medTime);
        }, 60 * 60 * 1000) // 1 hour
      });
    });
  }
  
  function clearAllReminders() {
    // Clear all pending timeouts
  reminderTimeouts.forEach(timeoutId => clearTimeout(timeoutId));
  reminderTimeouts = [];
  
  // Close all active notifications
  activeNotifications.forEach(({ notification, timeoutId }) => {
    if (notification) notification.close();
    if (timeoutId) clearTimeout(timeoutId);
  });
  
  activeNotifications = [];
  
  // Clear any existing service worker notifications
  if ('serviceWorker' in navigator && 'Notification' in window && Notification.permission === 'granted') {
    navigator.serviceWorker.getRegistration().then(registration => {
      if (registration) {
        registration.getNotifications().then(notifications => {
          notifications.forEach(notification => notification.close());
        });
      }
    });
  }
  }
  
  // --- Settings Page ---
  function initSettings(){
    // Theme segmented
    const themeLight = document.getElementById('themeLight');
    const themeDark = document.getElementById('themeDark');
    const setThemeActive = () => {
      themeLight?.classList.toggle('active', prefs.theme==='light');
      themeDark?.classList.toggle('active', prefs.theme==='dark');
    };
    setThemeActive();
    themeLight?.addEventListener('click', ()=>{ prefs.theme='light'; applyPrefs(); persistPrefs(); setThemeActive(); });
    themeDark?.addEventListener('click', ()=>{ prefs.theme='dark'; applyPrefs(); persistPrefs(); setThemeActive(); });

    // Font segmented
    const fontBtns = $$('[data-font]');
    const applyFontSize = (size) => {
      prefs.fontSize = size;
      const scale = size==='small'? 0.9 : size==='large'? 1.2 : 1.0;
      prefs.fontScale = scale;
      applyPrefs(); persistPrefs();
      fontBtns.forEach(b=> b.classList.toggle('active', b.getAttribute('data-font')===size));
    };
    if(fontBtns.length){
      fontBtns.forEach(b=> b.classList.toggle('active', b.getAttribute('data-font')===prefs.fontSize));
      fontBtns.forEach(btn=> btn.addEventListener('click', ()=> applyFontSize(btn.getAttribute('data-font'))));
    }

    // Contrast toggle
    const contrastToggle = document.getElementById('contrastToggle');
    if(contrastToggle){
      contrastToggle.checked = !!prefs.contrast;
      contrastToggle.onchange = () => {
        prefs.contrast = contrastToggle.checked;
        document.documentElement.classList.toggle('high-contrast', !!prefs.contrast);
        persistPrefs();
      };
    }

    // Notification prefs
    const notifEnable = document.getElementById('notifEnable');
    const notifSound = document.getElementById('notifSound');
    const reminderBefore = document.getElementById('reminderBefore');
    const morningTime = document.getElementById('morningTime');
    const afternoonTime = document.getElementById('afternoonTime');
    const eveningTime = document.getElementById('eveningTime');
    const snoozeDuration = document.getElementById('snoozeDuration');
    const testNotificationBtn = document.getElementById('testNotification');
    
    // Initialize form with current preferences
    if (notifEnable) notifEnable.checked = prefs.notifications.enabled !== false;
    if (notifSound) notifSound.checked = prefs.notifications.sound !== false;
    if (reminderBefore) reminderBefore.value = prefs.notifications.reminderBefore || 15;
    if (morningTime) morningTime.value = prefs.notifications.morningTime || '08:00';
    if (afternoonTime) afternoonTime.value = prefs.notifications.afternoonTime || '13:00';
    if (eveningTime) eveningTime.value = prefs.notifications.eveningTime || '20:00';
    if (snoozeDuration) snoozeDuration.value = prefs.notifications.snoozeDuration || 5;
    
    // Save notification preferences
    function saveNotificationPrefs() {
      prefs.notifications = {
        enabled: notifEnable?.checked !== false,
        sound: notifSound?.checked !== false,
        reminderBefore: parseInt(reminderBefore?.value || 15, 10),
        morningTime: morningTime?.value || '08:00',
        afternoonTime: afternoonTime?.value || '13:00',
        eveningTime: eveningTime?.value || '20:00',
        snoozeDuration: parseInt(snoozeDuration?.value || 5, 10)
      };
      persistPrefs();
      
      // Reschedule reminders with new settings
      scheduleMedicationReminders();
      
      // Show confirmation
      toast('Notification settings saved');
    }
    
    // Add event listeners
    const notificationInputs = [notifEnable, notifSound, reminderBefore, morningTime, 
                              afternoonTime, eveningTime, snoozeDuration];
    
    notificationInputs.forEach(input => {
      if (input) {
        input.addEventListener('change', saveNotificationPrefs);
      }
    });
    
    // Test notification button
    if (testNotificationBtn) {
      testNotificationBtn.addEventListener('click', () => {
        requestNotificationPermission().then(hasPermission => {
          if (hasPermission) {
            const testMed = {
              id: 'test',
              name: 'Test Medication',
              dosage: '1 pill',
              times: [new Date().toTimeString().substr(0, 5)]
            };
            showMedicationReminder(testMed, new Date());
          } else {
            toast('Please enable notifications in your browser settings');
          }
        });
      });
    }
    function saveNotif(){ prefs.notifications = { enabled: !!notifEnable.checked, lead: notifLead.value }; persistPrefs(); }
    notifEnable?.addEventListener('change', saveNotif);
    notifLead?.addEventListener('change', saveNotif);
  }

  // --- Patients Page ---
  function renderPatients(){
    const listEl = document.getElementById('patientsList');
    const form = document.getElementById('patientForm');
    const pts = getPatients();
    if(listEl){
      if(pts.length===0){ listEl.innerHTML = '<div class="helper">No patients yet. Add one below.</div>'; }
      else {
        listEl.innerHTML = '';
        pts.forEach(p => {
          const el = document.createElement('div');
          el.className = 'item';
          el.innerHTML = `<div class="item-left">
              <div class="item-icon"><i class="bi bi-person"></i></div>
              <div>
                <div class="item-title">${escapeHtml(p.name)}</div>
                <div class="item-meta">${escapeHtml(p.age||'')}${p.condition? ' • '+escapeHtml(p.condition):''}${p.contact? ' • '+escapeHtml(p.contact):''}</div>
              </div>
            </div>
            <div class="item-actions">
              <a class="btn btn-primary" href="patient_medications.php?patientId=${p.id}">View Medications</a>
              <button class="btn btn-ghost" data-edit="${p.id}">Edit</button>
              <button class="btn btn-danger" data-delete="${p.id}">Delete</button>
            </div>`;
          listEl.appendChild(el);
        });
        listEl.onclick = async (e) => {
          const del = e.target.closest('[data-delete]');
          const edit = e.target.closest('[data-edit]');
          if(del){
            const id = del.getAttribute('data-delete');
            const ok = await confirmDialog({ title:'Delete Patient', message:'This removes the patient reference (meds remain until you reset app).', confirmText:'Delete' });
            if(!ok) return;
            const arr = getPatients().filter(x=>x.id!==id);
            savePatients(arr);
            if(prefs.activePatientId===id){ prefs.activePatientId = arr[0]?.id || null; persistPrefs(); }
            renderPatients();
          }
          if(edit){
            const id = edit.getAttribute('data-edit');
            const p = getPatients().find(x=>x.id===id);
            if(!p) return;
            if(form){
              form.pid.value = p.id;
              form.pname.value = p.name;
              form.page.value = p.age || '';
              form.pcond.value = p.condition || '';
              form.pcontact.value = p.contact || '';
              form.scrollIntoView({behavior:'smooth'});
            }
          }
        };
      }
    }
    if(form){
      form.onsubmit = (e) => {
        e.preventDefault();
        clearFormErrors(form);
        const id = form.pid.value || uuid();
        const data = { id, name: form.pname.value.trim(), age: form.page.value.trim(), condition: form.pcond.value.trim(), contact: form.pcontact.value.trim() };
        if(!data.name){ setFieldError(form.pname, 'Name is required'); return; }
        const arr = getPatients();
        const i = arr.findIndex(x=>x.id===id);
        if(i>=0) arr[i]=data; else arr.push(data);
        savePatients(arr);
        if(!prefs.activePatientId){ prefs.activePatientId = id; persistPrefs(); }
        form.reset(); form.pid.value='';
        toast('Patient saved');
        renderPatients();
        const listNode = document.getElementById('patientsList');
        listNode?.scrollIntoView({ behavior:'smooth' });
      };
    }
  }

  // --- Patient Medications Page ---
  function renderPatientMeds(){
    const q = new URLSearchParams(location.search);
    const pid = q.get('patientId') || prefs.activePatientId;
    if(pid){ prefs.activePatientId = pid; delete prefs.activePatient; persistPrefs(); meds = load(getStoreKey(), []); }
    const hdr = document.getElementById('pmHeader');
    const pts = getPatients();
    const p = pts.find(x=>x.id===pid);
    if(hdr && p){ hdr.textContent = `${p.name}'s Medications`; }
    const list = document.getElementById('pmList');
    if(!list) return;
    list.innerHTML = '';
    if(meds.length===0){ list.innerHTML = '<div class="helper">No medications for this patient. Use Add Medication.</div>'; return; }
    meds.forEach(m => {
      const status = latestStatusAt(m, (m.times||[])[0]);
      const sClass = status==='taken'?'taken':(status==='missed'?'missed':'upcoming');
      const el = document.createElement('div');
      el.className = 'item';
      el.innerHTML = `<div class=\"item-left\">
          <div class=\"item-icon\"><i class=\"bi bi-capsule\"></i></div>
          <div>
            <div class=\"item-title\">${escapeHtml(m.name)}</div>
            <div class=\"item-meta\">${escapeHtml(m.dosage)} • ${m.frequency}${m.meal? ' • '+escapeHtml(m.meal):''} • ${(m.times||[]).join(' • ')}</div>
            <span class=\"status ${sClass}\">${statusLabel(sClass)}</span>
          </div>
        </div>
        <div class=\"item-actions\">
          <a class=\"btn btn-primary\" href=\"add_medication.php?patientId=${pid}&edit=${m.id}\">Edit</a>
          <button class=\"btn btn-danger\" data-delete=\"${m.id}\">Delete</button>
        </div>`;
      list.appendChild(el);
    });
    list.onclick = async (e) => {
      const del = e.target.closest('[data-delete]');
      if(del){
        const id = del.getAttribute('data-delete');
        const ok = await confirmDialog({ title:'Delete Medication', message:'Remove this medication?', confirmText:'Delete' });
        if(!ok) return;
        meds = meds.filter(m=>m.id!==id); persist(); renderPatientMeds();
      }
    };
  }

  // --- Login/Register Pages ---
  function initLoginPage(){
    const form = document.getElementById('loginForm');
    if(!form) return;
    form.onsubmit = (e) => {
      e.preventDefault();
      clearFormErrors(form);
      const email = form.email.value.trim();
      const pass = form.password.value;
      let hasErr = false;
      if(!email){ setFieldError(form.email, 'Email is required'); hasErr = true; }
      else if(!validateEmailFormat(email)){ setFieldError(form.email, 'Enter a valid email'); hasErr = true; }
      if(!pass){ setFieldError(form.password, 'Password is required'); hasErr = true; }
      if(hasErr) return;
      const HARD_EMAIL = 'sarannia123@gmail.com';
      const HARD_PASS = 'Sarannia123';
      if(email !== HARD_EMAIL){ setFieldError(form.email, 'Email not recognized'); return; }
      if(pass !== HARD_PASS){ setFieldError(form.password, 'Incorrect password'); return; }
      setSession({ loggedIn:true, caregiverId:'local-caregiver' });
      toast('Logged in');
      setTimeout(()=> location.href='index.php', 300);
    };
  }
  function initRegisterPage(){
    const form = document.getElementById('registerForm');
    if(!form) return;
    form.onsubmit = (e) => {
      e.preventDefault();
      clearFormErrors(form);
      const name = form.r_name.value.trim();
      const email = form.r_email.value.trim();
      const phone = form.r_phone.value.trim();
      const org = form.r_org.value.trim();
      const pass = form.r_password?.value || '';
      const experience = form.r_exp.value.trim();
      let hasErr = false;
      if(!name){ setFieldError(form.r_name, 'Name is required'); hasErr = true; }
      if(!email){ setFieldError(form.r_email, 'Email is required'); hasErr = true; }
      else if(!validateEmailFormat(email)){ setFieldError(form.r_email, 'Enter a valid email'); hasErr = true; }
      if(pass && pass.length>0 && pass.length<8){ setFieldError(form.r_password, 'Password must be at least 8 characters'); hasErr = true; }
      if(experience && (+experience < 0)){ setFieldError(form.r_exp, 'Experience cannot be negative'); hasErr = true; }
      if(hasErr) return;
      const profile = { name, email, phone, org, experience, updatedAt: new Date().toISOString() };
      save(CAREGIVER_PROFILE, profile);
      setSession({ loggedIn:true, caregiverId: 'local-caregiver' });
      toast('Account created');
      setTimeout(()=> location.href='index.php', 300);
    };
  }
  function getPatients(){ return load(PATIENTS_KEY, []); }
  function savePatients(list){ save(PATIENTS_KEY, list); }
  function session(){ return load(CAREGIVER_SESSION, { loggedIn:false }); }
  function setSession(s){ save(CAREGIVER_SESSION, s); }
  let meds = load(getStoreKey(), []);

  function load(key, fallback){
    try{ return JSON.parse(localStorage.getItem(key)) ?? fallback; }catch{ return fallback; }
  }
  function save(key, value){ localStorage.setItem(key, JSON.stringify(value)); }
  function persist(){ save(getStoreKey(), meds); }
  function persistPrefs(){ save(PREF_KEY, prefs); }

  // Migrate legacy records (single time -> times array)
  function migrate(){
    let changed = false;
    meds = meds.map(m => {
      if(!m) return m;
      if(!m.times){
        if(m.time){ m.times = [m.time]; delete m.time; changed = true; }
        else { m.times = []; }
      }
      return m;
    });
    if(changed) persist();
    // Migrate legacy A/B to per-patient structure by creating demo patients if none exist
    let pts = getPatients();
    if(pts.length===0){
      const demo = [];
      // If legacy medication keys exist, create mapped patients
      const legacyKeys = ['patientA','patientB'];
      legacyKeys.forEach(code => {
        const arr = load(`${BASE_STORE_KEY}_${code}`, null);
        if(arr){
          const id = uuid();
          demo.push({ id, name: code==='patientA'?'Patient A':'Patient B', age:'', condition:'', contact:'' });
          // Move meds into new key
          save(`${BASE_STORE_KEY}_${id}`, arr);
        }
      });
      if(demo.length>0){
        savePatients(demo);
        // Set activePatientId to first demo
        if(!prefs.activePatientId){ prefs.activePatientId = demo[0].id; persistPrefs(); }
      }
    }
  }
  migrate();

  // --- Preferences / Theme ---
  const root = document.documentElement;
  const applyPrefs = () => {
    root.setAttribute('data-theme', prefs.theme);
    const px = Math.max(8, Math.round(16 * (prefs.fontScale || 1)));
    root.style.setProperty('--font', `${px}px`);
  };
  applyPrefs();

  // Ensure a default caregiver profile and session exist (hardcoded for prototype)
  (function ensureDefaultCaregiver(){
    const existing = load(CAREGIVER_PROFILE, null);
    if(!existing){
      save(CAREGIVER_PROFILE, {
        name: 'Sarannia Veeramuthu',
        email: 'sarannia123@gmail.com',
        phone: '+60-1151444588',
        org: 'Wellness Medical Clinic',
        experience: '5',
        updatedAt: new Date().toISOString()
      });
    }
    const sess = session();
    if(!sess.loggedIn){ setSession({ loggedIn:true, caregiverId:'local-caregiver' }); }
  })();

  const themeBtn = $('#themeToggle');
  const fontUp = $('#fontUp');
  const fontDown = $('#fontDown');
  const themeLight = document.getElementById('themeLight');
  const themeDark = document.getElementById('themeDark');
  const setThemeActive = () => {
    themeLight?.classList.toggle('active', prefs.theme==='light');
    themeDark?.classList.toggle('active', prefs.theme==='dark');
  };
  setThemeActive();
  themeLight?.addEventListener('click', ()=>{ prefs.theme='light'; applyPrefs(); persistPrefs(); setThemeActive(); });
  themeDark?.addEventListener('click', ()=>{ prefs.theme='dark'; applyPrefs(); persistPrefs(); setThemeActive(); });
  themeBtn?.addEventListener('click', () => { prefs.theme = prefs.theme === 'light' ? 'dark' : 'light'; applyPrefs(); persistPrefs(); setThemeActive(); toast(`Theme: ${prefs.theme}`); });
  fontUp?.addEventListener('click', () => { prefs.fontScale = (prefs.fontScale || 1) + 0.1; applyPrefs(); persistPrefs(); });
  fontDown?.addEventListener('click', () => { prefs.fontScale = Math.max(0.4, (prefs.fontScale || 1) - 0.1); applyPrefs(); persistPrefs(); });

  // --- Toast ---
  function toast(msg, type = 'info') {
    // Create toast element if it doesn't exist
    let toastEl = document.querySelector('.toast');
    
    if (!toastEl) {
      toastEl = document.createElement('div');
      toastEl.className = 'toast';
      document.body.appendChild(toastEl);
    }
    
    // Set toast content and type
    toastEl.textContent = msg;
    
    // Remove any existing type classes
    toastEl.classList.remove('toast-success', 'toast-error', 'toast-warning', 'toast-info');
    
    // Add the appropriate type class
    if (type) {
      toastEl.classList.add(`toast-${type}`);
    }
    
    // Show the toast
    toastEl.classList.add('show');
    
    // Auto-hide after delay
    clearTimeout(window.toastTimer);
    window.toastTimer = setTimeout(() => {
      toastEl.classList.remove('show');
      // Remove the toast after animation completes
      setTimeout(() => {
        if (toastEl && !toastEl.classList.contains('show')) {
          toastEl.remove();
        }
      }, 300);
    }, 3000);
    
    return toastEl;
  }

  // --- Modal ---
  function getModalEls(){
    return {
      modal: $('#modal'),
      modalTitle: $('#modalTitle'),
      modalDesc: $('#modalDesc'),
      modalCancel: $('#modalCancel'),
      modalConfirm: $('#modalConfirm'),
    };
  }
  let modalResolve;
  function confirmDialog({ title='Confirm', message='Are you sure?', confirmText='Confirm', cancelText='Cancel' }){
    return new Promise((resolve) => {
      modalResolve = resolve;
      const { modal, modalTitle, modalDesc, modalCancel, modalConfirm } = getModalEls();
      if(!modal || !modalTitle || !modalDesc || !modalCancel || !modalConfirm){ resolve(false); return; }
      modalTitle.textContent = title;
      modalDesc.textContent = message;
      modalConfirm.textContent = confirmText;
      modalCancel.textContent = cancelText;
      modal.setAttribute('aria-hidden','false');
      modalConfirm.onclick = () => { closeModal(); resolve(true); };
      modalCancel.onclick = () => { closeModal(); resolve(false); };
      modal.addEventListener('click', (e) => { if(e.target === modal){ closeModal(); resolve(false); } }, { once:true });
      window.addEventListener('keydown', onEsc, { once:true });
      function onEsc(ev){ if(ev.key==='Escape'){ closeModal(); resolve(false); } }
    });
  }
  function closeModal(){ const m = $('#modal'); m?.setAttribute('aria-hidden','true'); }

  // --- Medication Model ---
  /* Medication shape
    { id, name, dosage, times:["HH:MM", ...], meal:"Before Meal|After Meal|With Meal", frequency:"Once Daily|Twice Daily|Thrice Daily|Every Night", history: [{date:"YYYY-MM-DD", time:"HH:MM", status:"taken|missed"}] }
  */

  function addMedication(m, patientId = null) { 
    const prefs = load(PREF_KEY, {});
    const patient = patientId || prefs.activePatientId || null;
    meds.push({ 
      ...m, 
      id: uuid(), 
      patientId: patient,
      history: m.history || [] 
    }); 
    persist(); 
  }
  function updateMedication(id, patch){ meds = meds.map(m => m.id===id ? { ...m, ...patch } : m); persist(); }
  async function deleteMedication(id){
    const ok = await confirmDialog({ title:'Delete Medication', message:'This will remove the medication from your list.', confirmText:'Delete', cancelText:'Cancel' });
    if(!ok) return;
    meds = meds.filter(m => m.id!==id); persist(); toast('Medication deleted');
    routeRefresh();
  }
  function markStatus(id, status, timeStr){
    const m = meds.find(x => x.id===id); 
    if(!m) return;
    
    const d = new Date();
    const date = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
    let updated = false;
    
    // Ensure history array exists
    m.history = [...(m.history||[])];
    
    // Find and update existing history entry if it exists
    for(let i=0; i < m.history.length; i++){
      const h = m.history[i];
      if(h.date === date && h.time === timeStr){ 
        h.status = status; 
        updated = true; 
        break; 
      }
    }
    
    // If no existing entry, add a new one
    if(!updated){ 
      m.history.push({ 
        date, 
        time: timeStr, 
        status 
      }); 
    }
    
    persist(); 
    
    // Show appropriate toast message
    if(status === 'taken') {
      toast('Marked as taken', 'success');
    } else if(status === 'missed') {
      toast('Marked as missed', 'error');
    }
    
    // Refresh the UI
    routeRefresh();
  }

  // --- Logic helpers ---
  function upcomingReminders(withinMinutes = 180){
    const now = TODAY();
    const upcoming = [];
    meds.forEach(m => {
      (m.times||[]).forEach(tStr => {
        const t = parseTime(tStr);
        const diff = (t - now) / 60000; // minutes
        const status = latestStatusAt(m, tStr);
        if(diff >= -60 && diff <= withinMinutes && status !== 'taken'){
          upcoming.push({ ...m, time: tStr, inMin: Math.round(diff) });
        }
      });
    });
    return upcoming.sort((a,b)=>a.inMin-b.inMin);
  }
  function latestStatusAt(m, time){
    if(!m.history || m.history.length===0) return 'upcoming';
    const now = TODAY();
    const todayStr = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}`;
    for(let i=m.history.length-1;i>=0;i--){
      const h = m.history[i];
      if(h.date===todayStr && h.time===time) return h.status;
    }
    return 'upcoming';
  }
  function nextDose(){
    const up = upcomingReminders(720);
    return up[0] || null;
  }
  function adherenceSummary(){
    let taken=0, missed=0, total=0;
    meds.forEach(m => {
      (m.history||[]).forEach(h => { total++; if(h.status==='taken') taken++; if(h.status==='missed') missed++; });
    });
    const rate = total? Math.round((taken/total)*100):0;
    return { taken, missed, total, rate };
  }

  // --- Caregiver Dashboard Page ---
  function renderCaregiver(){
    const sel = document.getElementById('patientSel');
    const pts = getPatients();
    if(sel){
      if(pts.length>0){
        sel.innerHTML = pts.map(p=>`<option value="${p.id}">${escapeHtml(p.name)}</option>`).join('');
        if(prefs.activePatientId && pts.some(p=>p.id===prefs.activePatientId)){
          sel.value = prefs.activePatientId;
        } else {
          prefs.activePatientId = pts[0].id; persistPrefs(); sel.value = prefs.activePatientId;
        }
      } else {
        // Legacy fallback
        sel.innerHTML = `<option value="patientA">Patient A</option><option value="patientB">Patient B</option>`;
        sel.value = prefs.activePatient || 'patientA';
      }
      sel.onchange = () => {
        const val = sel.value;
        if(pts.some(p=>p.id===val)){
          prefs.activePatientId = val; delete prefs.activePatient;
        } else {
          prefs.activePatient = val; prefs.activePatientId = null;
        }
        persistPrefs();
        meds = load(getStoreKey(), []);
        renderCaregiver();
      };
    }
    const s = adherenceSummary();
    const t = document.getElementById('cgTaken'); if(t) t.textContent = s.taken;
    const m = document.getElementById('cgMissed'); if(m) m.textContent = s.missed;
    const tot = document.getElementById('cgTotal'); if(tot) tot.textContent = s.total;
    const r = document.getElementById('cgRate'); if(r) r.textContent = s.rate + '%';

    // Render Recent Activity feed for today
    const feed = document.getElementById('caregiverFeed');
    if(feed){
      const now = TODAY();
      const todayStr = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}`;
      const events = [];
      meds.forEach(med => {
        (med.history||[]).forEach(h => {
          if(h.date === todayStr && (h.status === 'taken' || h.status === 'missed')){
            events.push({
              time: h.time,
              status: h.status,
              medName: med.name,
              dosage: med.dosage
            });
          }
        });
      });
      // Sort by time descending (latest first)
      events.sort((a,b) => parseTime(b.time) - parseTime(a.time));
      feed.innerHTML = '';
      if(events.length === 0){
        feed.innerHTML = '<div class="helper">No activity yet for today.</div>';
      } else {
        events.forEach(ev => {
          const sClass = ev.status === 'taken' ? 'taken' : 'missed';
          const icon = ev.status === 'taken' ? '<i class="bi bi-check-circle"></i>' : '<i class="bi bi-x-circle"></i>';
          const el = document.createElement('div');
          el.className = 'item';
          el.innerHTML = `<div class="item-left">
              <div class="item-icon">${icon}</div>
              <div>
                <div class="item-title">${escapeHtml(ev.medName)} · ${escapeHtml(ev.dosage)}</div>
                <div class="item-meta">${ev.time}</div>
                <span class="status ${sClass}">${statusLabel(sClass)}</span>
              </div>
            </div>`;
          feed.appendChild(el);
        });
      }
    }
  }

  // --- Page routers ---
  function routeRefresh(){
    // Always reload meds for the active patient in case it changed
    meds = load(getStoreKey(), []);
    const page = document.body.dataset.page;
    if(page==='home') renderHome();
    if(page==='add') initAddPage();
    if(page==='list') renderList();
    if(page==='caregiver') renderCaregiver();
    if(page==='settings') initSettings();
    if(page==='login') initLoginPage();
    if(page==='register') initRegisterPage();
    if(page==='patients') renderPatients();
    if(page==='patient_meds') renderPatientMeds();
    if(page==='profile') initProfile();
    if(page==='about') {/* static page, no-op */}
  }

  // --- Render: Home ---
  function getTodaysMedications() {
  const today = new Date().toISOString().split('T')[0]; // YYYY-MM-DD
  const now = new Date();
  const currentTime = now.getHours() * 60 + now.getMinutes(); // Current time in minutes
  
  // Get all medications with their scheduled times for today
  const todaysMeds = [];
  
  meds.forEach(med => {
    (med.times || []).forEach(timeStr => {
      const [hours, minutes] = timeStr.split(':').map(Number);
      const medTimeInMinutes = hours * 60 + minutes;
      
      // Create a medication entry for each scheduled time
      const medEntry = {
        id: med.id,
        name: med.name,
        dosage: med.dosage,
        time: timeStr,
        timeInMinutes: medTimeInMinutes,
        status: 'pending' // default status
      };
      
      // Check if there's a history entry for today
      if (med.history) {
        const todayHistory = med.history.find(h => 
          h.date === today && h.time === timeStr
        );
        
        if (todayHistory) {
          medEntry.status = todayHistory.status; // 'taken' or 'missed'
        } else if (medTimeInMinutes < currentTime - 30) { // If time has passed and not marked
          medEntry.status = 'missed';
        }
      } else if (medTimeInMinutes < currentTime - 30) { // No history at all
        medEntry.status = 'missed';
      }
      
      todaysMeds.push(medEntry);
    });
  });
  
  // Sort by time
  return todaysMeds.sort((a, b) => a.timeInMinutes - b.timeInMinutes);
}

function renderHome(){
    // Render patient badge
    const badge = document.getElementById('patientBadge');
    if(badge){
      let label = null;
      const pts = getPatients();
      if(prefs.activePatientId){
        const p = pts.find(x=>x.id===prefs.activePatientId);
        if(p) label = p.name;
      }
      if(!label){ label = (prefs.activePatient === 'patientB') ? 'Patient B' : 'Patient A'; }
      badge.textContent = `Active: ${label}`;
      badge.className = 'status upcoming';
    }
    const next = nextDose();
    const nextBox = $('#nextDoseBox');
    const reminderList = $('#reminders');
    const todayScheduleList = $('#todaySchedule');
    
    // Update KPIs
    const { taken, missed, total, rate } = adherenceSummary();
    $('#kpiTaken').textContent = taken;
    $('#kpiMissed').textContent = missed;
    $('#kpiTotal').textContent = total;
    $('#kpiRate').textContent = rate + '%';
    
    // Render today's medication schedule
    const todaysMeds = getTodaysMedications();
    todayScheduleList.innerHTML = '';
    
    if (todaysMeds.length === 0) {
      todayScheduleList.innerHTML = '<div class="helper">No medications scheduled for today.</div>';
    } else {
      todaysMeds.forEach(med => {
        const medEl = document.createElement('div');
        medEl.className = `item ${med.status}`;
        medEl.innerHTML = `
          <div class="item-left">
            <div class="item-icon">
              <i class="bi ${med.status === 'taken' ? 'bi-check-circle-fill' : med.status === 'missed' ? 'bi-x-circle-fill' : 'bi-clock'}"></i>
            </div>
            <div>
              <div class="item-title">${escapeHtml(med.name)} <span class="dosage">${escapeHtml(med.dosage)}</span></div>
              <div class="item-meta">Scheduled for ${med.time}</div>
              <span class="status ${med.status}">${med.status.charAt(0).toUpperCase() + med.status.slice(1)}</span>
            </div>
          </div>
          <div class="item-actions">
            <button class="btn btn-primary" data-action="taken" data-id="${med.id}" data-time="${med.time}" ${med.status === 'taken' ? 'disabled' : ''}>
              <i class="bi bi-check-lg"></i> Taken
            </button>
            <button class="btn btn-danger" data-action="missed" data-id="${med.id}" data-time="${med.time}" ${med.status === 'missed' || med.status === 'taken' ? 'disabled' : ''}>
              <i class="bi bi-x-lg"></i> Missed
            </button>
          </div>
        `;
        todayScheduleList.appendChild(medEl);
      });
    }
    
    // Add event listeners for the new buttons
    todayScheduleList.addEventListener('click', onItemAction);

    if(next){
      nextBox.innerHTML = `<div class="item-left"><div class="item-icon"><i class="bi bi-alarm"></i></div>
        <div><div class="item-title">${escapeHtml(next.name)} · ${escapeHtml(next.dosage)}</div>
        <div class="item-meta">Next at ${next.time} (${next.inMin>=0? next.inMin+' min':'now'})</div></div></div>
        <div class="item-actions">
          <button class="btn btn-primary" data-action="taken" data-id="${next.id}" data-time="${next.time}">Taken</button>
          <button class="btn btn-danger" data-action="missed" data-id="${next.id}" data-time="${next.time}">Missed</button>
        </div>`;
    } else {
      nextBox.innerHTML = '<div class="helper">No upcoming dose in the next 12 hours.</div>';
    }

    const ups = upcomingReminders();
    reminderList.innerHTML = '';
    if(ups.length===0){
      reminderList.innerHTML = '<div class="helper">No reminders in the next 3 hours.</div>';
    } else {
      ups.forEach(m => {
        const status = latestStatusAt(m, m.time);
        const sClass = status==='taken'?'taken':(status==='missed'?'missed':'upcoming');
        const el = document.createElement('div');
        el.className = 'item';
        el.innerHTML = `<div class="item-left">
            <div class="item-icon"><i class="bi bi-capsule"></i></div>
            <div>
              <div class="item-title">${escapeHtml(m.name)}</div>
              <div class="item-meta">${escapeHtml(m.dosage)} • ${m.time} • in ${m.inMin} min</div>
              <span class="status ${sClass}">${statusLabel(sClass)}</span>
            </div>
          </div>
          <div class="item-actions">
            <button class="btn btn-primary" data-action="taken" data-id="${m.id}" data-time="${m.time}">Taken</button>
            <button class="btn btn-danger" data-action="missed" data-id="${m.id}" data-time="${m.time}">Missed</button>
          </div>`;
        reminderList.appendChild(el);
      });
    }

    reminderList.addEventListener('click', onItemAction);
    nextBox.addEventListener('click', onItemAction);
  }

  function onItemAction(e){
    const btn = e.target.closest('button'); if(!btn) return;
    const id = btn.getAttribute('data-id');
    const action = btn.getAttribute('data-action');
    const t = btn.getAttribute('data-time');
    if(action==='taken') markStatus(id,'taken', t);
    if(action==='missed') markStatus(id,'missed', t);
  }

  // --- Render: List ---
  function renderList(){
    const list = $('#medList');
    list.innerHTML = '';
    if(meds.length===0){ list.innerHTML = '<div class="helper">No medications added yet. Use the Add page.</div>'; return; }
    meds.forEach(m => {
      const status = latestStatusAt(m, (m.times||[])[0]);
      const sClass = status==='taken'?'taken':(status==='missed'?'missed':'upcoming');
      const el = document.createElement('div');
      el.className = 'item';
      el.innerHTML = `<div class="item-left">
          <div class="item-icon"><i class="bi bi-capsule"></i></div>
          <div>
            <div class="item-title">${escapeHtml(m.name)}</div>
            <div class="item-meta">${escapeHtml(m.dosage)} • ${m.frequency}${m.meal? ' • '+escapeHtml(m.meal):''} • ${(m.times||[]).join(' • ')}</div>
            <span class="status ${sClass}">${statusLabel(sClass)}</span>
          </div>
        </div>
        <div class="item-actions">
          <button class="btn btn-primary" data-edit="${m.id}">Edit</button>
          <button class="btn btn-danger" data-delete="${m.id}">Delete</button>
        </div>`;
      list.appendChild(el);
    });

    list.addEventListener('click', async (e) => {
      const del = e.target.closest('[data-delete]');
      const edit = e.target.closest('[data-edit]');
      if(del){ await deleteMedication(del.getAttribute('data-delete')); renderList(); }
      if(edit){
        const id = edit.getAttribute('data-edit');
        location.href = `add_medication.php?edit=${encodeURIComponent(id)}`;
      }
    });
  }

  // --- Profile Page ---
  function initProfile(){
    const form = document.getElementById('profileForm');
    if(form){
      const profile = load(CAREGIVER_PROFILE, {});
      form.p_name.value = profile.name || '';
      form.p_email.value = profile.email || '';
      form.p_phone.value = profile.phone || '';
      form.p_org.value = profile.org || '';
      form.p_exp.value = profile.experience || '';
      
      // Add validation feedback
      form.p_name.addEventListener('input', () => clearFieldError(form.p_name));
      form.p_email.addEventListener('input', () => clearFieldError(form.p_email));
      
      form.onsubmit = (e)=>{
        e.preventDefault();
        clearFormErrors(form);
        
        // Validate form
        let isValid = true;
        const name = form.p_name.value.trim();
        const email = form.p_email.value.trim();
        const phone = form.p_phone.value.trim();
        const org = form.p_org.value.trim();
        const experience = form.p_exp.value.trim();
        
        // Name validation
        if (!name) {
          setFieldError(form.p_name, 'Name is required');
          isValid = false;
        }
        
        // Email validation
        if (!email) {
          setFieldError(form.p_email, 'Email is required');
          isValid = false;
        } else if (!validateEmailFormat(email)) {
          setFieldError(form.p_email, 'Please enter a valid email address');
          isValid = false;
        }
        
        // Phone validation (if provided)
        if (phone && !/^[0-9\-\+\(\)\s]+$/.test(phone)) {
          setFieldError(form.p_phone, 'Please enter a valid phone number');
          isValid = false;
        }
        
        // Experience validation (if provided)
        if (experience && isNaN(experience)) {
          setFieldError(form.p_exp, 'Please enter a valid number');
          isValid = false;
        }
        
        if (!isValid) {
          toast('Please fix the errors in the form', 'error');
          return;
        }
        
        const next = {
          name,
          email,
          phone,
          org,
          experience,
          updatedAt: new Date().toISOString()
        };
        
        save(CAREGIVER_PROFILE, next);
        toast('Profile saved successfully!', 'success');
      };
    }
    const logoutBtnProfile = document.getElementById('logoutBtnProfile');
    if(logoutBtnProfile){
      logoutBtnProfile.onclick = async ()=>{
        const ok = await confirmDialog({ title:'Logout', message:'End your session?', confirmText:'Logout' });
        if(!ok) return;
        setSession({ loggedIn:false, caregiverId:null });
        toast('Logged out');
        setTimeout(()=> location.href='login.php', 300);
      };
    }
    const resetBtn = document.getElementById('resetApp');
    if(resetBtn){
      resetBtn.onclick = async ()=>{
        const ok = await confirmDialog({ title:'Reset App', message:'Clear all local data?', confirmText:'Reset' });
        if(!ok) return;
        localStorage.clear(); location.reload();
      };
    }
  }

  // --- Add/Edit Page ---
  function initAddPage(){
    const form = $('#medForm');
    // If a patientId is provided in query, set it active for this add
    const qp = new URLSearchParams(location.search);
    const pidFromQuery = qp.get('patientId');
    if(pidFromQuery){ prefs.activePatientId = pidFromQuery; delete prefs.activePatient; persistPrefs(); meds = load(getStoreKey(), []); }
    const editId = qp.get('edit');
    if(editId){
      const m = meds.find(x => x.id===editId);
      if(m){
        form.name.value = m.name;
        form.dosage.value = m.dosage;
        const t0 = (m.times && m.times[0]) ? m.times[0] : '';
        form.time.value = t0;
        form.frequency.value = m.frequency || 'Once Daily';
        if(form.meal) form.meal.value = m.meal || 'After Meal';
        $('#formTitle').textContent = 'Edit Medication';
        $('#submitBtn').textContent = 'Save Changes';
      }
    }
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      clearFormErrors(form);
      const name = form.name.value.trim();
      const dosage = form.dosage.value.trim();
      const time = form.time.value;
      const frequency = form.frequency.value;
      const meal = form.meal ? form.meal.value : undefined;
      let hasErr = false;
      if(!name){ setFieldError(form.name, 'Medication name is required'); hasErr = true; }
      if(!dosage){ setFieldError(form.dosage, 'Dosage is required'); hasErr = true; }
      if(!time){ setFieldError(form.time, 'Time is required'); hasErr = true; }
      else if(!/^\d{2}:\d{2}$/.test(time)){ setFieldError(form.time, 'Use HH:MM format'); hasErr = true; }
      if(hasErr) return;
      const times = computeTimes(time, frequency);
      if(editId){ updateMedication(editId, { name, dosage, times, frequency, meal }); toast('Medication updated'); }
      else { addMedication({ name, dosage, times, frequency, meal }); toast('Medication added'); }
      setTimeout(()=>{ location.href = 'medication_list.php'; }, 300);
    });
  }
  function computeTimes(baseHHMM, freq){
    const base = parseTime(baseHHMM);
    const addH = (d, h) => { const x = new Date(d); x.setHours(x.getHours()+h); return toTimeStr(x); };
    const to = (h,m) => { const x = new Date(); x.setHours(h, m, 0, 0); return toTimeStr(x); };
    const baseStr = toTimeStr(base);
    switch((freq||'').toLowerCase()){
      case 'once daily':
        return [baseStr];
      case 'twice daily':
        return [baseStr, addH(base, 12)];
      case 'thrice daily':
        return [baseStr, addH(base, 6), addH(base, 12)];
      case 'every night':
        return [to(21,0)];
      case 'every morning':
        return [to(8,0)];
      case 'every afternoon':
        return [to(14,0)];
      default:
        return [baseStr];
    }
  }
  function statusLabel(st){
    if(st==='taken') return 'Taken';
    if(st==='missed') return 'Missed';
    return 'Upcoming';
  }
  function escapeHtml(s){ return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c])); }

  // --- Boot ---
  $('#year')?.append(new Date().getFullYear());
  
  // Request notification permission on page load
  document.addEventListener('DOMContentLoaded', () => {
    routeRefresh();
    
    // Initialize notifications if enabled
    if (prefs.notifications?.enabled !== false) {
      requestNotificationPermission().then(() => {
        scheduleMedicationReminders();
      });
    }
    
    // Reschedule reminders when the page becomes visible again
    document.addEventListener('visibilitychange', () => {
      if (!document.hidden && prefs.notifications?.enabled) {
        scheduleMedicationReminders();
      }
    });
  });
  
  // Schedule reminders when the window regains focus
  window.addEventListener('focus', () => {
    if (prefs.notifications?.enabled) {
      scheduleMedicationReminders();
    }
  });
})();
