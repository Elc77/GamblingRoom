<?php
session_start();

$currentStep = isset($_POST['step']) ? (int)$_POST['step'] : 0;

switch ($currentStep) {
  case 1:
    $_SESSION['totalPlayers'] = (int)$_POST['num_players'];
    $_SESSION['totalRounds'] = (int)$_POST['num_rounds'];
    $_SESSION['diceCount'] = (int)$_POST['num_dice'];
    break;

  case 2:
    if (!empty($_POST['ime'])) {
      $_SESSION['participants'] = [];
      foreach ($_POST['ime'] as $index => $playerName) {
        $_SESSION['participants'][] = [
          'ime' => $playerName,
          'sum' => 0
        ];
      }
      $_SESSION['roundIndex'] = 1;
    } elseif (isset($_POST['next_round'])) {
      $_SESSION['roundIndex']++;
    }
    break;
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
  <meta charset="UTF-8">
  <title>Gambling Room</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="images/dice3.png" type="image/png">
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

</head>
<body>
<div class="center-box">
  <h1 class="main-title">Gambling Room</h1>

  <?php if ($currentStep === 0): ?>
    <form method="post">
      <div class="selection-row">
        <?php
        $options = [
          'num_players' => [1, 6, 'Number of players:'],
          'num_rounds' => [1, 5, 'Number of Rounds:'],
          'num_dice' => [1, 5, 'Number of Dices:']
        ];
        foreach ($options as $name => [$start, $end, $label]): ?>
          <div class="selection-item">
            <label><?= $label ?></label>
            <select name="<?= $name ?>">
              <?php for ($i = $start; $i <= $end; $i++) echo "<option value='$i'>$i</option>"; ?>
            </select>
          </div>
        <?php endforeach; ?>
      </div>
      <input type="hidden" name="step" value="1">
      <button type="submit">Continue</button>
    </form>

  <?php elseif ($currentStep === 1): ?>
    <form method="post">
      <div class="fieldset-row">
        <?php for ($i = 0; $i < $_SESSION['totalPlayers']; $i++): ?>
          <fieldset>
            <legend>Player <?= $i + 1 ?></legend>
            <label>Player Name:</label><br>
            <input type="text" name="ime[]" required><br><br>
          </fieldset>
        <?php endfor; ?>
      </div>
      <input type="hidden" name="step" value="2">
      <button type="submit">Start Game</button>
    </form>

  <?php elseif ($currentStep === 2): ?>
    <?php
  $curr = $_SESSION['roundIndex'];
  $max = $_SESSION['totalRounds'];

  if ($curr > $max) {
    echo '<form method="post"><input type="hidden" name="step" value="3">';
    echo '<script>document.forms[0].submit();</script></form>';
    exit;
  }
  ?>
  
  <h2 class="round-heading">🎲 Round <?= $curr ?> of <?= $max ?></h2>

  <section class="players-round">
    <?php foreach ($_SESSION['participants'] as $index => &$player): ?>
      <?php
      $rolls = [];
      for ($k = 0; $k < $_SESSION['diceCount']; $k++) $rolls[] = rand(1, 6);
      $score = array_sum($rolls);
      $player['sum'] += $score;
      ?>
      <div class="user-box fade-in">
        <h3 class="player-title">Player <?= $index + 1 ?>: <?= htmlspecialchars($player['ime']) ?></h3>
        
        <div class="dice-roll-container">
          <div class="dice-visuals">
            <?php foreach ($rolls as $value): ?>
              <img class="dice" data-result="<?= $value ?>" src="images/dice-anim.gif" alt="Dice">
            <?php endforeach; ?>
          </div>
          <div class="dice-sum" style="display:none;">
             This round: <strong><?= $score ?></strong><br>
             Total: <strong><?= $player['sum'] ?></strong>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </section>

  <div id="round-buttons" style="display:none; margin-top: 30px;">
    <form method="post">
      <input type="hidden" name="step" value="<?= ($curr < $max) ? 2 : 3 ?>">
      <?php if ($curr < $max): ?>
        <button type="submit" name="next_round">➡️ Next Round</button>
      <?php else: ?>
        <button type="submit">🎉 Show Results</button>
      <?php endif; ?>
    </form>
  </div>

<?php elseif ($currentStep === 3): ?>
  <h2>🏆 Podium</h2>

  <?php
  $standings = $_SESSION['participants'];
  usort($standings, fn($a, $b) => $b['sum'] <=> $a['sum']);
  $topScore = $standings[0]['sum'];
  $champions = array_filter($standings, fn($p) => $p['sum'] === $topScore);
  ?>

  <table class="leaderboard-table">
    <thead>
      <tr><th>#</th><th>Player</th><th>Points</th></tr>
    </thead>
    <tbody>
      <?php foreach ($standings as $pos => $player): ?>
        <tr>
          <td><?= $pos + 1 ?></td>
          <td><?= htmlspecialchars($player['ime']) ?></td>
          <td><?= $player['sum'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Confetti 🎉 -->
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
  <script>
    setTimeout(() => {
      confetti({
        particleCount: 200,
        spread: 90,
        origin: { y: 0.6 }
      });
    }, 500);
  </script>

  <!-- Winner Announcement -->
  <div class="winner-box fade-in">
    <?php if (count($champions) === 1): ?>
      <h3>🏅 Champion: <?= htmlspecialchars(reset($champions)['ime']) ?></h3>
    <?php else: ?>
      <h3>🥇 Winners:</h3>
      <?php foreach ($champions as $winner): ?>
        <?= htmlspecialchars($winner['ime']) ?><br>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Auto-Redirect -->
  <p id="redirect-timer">
    Redirecting in <span id="countdown">10</span> seconds...
  </p>
  <script>
    let sec = 10, counter = document.getElementById("countdown");
    setInterval(() => { if (sec > 0) counter.textContent = --sec; }, 1000);
    setTimeout(() => location.href = 'index.php', 10000);
  </script>
<?php endif; ?>


  <!-- RE-ADDED buttons to prevent JS failure -->
 

</div>
<script src="js/script.js"></script>
</body>
</html>
