const searchIcon = document.querySelector('.search-icon');
const searchBox = document.querySelector('.search-box');

searchIcon.addEventListener('click', function(e) {
  e.stopPropagation();
  searchBox.classList.toggle('active');
});

// Close when clicking outside
document.addEventListener('click', function(e) {
  if (!searchBox.contains(e.target) && !searchIcon.contains(e.target)) {
    searchBox.classList.remove('active');
  }
});

const topbar = document.querySelector('.topbar');
const navbar = document.querySelector('.navbar');

window.addEventListener('scroll', function() {
  if (window.scrollY > 50) {
    topbar.classList.add('hidden');
    navbar.style.top = '0';
  } else {
    topbar.classList.remove('hidden');
    navbar.style.top = '30px';
  }
});

const heroSlides = document.querySelectorAll('.hero-slider .slide');
let heroCurrent = 0;

function showHeroSlide(index) {
  heroSlides.forEach((slide, i) => {
    slide.classList.toggle('active', i === index);
  });
}

function nextHeroSlide() {
  heroCurrent = (heroCurrent + 1) % heroSlides.length;
  showHeroSlide(heroCurrent);
}

setInterval(nextHeroSlide, 3000);


const counters = document.querySelectorAll('.counter');
counters.forEach(counter => {
  const updateCount = () => {
    const target = +counter.getAttribute('data-target');
    const count = +counter.innerText;
    const increment = target / 200; // speed factor

    if (count < target) {
      counter.innerText = Math.ceil(count + increment);
      setTimeout(updateCount, 20);
    } else {
      counter.innerText = target;
    }
  };
  updateCount();
});

const slider = document.querySelector('.car-track');

if (slider) {
  slider.addEventListener('mouseenter', () => {
    slider.style.animationPlayState = 'paused';
  });

  slider.addEventListener('mouseleave', () => {
    slider.style.animationPlayState = 'running';
  });
}

const testimonial = document.querySelector('.testimonial-track');

if (testimonial) {
  testimonial.addEventListener('mouseenter', () => {
    testimonial.style.animationPlayState = 'paused';
  });

  testimonial.addEventListener('mouseleave', () => {
    testimonial.style.animationPlayState = 'running';
  });
}

/*
let carCurrentSlide = 0;
const carSlides = document.querySelectorAll(".car-slide");

function showCarSlide(index){
  carSlides.forEach((slide, i) => {
    slide.classList.remove("active");
  });

  carSlides[index].classList.add("active");
}

function nextCarSlide(){
  carCurrentSlide++;
  if(carCurrentSlide >= carSlides.length){
    carCurrentSlide = 0;
  }
  showCarSlide(carCurrentSlide);
}

function prevCarSlide(){
  carCurrentSlide--;
  if(carCurrentSlide < 0){
    carCurrentSlide = carSlides.length - 1;
  }
  showCarSlide(carCurrentSlide);
}

*/

let carIndex = 0;

const track = document.querySelector(".car-track");
const slides = document.querySelectorAll(".car-slide");

function updateSlide() {
  track.style.transform = `translateX(-${carIndex * 100}%)`;
}

function nextCarSlide() {
  carIndex++;
  if (carIndex >= slides.length) {
    carIndex = 0;
  }
  updateSlide();
}

function prevCarSlide() {
  carIndex--;
  if (carIndex < 0) {
    carIndex = slides.length - 1;
  }
  updateSlide();
}

setInterval(nextCarSlide, 4000);



function toggleChat() {
  const chat = document.getElementById("chatbot");
  if (!chat) return;

  chat.classList.toggle("active");
}


/* SEND MESSAGE */
async function sendMessage() {
  let input = document.getElementById("userInput");
  let message = input.value;

  if (!message) return;

  addMessage(message, "user");
  input.value = "";

  showTyping();

  try {
    let res = await fetch("ai-chat.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ message: message })
    });

    let data = await res.json();

    hideTyping();

    let reply = "";

    // HuggingFace format
    if (data && data[0]?.generated_text) {
      reply = data[0].generated_text;
    }
    // fallback AI
    else if (data.error) {
      reply = getBotReply(message);
    }
    else {
      reply = getBotReply(message);
    }

    addMessage(reply, "bot");

  } catch (error) {
    hideTyping();
    addMessage(getBotReply(message), "bot");
  }
}

function getBotReply(message) {
  message = message.toLowerCase();

  // 🚗 booking
  if(message.includes("book") || message.includes("rent")) {
    return "You can book a car by clicking the 'Book Now' button or going to the booking section 🚗";
  }

  // 💰 price
  else if(message.includes("price") || message.includes("cost")) {
    return "Our prices depend on the car type. Visit the Cars section to see full details 💰";
  }

  // 📞 contact
  else if(message.includes("contact") || message.includes("phone")) {
    return "You can contact us at +111-222-333 or email info@example.com 📞";
  }

  // 🚙 car types
  else if(message.includes("cars") || message.includes("types")) {
    return "We offer SUVs, Sedans, Hatchbacks, Sports cars and Luxury vehicles 🚙";
  }

  // 👋 greeting
  else if(message.includes("hello") || message.includes("hi")) {
    return "Hello 👋 Welcome to Emerald Cars! How can I help you today?";
  }

  // ❓ default
  else {
    return "Sorry, I didn’t understand that. Try asking about booking, prices, or cars 😊";
  }
}

/* ADD MESSAGE */
function addMessage(text, type) {
  const msg = document.createElement("div");
  msg.classList.add("message", type);
  msg.innerText = text;

  const body = document.getElementById("chatBody");

  body.appendChild(msg);

  // smooth scroll instead of jump
  body.scrollTo({
    top: body.scrollHeight,
    behavior: "smooth"
  });
}


/* BOT RESPONSE (READY FOR AI API) */
function botReply(userText) {
  // 🔥 HERE YOU WILL CONNECT REAL AI API LATER
  let reply = "I am EmeraldCars Bot 🚗. You said: " + userText;

  addMessage(reply, "bot");
}

/* TYPING */
function showTyping() {
  document.getElementById("typing").style.display = "block";
}

function hideTyping() {
  document.getElementById("typing").style.display = "none";
}

/* AUTO WELCOME MESSAGE */
window.onload = function () {
  setTimeout(() => {
    addMessage("Hi 👋 Welcome to EmeraldCars! How can I help you today?", "bot");
  }, 1000);
};

/* DRAG FEATURE */
const chat = document.getElementById("chatbot");
const header = document.getElementById("chatHeader");

let isDragging = false;
let offsetX, offsetY;

header.addEventListener("mousedown", (e) => {
  isDragging = true;
  offsetX = e.clientX - chat.offsetLeft;
  offsetY = e.clientY - chat.offsetTop;
});

document.addEventListener("mousemove", (e) => {
  if (isDragging) {
    chat.style.left = (e.clientX - offsetX) + "px";
    chat.style.top = (e.clientY - offsetY) + "px";
    chat.style.bottom = "auto";
    chat.style.right = "auto";
  }
});

document.addEventListener("mouseup", () => {
  isDragging = false;
});


  const buttons = document.querySelectorAll('.filter-btn');
  const items = document.querySelectorAll('.portfolio-item');

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const filter = btn.getAttribute('data-filter');

      // highlight active button
      buttons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      // show/hide items
      items.forEach(item => {
        if (filter === 'all' || item.getAttribute('data-category') === filter) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    });
  });

document.querySelectorAll('.car-gallery').forEach(gallery => {
  const images = gallery.querySelectorAll('img');
  let current = 0;
  let autoSlide;

  const showImage = index => {
    images.forEach((img, i) => img.classList.toggle('active', i === index));
  };

  const nextImage = () => {
    current = (current + 1) % images.length;
    showImage(current);
  };

  const prevImage = () => {
    current = (current - 1 + images.length) % images.length;
    showImage(current);
  };

  // Button controls
  gallery.querySelector('.gallery-prev').addEventListener('click', () => {
    prevImage();
    resetAutoSlide();
  });

  gallery.querySelector('.gallery-next').addEventListener('click', () => {
    nextImage();
    resetAutoSlide();
  });

  // Auto slide every 4 seconds
  const startAutoSlide = () => {
    autoSlide = setInterval(nextImage, 4000);
  };

  const resetAutoSlide = () => {
    clearInterval(autoSlide);
    startAutoSlide();
  };

  // Pause auto slide on hover
  gallery.addEventListener('mouseenter', () => clearInterval(autoSlide));
  gallery.addEventListener('mouseleave', startAutoSlide);

  // Initialize
  showImage(current);
  startAutoSlide();
});



let bookingSection = document.getElementById("booking-section");

window.addEventListener("scroll", function () {
  const button = document.querySelector(".floating-btn");

  if (!button || !bookingSection) return;

  let sectionTop = bookingSection.offsetTop;

  if (window.scrollY > sectionTop - 200) {
    button.classList.add("show");
  } else {
    button.classList.remove("show");
  }
});

let openedOnce = false;

window.addEventListener("scroll", function () {
  if (openedOnce) return;

  if (window.scrollY > 1200) {
    openedOnce = true;

    setTimeout(() => {
      document.getElementById("chatbot").classList.add("active");
    }, 800);
  }
});




