// ===== SIDEBAR TOGGLE =====
function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  if (sidebar) sidebar.classList.toggle("collapsed");
}

// ===== FILTER SCRIPT =====
const searchInput = document.getElementById("searchInput");
const statusFilter = document.getElementById("statusFilter");
const dateFilter = document.getElementById("dateFilter");

function filterTable() {
  const search = searchInput?.value.toLowerCase() || "";
  const status = statusFilter?.value || "";
  const date = dateFilter?.value || "";

  const rows = document.querySelectorAll("table tbody tr");

  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    const rowStatus = row.getAttribute("data-status");
    const rowDate = row.getAttribute("data-date");

    const matchSearch = text.includes(search);
    const matchStatus = status === "" || rowStatus === status;
    const matchDate = date === "" || rowDate === date;

    row.style.display = (matchSearch && matchStatus && matchDate) ? "" : "none";
  });
}

// Attach events safely
if (searchInput) searchInput.addEventListener("keyup", filterTable);
if (statusFilter) statusFilter.addEventListener("change", filterTable);
if (dateFilter) dateFilter.addEventListener("change", filterTable);

// ===== CHART.JS DEFAULTS =====
Chart.defaults.animation = {
  duration: 1000,
  easing: 'easeOutQuart'
};

// ===== BAR CHART =====
const chart1 = document.getElementById("chart1");
if (chart1) {
  new Chart(chart1, {
    type: 'bar',
    data: {
      labels: ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
      datasets: [{
        label: 'Bookings',
        data: [200,150,180,220,170,250,300],
        backgroundColor: '#2563eb'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: { duration: 2000, easing: 'easeInOutQuart' }
    }
  });
}

// ===== PIE CHART =====
const chart2 = document.getElementById("chart2");
if (chart2) {
  new Chart(chart2, {
    type: 'pie',
    data: {
      labels: ['Sedan','SUV','Hatchback'],
      datasets: [{
        data: [30,40,30],
        backgroundColor: ['#2563eb','#22c55e','#f59e0b']
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: { animateRotate: true, animateScale: true, duration: 2000 }
    }
  });
}

// ===== LINE CHART (Revenue) =====
// IMPORTANT: monthlyData must be injected in PHP page like:
// <script>const monthlyData = <?php echo json_encode(array_values($monthlyData)); ?>;</script>
let earningsChart;
function loadChart() {
  if (typeof monthlyData === "undefined") return;

  const isDark = document.body.classList.contains('dark-mode');
  const ctx = document.getElementById('earningsSummary');
  if (!ctx) return;

  if (earningsChart) earningsChart.destroy();

  earningsChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
      datasets: [{
        label:'Revenue',
        data: monthlyData,
        borderColor:'orange',
        tension:0.4,
        fill:true
      }]
    },
    options: {
      responsive:true,
      maintainAspectRatio:false,
      animation:{ duration:2000, easing:'easeInOutQuart' },
      plugins:{
        tooltip:{
          backgroundColor: isDark ? '#222' : '#000',
          titleColor:'#fff',
          bodyColor:'#fff'
        },
        legend:{ labels:{ color: isDark ? '#fff' : '#333' } }
      },
      scales:{
        x:{ ticks:{ color: isDark ? '#fff' : '#666' } },
        y:{ ticks:{ color: isDark ? '#fff' : '#666' } }
      }
    }
  });
}

// ===== LIVE UPDATES =====
setInterval(() => {
  fetch('live_stats.php')
    .then(res => res.json())
    .then(data => {
      document.getElementById('revenue').innerText = "$" + data.revenue;
      document.getElementById('bookings').innerText = data.bookings;
      document.getElementById('rented').innerText = data.rented;
      document.getElementById('available').innerText = data.available;
    });
}, 10000);

setInterval(() => {
  fetch('notifications.php')
    .then(res => res.json())
    .then(data => {
      document.getElementById('notifCount').innerText = data.pending;
    });
}, 5000);

// ===== DARK MODE =====
function toggleDarkMode(){
  document.body.classList.toggle("dark-mode");
  localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
  loadChart();
}

// ===== ON LOAD =====
window.onload = function(){
  if(localStorage.getItem("theme") === "dark"){
    document.body.classList.add("dark-mode");
  }
  loadChart();
}

// ===== TOAST =====
function showToast(msg){
  const t = document.getElementById("toast");
  if (!t) return;
  t.innerText = msg;
  t.style.display = "block";
  setTimeout(()=>{ t.style.display = "none"; },3000);
}

// ===== URL PARAM CHECK =====
const params = new URLSearchParams(window.location.search);
if(params.get("msg") === "passwordchanged"){
  showToast("Password updated successfully 🔐");
}
