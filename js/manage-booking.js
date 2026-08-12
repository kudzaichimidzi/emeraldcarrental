
/* VIEW (FROM B) */
function viewBooking(data){
document.getElementById("modalBody").innerHTML=`
<p><b>User:</b> ${data.userEmail}</p>
<p><b>Car:</b> ${data.VehiclesTitle}</p>
<p><b>Brand:</b> ${data.VehiclesBrand}</p>
<p><b>From:</b> ${data.FromDate}</p>
<p><b>To:</b> ${data.ToDate}</p>
<p><b>Message:</b> ${data.message}</p>
`;
new bootstrap.Modal(document.getElementById('bookingModal')).show();
}

/* STATUS UPDATE (AJAX) */
function updateStatus(id,status){
fetch("update_booking.php?id="+id+"&status="+status)
.then(r=>r.text())
.then(d=>{
if(d.trim()=="success"){
toast("Updated 🚀");
setTimeout(()=>location.reload(),500);
}else{
toast("Failed ❌");
}
});
}

/* SEARCH */
function searchTable(){
let v=document.getElementById("search").value.toLowerCase();
document.querySelectorAll("#bookingTable tbody tr").forEach(r=>{
r.style.display=r.innerText.toLowerCase().includes(v)?"":"none";
});
}

/* NOTIFICATION TOGGLE */
function toggleNotif(){
let box=document.getElementById("notifBox");
box.style.display = box.style.display==="none"?"block":"none";
}

/* LIVE UPDATE (FROM BOTH) */
setInterval(()=>{
fetch("live_stats.php")
.then(r=>r.json())
.then(d=>{
document.getElementById("notifCount").innerText=d.pending;
document.getElementById("pendingBadge").innerText=d.pending;
});
},4000);

/* TOAST */
function toast(msg){
let t=document.createElement("div");
t.className="toast-box";
t.innerText=msg;
document.body.appendChild(t);
setTimeout(()=>t.remove(),2000);
}

setInterval(() => {
    location.reload();
}, 10000);