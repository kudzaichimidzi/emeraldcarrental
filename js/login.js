window.onload = function () {
  document.querySelectorAll("input[type=email], input[type=password]")
    .forEach(inp => inp.value = "");
};


/* =========================
   PASSWORD TOGGLE
========================= */
function toggle(id, el) {
  const input = document.getElementById(id);
  const icon = el.querySelector("i");

  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    input.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}


/* =========================
   CANCEL MODAL
========================= */
function confirmCancel() {
  const modal = document.getElementById("cancelModal");
  modal.style.display = "flex";

  setTimeout(() => {
    modal.classList.add("show");
  }, 10);
}

function closeCancel() {
  const modal = document.getElementById("cancelModal");
  modal.classList.remove("show");

  setTimeout(() => {
    modal.style.display = "none";
  }, 300);
}


/* =========================
   CLICK OUTSIDE CLOSE
========================= */
window.onclick = function (event) {
  const modal = document.getElementById("cancelModal");

  if (event.target === modal) {
    closeCancel();
  }
};