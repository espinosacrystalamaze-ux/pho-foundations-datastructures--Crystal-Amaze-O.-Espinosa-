<?php
$file = 'books.json';
if (!file_exists($file)) die("Error: Missing books.json");

$data = json_decode(file_get_contents($file), true);
if (!is_array($data)) die("Error: Invalid JSON format");

function showBooks($id, $title, $books) {
  if (empty($books)) return;

  echo "<section class='category' id='cat_$id'>
          <div class='cat-header'>
            <h2>".htmlspecialchars($title)."</h2>
            <div class='scroll-buttons'>
              <button onclick=\"scrollRow('$id', -1)\"><i>&#10094;</i></button>
              <button onclick=\"scrollRow('$id', 1)\"><i>&#10095;</i></button>
            </div>
          </div>
          <div class='book-list' id='$id'>";

  foreach ($books as $b) {
    $img = htmlspecialchars($b['imageLink'] ?? 'default.jpg');
    $titleTxt = htmlspecialchars($b['title'] ?? 'Untitled');
    $author = htmlspecialchars($b['author'] ?? 'Unknown');
    $year = htmlspecialchars($b['year'] ?? '');

    echo "<div class='book-card' onclick='showPopup(this)' 
            data-title='$titleTxt' data-author='$author' 
            data-year='$year' data-img='images/$img'>
            <div class='book-cover'>
              <img src='images/$img' alt='$titleTxt'>
            </div>
            <div class='book-info'>
              <h3>$titleTxt</h3>
              <p>$author".($year ? " • $year" : "")."</p>
            </div>
          </div>";
  }

  echo "</div></section>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Ducoments</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

* { box-sizing: border-box; }

body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  color: #fff0f7;
  background: linear-gradient(135deg, #e0aaff, #ff9ec7, #b388ff, #ffb6e0);
  background-size: 400% 400%;
  animation: gradientFlow 12s ease infinite;
  min-height: 100vh;
  overflow-x: hidden;
}

/* Animated gradient */
@keyframes gradientFlow {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

/* Header */
header {
  text-align: center;
  padding: 50px 20px 20px;
}
header h1 {
  font-size: 2.6em;
  color: #ffe6ff;
  text-shadow: 0 0 20px #ffb6ff, 0 0 40px #c89bff;
  letter-spacing: 2px;
}

/* Search Bar */
.search-bar {
  text-align: center;
  margin: 25px 0 40px;
}
.search-bar input {
  width: 60%;
  max-width: 400px;
  padding: 12px 18px;
  border-radius: 40px;
  border: none;
  font-size: 16px;
  outline: none;
  color: #fff;
  background: rgba(255, 255, 255, 0.15);
  box-shadow: inset 0 0 10px rgba(255,255,255,0.25), 0 0 10px #d8a6ff;
  backdrop-filter: blur(10px);
  transition: 0.3s;
}
.search-bar input:focus {
  background: rgba(255,182,255,0.3);
  box-shadow: 0 0 20px #ff9eff, 0 0 30px #bb86fc;
}

/* Category */
.category {
  padding: 20px 30px 60px;
  position: relative;
}
.cat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}
.cat-header h2 {
  color: #ffe0ff;
  font-size: 1.6em;
  text-shadow: 0 0 10px #e5b3ff, 0 0 20px #ffbdf4;
}

/* Scroll Buttons */
.scroll-buttons button {
  background: rgba(255,255,255,0.2);
  border: none;
  color: #fff;
  font-size: 22px;
  border-radius: 50%;
  width: 40px; height: 40px;
  margin: 0 4px;
  cursor: pointer;
  transition: 0.3s;
}
.scroll-buttons button:hover {
  background: linear-gradient(135deg, #ff99ff, #b388ff);
  color: #fff;
  box-shadow: 0 0 12px #ffbfff, 0 0 20px #b388ff;
}

/* Book List */
.book-list {
  display: flex;
  overflow-x: auto;
  scroll-behavior: smooth;
  gap: 25px;
  padding-bottom: 10px;
}
.book-list::-webkit-scrollbar { display: none; }

/* Book Card */
.book-card {
  flex: 0 0 auto;
  width: 190px;
  border-radius: 16px;
  background: rgba(255,255,255,0.15);
  box-shadow: 0 4px 20px rgba(170,0,255,0.3);
  overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
  cursor: pointer;
  backdrop-filter: blur(8px);
}
.book-card:hover {
  transform: translateY(-8px) scale(1.05);
  box-shadow: 0 0 25px #ff9eff, 0 0 30px #b388ff;
}
.book-cover img {
  width: 100%;
  height: 260px;
  object-fit: cover;
}
.book-info {
  text-align: center;
  padding: 12px;
}
.book-info h3 {
  font-size: 15px;
  color: #fff;
  margin: 6px 0;
  height: 38px;
  overflow: hidden;
}
.book-info p {
  font-size: 13px;
  color: #f2d6ff;
}

/* Popup */
.popup {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(80,0,120,0.4);
  justify-content: center;
  align-items: center;
  z-index: 10;
  backdrop-filter: blur(12px);
}
.popup.active { display: flex; }

.popup-content {
  background: rgba(255,255,255,0.15);
  padding: 25px;
  border-radius: 20px;
  width: 340px;
  text-align: center;
  color: #fff;
  backdrop-filter: blur(18px);
  box-shadow: 0 0 30px rgba(180,0,255,0.5);
  animation: fadeIn 0.3s ease;
}
.popup-content img {
  width: 100%;
  border-radius: 10px;
  margin-bottom: 12px;
}
.popup-content h3 { color: #ffbdf4; margin: 10px 0; }
.popup-content p { color: #f2d6ff; margin: 4px 0; }

/* Close Button */
.close-btn {
  position: absolute;
  top: 15px; right: 20px;
  background: linear-gradient(135deg, #ff9edf, #b388ff);
  border: none;
  color: #fff;
  width: 30px; height: 30px;
  border-radius: 50%;
  font-size: 18px;
  cursor: pointer;
  transition: 0.3s;
  box-shadow: 0 0 10px #ffb6ff;
}
.close-btn:hover { background: #e0aaff; }

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}

@media (max-width: 600px) {
  .book-card { width: 150px; }
  .book-cover img { height: 200px; }
  .search-bar input { width: 80%; }
}
</style>
</head>
<body>

<header>
  <h1> ESPINOSA LIBRARY </h1>
</header>

<div class="search-bar">
  <input type="text" id="searchInput" placeholder="Search your favorite books...">
</div>

<?php
  showBooks("history", "History Books", $data["history_books"] ?? []);
  showBooks("adventure", "Adventure Books", $data["adventure_books"] ?? []);
  showBooks("action", "Action Books", $data["action_books"] ?? []);
?>

<div class="popup" id="bookPopup">
  <div class="popup-content">
    <button class="close-btn" onclick="closePopup()">×</button>
    <img id="popupImg" src="">
    <h3 id="popupTitle"></h3>
    <p id="popupAuthor"></p>
    <p id="popupYear"></p>
  </div>
</div>

<script>
function scrollRow(id, dir) {
  document.getElementById(id).scrollBy({ left: dir * 250, behavior: 'smooth' });
}

function showPopup(card) {
  document.getElementById("bookPopup").classList.add("active");
  document.getElementById("popupImg").src = card.dataset.img;
  document.getElementById("popupTitle").innerText = card.dataset.title;
  document.getElementById("popupAuthor").innerText = "By " + card.dataset.author;
  document.getElementById("popupYear").innerText = "Published: " + card.dataset.year;
}
function closePopup() {
  document.getElementById("bookPopup").classList.remove("active");
}

document.getElementById("searchInput").addEventListener("input", function() {
  let query = this.value.toLowerCase();
  document.querySelectorAll(".book-card").forEach(c => {
    let text = (c.dataset.title + c.dataset.author).toLowerCase();
    c.style.display = text.includes(query) ? "block" : "none";
  });
});
</script>
</body>
</html>
