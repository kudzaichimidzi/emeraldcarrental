// DARK MODE

function darkMode(){

    document.body.classList.toggle("dark");

}



// SIMPLE TABLE SEARCH

let searchBox = document.getElementById("searchInput");


if(searchBox){

searchBox.addEventListener("keyup", function(){

    let value = this.value.toLowerCase();


    let rows = document.querySelectorAll("table tbody tr");


    rows.forEach(function(row){


        let text = row.innerText.toLowerCase();


        if(text.includes(value)){

            row.style.display="";

        }

        else{

            row.style.display="none";

        }


    });


});


}





// CARD NUMBER ANIMATION

let numbers = document.querySelectorAll(".card h1");


numbers.forEach(function(number){


let value = parseInt(number.innerText);


if(!isNaN(value)){


let count = 0;


let timer = setInterval(function(){


count++;


number.innerText = count;


if(count >= value){

clearInterval(timer);

}


},20);



}


});


// PROFILE HOVER

let profile=document.querySelector(".profile-card");


if(profile){

profile.addEventListener("click",function(){

alert("Admin profile");

});

}