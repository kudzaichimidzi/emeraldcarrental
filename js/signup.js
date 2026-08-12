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
/*
document.addEventListener("DOMContentLoaded", function() {
  lottie.loadAnimation
  ({
    container: document.getElementById('signupAnimation'),
    renderer: 'svg',
    loop: true,
    autoplay: true,
path: 'admin/vehicleimages/business-team.json'    });
});*/
