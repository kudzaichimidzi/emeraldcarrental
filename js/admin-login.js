
lottie.loadAnimation({
  container: document.getElementById('adminAnimation'),
  renderer: 'svg',
  loop: true,
  autoplay: true,
  path: '../admin/vehicleimages/singing-contract.json'
});
function toggle(id, el){
  let input = document.getElementById(id);
  if(input.type === "password"){
    input.type = "text";
    el.innerText = "🙈";
  } else {
    input.type = "password";
    el.innerText = "👁";
  }
}

lottie.loadAnimation({
  container: document.getElementById('sideAnimation'),
  renderer: 'svg',
  loop: true,
  autoplay: true,
  path: '../admin/vehicleimages/call-center.json' // make sure this file exists
});


function showCode() {
  const codeBox = document.getElementById('codeBox');
  const userField = document.querySelector('[name="admin_username"]');
  const passField = document.querySelector('[name="admin_password"]');

  if (codeBox.style.display === 'none' || codeBox.style.display === '') {
    // Show code box, disable username/password
    codeBox.style.display = 'block';
    userField.disabled = true;
    passField.disabled = true;
  } else {
    // Hide code box, re-enable username/password
    codeBox.style.display = 'none';
    userField.disabled = false;
    passField.disabled = false;
  }
}


document.addEventListener("DOMContentLoaded", function () {
  const input = document.getElementById("codeInput");

  if(input){
    input.addEventListener("animationend", function(){
      input.classList.remove("shake");
    });
  }
});



if (hasError) {
  const codeBox = document.getElementById("codeBox");
  const input = document.getElementById("codeInput");
  const error = document.getElementById("codeError");

  const userField = document.querySelector('[name="admin_username"]');
  const passField = document.querySelector('[name="admin_password"]');

  // show code box
  codeBox.style.display = "block";

  // show error message
  error.style.display = "block";

  // disable username & password (VERY IMPORTANT)
  userField.disabled = true;
  passField.disabled = true;

  // 🔥 FORCE SHAKE (this fixes your problem)
 input.style.animation = "none";
input.offsetHeight; // force reflow
input.style.animation = null;
input.classList.add("shake");
}

const lockIcon = document.getElementById("lockIcon");
const passwordField = document.querySelector('[name="admin_password"]');

// 🔥 glow when typing password
if(passwordField){
  passwordField.addEventListener("input", function(){
    if(this.value.length > 0){
      lockIcon.classList.add("active");
    } else {
      lockIcon.classList.remove("active");
    }
  });
}


if (hasError) {
   
   lockIcon.classList.add("lock-error");

   setTimeout(() => {
     lockIcon.classList.remove("lock-error");
   }, 400);
}