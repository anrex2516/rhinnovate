// script.js
let currentSlide = 0;
const slides = document.querySelectorAll(".carousel-image");

function showSlide(index) {
  slides.forEach((slide, i) => {
    slide.classList.toggle("active", i === index);
  });
}

function moveSlide(direction) {
  currentSlide = (currentSlide + direction + slides.length) % slides.length;
  showSlide(currentSlide);
}

// Inicializa la primera imagen como activa
showSlide(currentSlide);
