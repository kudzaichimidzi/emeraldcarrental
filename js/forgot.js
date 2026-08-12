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

let chatOpen = false;

/* OPEN / CLOSE CHAT */
function toggleChat() {
  const chat = document.getElementById("chatbot");
  const toggle = document.querySelector(".chat-toggle");

  if (chatOpen) {
    chat.style.display = "none";
    chatOpen = false;
  } else {
    chat.style.display = "flex";
    chatOpen = true;
  }
}

/* SEND MESSAGE */
function sendMessage() {
  let input = document.getElementById("userInput");
  let text = input.value.trim();

  if (text === "") return;

  addMessage(text, "user");
  input.value = "";

  showTyping();

  setTimeout(() => {
    hideTyping();
    botReply(text);
  }, 1500);
}

/* ADD MESSAGE */
function addMessage(text, type) {
  let chatBody = document.getElementById("chatBody");

  let msg = document.createElement("div");
  msg.classList.add("message", type);
  msg.innerText = text;

  chatBody.appendChild(msg);
  chatBody.scrollTop = chatBody.scrollHeight;
}

/* BOT RESPONSE */
function botReply(userText) {
  let reply = "I can help you with bookings 🚗";

  if (userText.toLowerCase().includes("price")) {
    reply = "Our prices start from $20/day 🚗";
  } else if (userText.toLowerCase().includes("book")) {
    reply = "You can book a car from the Rent Car section!";
  } else if (userText.toLowerCase().includes("hello")) {
    reply = "Hello 👋 Welcome to EmeraldCars!";
  }

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
    let chatBody = document.getElementById("chatBody");

    let msg = document.createElement("div");
    msg.classList.add("message", "bot");
    msg.innerText = "👋 Hi! Welcome to EmeraldCars. How can I help you today?";

    chatBody.appendChild(msg);
  }, 3000);
};

document.getElementById("pass1").addEventListener("input", function() {
  const help = document.getElementById("passwordHelp");
  if (this.value.length < 8) {
    help.textContent = "Password too short (min 8 chars)";
    help.style.color = "red";
  } else {
    help.textContent = "Strong enough ✔";
    help.style.color = "green";
  }
});

document.addEventListener("DOMContentLoaded", function() {
  const pass1 = document.getElementById("pass1");
  const help = document.getElementById("passwordHelp");

  pass1.addEventListener("input", function() {
    const val = this.value;

    // Check length
    if (val.length < 8) {
      help.textContent = "Password too short (min 8 chars)";
      help.style.color = "red";
    } 
    // Check complexity
    else if (!/[A-Z]/.test(val) || !/[0-9]/.test(val) || !/[!@#$%^&*]/.test(val)) {
      help.textContent = "Add uppercase, number & symbol for stronger password";
      help.style.color = "orange";
    } 
    else {
      help.textContent = "Strong enough ✔";
      help.style.color = "green";
    }
  });
});
