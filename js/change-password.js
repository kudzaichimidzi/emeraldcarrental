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