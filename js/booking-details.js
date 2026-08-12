document.addEventListener("DOMContentLoaded", () => {
  // Example: highlight active timeline step
  const steps = document.querySelectorAll(".timeline .step");
  steps.forEach(step => {
    step.addEventListener("click", () => {
      steps.forEach(s => s.classList.remove("active"));
      step.classList.add("active");
    });
  });
});
