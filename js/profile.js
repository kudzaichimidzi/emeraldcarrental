/*let currentSection = 'home-section';

window.addEventListener("scroll", function() {
  const dock = document.querySelector(".floating-dock");

  // ✅ FIX: check if element exists
  if (!dock) return;

  if (window.scrollY > 100) {
    dock.style.display = "flex";
  } else {
    dock.style.display = "none";
  }
});
*/

function showSection(sectionId) {
  document.querySelectorAll('.section').forEach(sec => sec.style.display = 'none');

  let target = document.getElementById(sectionId);

  if (target) {
    target.style.display = 'block';
    currentSection = sectionId;
  }
}

function previewImage(event) {
  const reader = new FileReader();

  reader.onload = function(){
    document.getElementById('profileImage').src = reader.result;

    // hide placeholder when previewing
    const placeholder = document.getElementById('profilePlaceholder');
    if (placeholder) {
      placeholder.style.display = "none";
    }
  }

  reader.readAsDataURL(event.target.files[0]);
}




function openPopup(id){
  const popup = document.getElementById(id);
  popup.style.display = "flex";
  if (!popup) return;
  setTimeout(() => popup.classList.add("show"), 10);
}




function checkPassword(){
  const pass = document.getElementById("newPassword").value;
  const confirm = document.getElementById("confirmPassword").value;

  const length = pass.length >= 6;
  const number = /\d/.test(pass);
  const special = /[!@#$%^&*]/.test(pass);
  const match = pass === confirm && pass !== "";

  // Update rules
  document.getElementById("length").innerHTML = length ? "✅ Minimum 6 characters" : "❌ Minimum 6 characters";
  document.getElementById("number").innerHTML = number ? "✅ At least 1 number" : "❌ At least 1 number";
  document.getElementById("special").innerHTML = special ? "✅ At least 1 special character" : "❌ At least 1 special character";
  document.getElementById("match").innerHTML = match ? "✅ Passwords match" : "❌ Passwords match";

  // Strength calculation
  let score = 0;
  if(length) score++;
  if(number) score++;
  if(special) score++;

  const bar = document.getElementById("strengthBar");
  const text = document.getElementById("strengthText");

  if(score === 1){
    bar.style.width = "33%";
    bar.style.background = "red";
    text.innerText = "Weak";
  }
  else if(score === 2){
    bar.style.width = "66%";
    bar.style.background = "orange";
    text.innerText = "Medium";
  }
  else if(score === 3){
    bar.style.width = "100%";
    bar.style.background = "green";
    text.innerText = "Strong";
  }
}

function openPasswordPopup(){
  // 1. Hide all sections first
  document.querySelectorAll('.section').forEach(sec => sec.style.display = 'none');

  // 2. Then open popup
  openPopup('passwordPopup');
  // optional: clear old inputs
  document.getElementById("newPassword").value = "";
  document.getElementById("confirmPassword").value = "";
}

function closePopup(id){
  const popup = document.getElementById(id);

  if (!popup) return;

  popup.classList.remove("show");

  setTimeout(() => {
    popup.style.display = "none";

    if(currentSection){
      let sec = document.getElementById(currentSection);
      if(sec) sec.style.display = 'block';
    }

  }, 300);
}


function animateCount(id, target) {
    let el = document.getElementById(id);
    if (!el) return; // stop if element doesn't exist

    let count = 0;
    let interval = setInterval(() => {
        count++;
        el.innerText = count;
        if (count >= target) clearInterval(interval);
    }, 20);
}

function openDeleteModal(){
    document.getElementById("deleteModal").style.display = "flex";
}

function closeDeleteModal(){
    document.getElementById("deleteModal").style.display = "none";
}
function toggleProfileMenu(){
  let menu = document.getElementById("profileMenu");
  menu.style.display = (menu.style.display === "flex") ? "none" : "flex";
}

function toggleNotif(){
  let panel = document.getElementById("notifPanel");

  if(!panel) return; // safety

  if(panel.classList.contains("show")){
    panel.classList.remove("show");
  } else {
    panel.classList.add("show");

    // mark as read
    fetch("profile.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: "mark_read=1"
    });

    // clear badge instantly
    let badge = document.querySelector(".badge");
    if(badge){
      badge.innerText = "0";
    }
  }
}




const suggestions = [
  {name: "Home", section: "home-section"},
  {name: "Profile Info", section: "basic-info-section"},
  {name: "Security", section: "security-section"},
  {name: "Accounts", section: "accounts-section"},
  {name: "Notifications", section: "notifications-section"},
  {name: "Delete Account", section: "delete-section"}
];

const searchBox = document.getElementById("searchBox");
const suggestionBox = document.getElementById("searchSuggestions");

if (searchBox && suggestionBox) {

  searchBox.addEventListener("keyup", function(){

    let value = this.value.toLowerCase();

    if(value !== ""){
      showSection("home-section");
    }

    highlightCards(value);
    searchBookings(value);

    suggestionBox.innerHTML = "";

    if(value === ""){
      suggestionBox.style.display = "none";
      return;
    }

    let filtered = suggestions.filter(item => 
      item.name.toLowerCase().includes(value)
    );

    filtered.forEach(item => {
      let div = document.createElement("div");
      div.innerText = item.name;

      div.onclick = function(){
        showSection(item.section);
        suggestionBox.style.display = "none";
      };

      suggestionBox.appendChild(div);
    });

    suggestionBox.style.display = "block";

  });

}

function highlightCards(value){
  let cards = document.querySelectorAll(".feature-card, .info-card, .list-item");

  cards.forEach(card => {
    card.classList.remove("highlight");

    if(card.innerText.toLowerCase().includes(value)){
      card.classList.add("highlight");
    }
  });
}

function searchBookings(value){
console.log("SEARCH VALUE:", value);
  let bookings = document.querySelectorAll("#bookingList .booking-item");

  // 🔥 FIRST handle empty search
  if(value === ""){
    bookings.forEach(b => b.style.display = "block");
    return;
  }

  // 🔥 THEN filter normally
  bookings.forEach(b => {

    let text = b.innerText.toLowerCase();

    if(text.includes(value)){
      b.style.display = "block";
    } else {
      b.style.display = "none";
    }

  });

}



window.onload = function() {

  if(window.location.hash === "#security"){
    showSection('security-section');
  }

  animateCount("totalBookings", totalBookingsValue);

};

function markAllRead(){
  fetch("profile.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "mark_read=1"
  }).then(() => {
document.querySelector(".badge").innerText = "0";
  });
}

function clearAll(){
  fetch("profile.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "clear_all=1"
  }).then(() => location.reload());
}