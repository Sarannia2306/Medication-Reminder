<?php include __DIR__.'/includes/header.php'; ?>

<div class="onboarding-container">
  <div class="onboarding-slide active" data-slide="1">
    <div class="onboarding-image">
      <img src="assets/images/onboarding-1.svg" alt="Welcome to MedTrack">
    </div>
    <h2>Welcome to MedTrack</h2>
    <p>Your personal medication management assistant to help you stay on track with your health.</p>
    <div class="onboarding-dots">
      <span class="dot active"></span>
      <span class="dot"></span>
      <span class="dot"></span>
    </div>
    <button class="btn btn-primary next-slide">Next</button>
  </div>

  <div class="onboarding-slide" data-slide="2">
    <div class="onboarding-image">
      <img src="assets/images/onboarding-2.svg" alt="Track Your Medications">
    </div>
    <h2>Track Your Medications</h2>
    <p>Easily add and manage all your medications in one place with our simple interface.</p>
    <div class="onboarding-dots">
      <span class="dot"></span>
      <span class="dot active"></span>
      <span class="dot"></span>
    </div>
    <div class="onboarding-buttons">
      <button class="btn btn-ghost prev-slide">Back</button>
      <button class="btn btn-primary next-slide">Next</button>
    </div>
  </div>

  <div class="onboarding-slide" data-slide="3">
    <div class="onboarding-image">
      <img src="assets/images/onboarding-3.svg" alt="Get Reminders">
    </div>
    <h2>Never Miss a Dose</h2>
    <p>Customizable reminders ensure you never forget to take your medications on time.</p>
    <div class="onboarding-dots">
      <span class="dot"></span>
      <span class="dot"></span>
      <span class="dot active"></span>
    </div>
    <div class="onboarding-buttons">
      <button class="btn btn-ghost prev-slide">Back</button>
      <a href="login.php" class="btn btn-primary">Get Started</a>
    </div>
  </div>
</div>

<style>
.onboarding-container {
  max-width: 600px;
  margin: 2rem auto;
  text-align: center;
  padding: 2rem;
}

.onboarding-slide {
  display: none;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
}

.onboarding-slide.active {
  display: flex;
}

.onboarding-image {
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
}

.onboarding-image img {
  max-width: 100%;
  max-height: 100%;
}

.onboarding-dots {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin: 1rem 0;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background-color: #e0e0e0;
  display: inline-block;
}

.dot.active {
  background-color: #4a90e2;
}

.onboarding-buttons {
  display: flex;
  gap: 1rem;
  width: 100%;
  justify-content: center;
  margin-top: 1rem;
}

.btn {
  min-width: 120px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const nextButtons = document.querySelectorAll('.next-slide');
  const prevButtons = document.querySelectorAll('.prev-slide');
  const slides = document.querySelectorAll('.onboarding-slide');
  const dots = document.querySelectorAll('.dot');
  
  function showSlide(index) {
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    slides[index].classList.add('active');
    dots[index].classList.add('active');
  }
  
  nextButtons.forEach(button => {
    button.addEventListener('click', () => {
      const currentSlide = document.querySelector('.onboarding-slide.active');
      const nextSlide = currentSlide.nextElementSibling;
      
      if (nextSlide && nextSlide.classList.contains('onboarding-slide')) {
        const nextIndex = parseInt(nextSlide.dataset.slide) - 1;
        showSlide(nextIndex);
      }
    });
  });
  
  prevButtons.forEach(button => {
    button.addEventListener('click', () => {
      const currentSlide = document.querySelector('.onboarding-slide.active');
      const prevSlide = currentSlide.previousElementSibling;
      
      if (prevSlide && prevSlide.classList.contains('onboarding-slide')) {
        const prevIndex = parseInt(prevSlide.dataset.slide) - 1;
        showSlide(prevIndex);
      }
    });
  });
  
  // Initialize first slide
  showSlide(0);
});
</script>

<?php include __DIR__.'/includes/footer.php'; ?>
