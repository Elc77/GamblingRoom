// dice animation 
function showDiceResults() {
  setTimeout(function () {
    document.querySelectorAll(".dice").forEach(function (die) {
      let res = die.getAttribute("data-result");
      die.src = "images/dice" + res + ".png";
    });
    document.querySelectorAll(".dice-sum").forEach(el => el.style.display = "block");
    let btns = document.getElementById("round-buttons");
    if (btns) btns.style.display = "block";
  }, 1500);
}

// Ensure function runs on every page load even with same-session flow
document.addEventListener("DOMContentLoaded", showDiceResults);
