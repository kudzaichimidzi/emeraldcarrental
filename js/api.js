/*async function loadPage(page) {

  const content = document.getElementById("app-content");

  // loading state
  content.innerHTML = "<p style='color:white;'>Loading...</p>";

  try {

    const res = await fetch("api.php?page=" + page);
    const data = await res.json();

    if (data.status === "success") {
      document.title = data.title + " | Emerald Cars";

      content.innerHTML = `
        <div class="section-fade">
          ${data.html}
        </div>
      `;
    } else {
      content.innerHTML = "<h2>Error loading page</h2>";
    }

  } catch (error) {
    content.innerHTML = "<h2>Server Error</h2>";
  }
}

// auto-load home on start
window.onload = () => {
  loadPage("home");
};*/