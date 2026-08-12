document.addEventListener("DOMContentLoaded", function(){

    // all your JS here


/* 🔥 NEW VALIDATION (NO EMPTY BOOKINGS) */
document.getElementById("bookingForm").addEventListener("submit", function(e){

    let from = document.querySelector('[name="fromdate"]').value;
    let to = document.querySelector('[name="todate"]').value;
    let phone = document.querySelector('[name="phone"]').value;
let carElement = document.querySelector('[name="car"]');

let car = "";

if(carElement){
    car = carElement.value;
}

    // CHECK BUTTON CLICKED
    if(document.activeElement.name === "check"){
        if(from === "" || to === ""){
            alert("⚠ Select dates first");
            e.preventDefault();
        }
        return;
    }

    // FINAL BOOKING VALIDATION
    if(from === "" || to === ""){
        alert("⚠ Please select booking dates");
        e.preventDefault();
        return;
    }

    if(new Date(from) > new Date(to)){
        alert("⚠ From Date cannot be greater than To Date");
        e.preventDefault();
        return;
    }

if(!/^[0-9]+$/.test(phone)){
    alert("⚠ Phone must contain numbers only");
    e.preventDefault();
    return;
}

// Only check car when booking, not when checking availability
if(document.activeElement.value === "book"){

    if(car === ""){
        alert("⚠ Please select a car");
        e.preventDefault();
        return;
    }

}

      // ✅ CONFIRMATION POPUP (CORRECT PLACE)
    if(!confirm("Confirm booking?")){
        e.preventDefault();
        return;
    }


    // Calculate final price before sending
calculateTotal();

    // 🔥 NOW SEND TO insert_booking.php
    this.action = "insert_booking.php";
});

let today = new Date().toISOString().split("T")[0];
document.querySelector('[name="fromdate"]').min = today;
document.querySelector('[name="todate"]').min = today;

let carSelect = document.querySelector('[name="car"]');

if(carSelect){

    carSelect.addEventListener('change', function(){

        if(this.value !== ""){

            document.getElementById("bookBtn").disabled = false;

        }

    });

}



function calculateTotal(){

    let car = document.querySelector("select[name='car']");
    let from = document.querySelector("input[name='fromdate']").value;
    let to = document.querySelector("input[name='todate']").value;


    if(!car || car.value === "" || from === "" || to === ""){
        document.getElementById("totalPrice").innerText = "0";
        document.getElementById("totalInput").value = "0";
        return;
    }


    // Get price from option data-price
    let pricePerDay = parseFloat(
        car.options[car.selectedIndex].getAttribute("data-price")
    );


    let start = new Date(from);
    let end = new Date(to);


    let days = Math.ceil(
        (end - start) / (1000 * 60 * 60 * 24)
    );


    if(days <= 0){
        days = 1;
    }


    let total = days * pricePerDay;


    document.getElementById("totalPrice").innerText = total.toFixed(2);

    document.getElementById("totalInput").value = total.toFixed(2);

}

let fromInput = document.querySelector('[name="fromdate"]');
let toInput = document.querySelector('[name="todate"]');
let carInput = document.querySelector('[name="car"]');


if(fromInput){
    fromInput.addEventListener('change', calculateTotal);
}


if(toInput){
    toInput.addEventListener('change', calculateTotal);
}


if(carInput){
    carInput.addEventListener('change', calculateTotal);
}


});