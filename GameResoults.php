<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $players = $_POST['player'];
    $balances = array_map('intval', $_POST['balance']);
    $bets = array_map('intval', $_POST['bet']);
    $numDice = (int)$_POST['stKock'];
    $numGames = (int)$_POST['stIger'];

    $roundDetails = [];
    $finalBalances = [];

    for ($i = 0; $i < count($players); $i++) {
        $finalBalances[$players[$i]] = $balances[$i];
    }

    for ($game = 1; $game <= $numGames; $game++) {
        $roundScores = [];
        $diceRolls = [];

        foreach ($players as $index => $player) {
            $rolls = [];
            $total = 0;
            for ($d = 0; $d < $numDice; $d++) {
                $roll = rand(1, 6);
                $total += $roll;
                $rolls[] = $roll;
            }
            $roundScores[$player] = $total;
            $diceRolls[$player] = $rolls;
        }

        $maxScore = max($roundScores);
        $winners = array_keys($roundScores, $maxScore);

        foreach ($players as $index => $player) {
            $bet = $bets[$index];
            if (in_array($player, $winners)) {
                if (count($winners) === 1) {
                    $finalBalances[$player] += $bet; // won + own bet back
                } else {
                    // Tie — return bet only
                }
            } else {
                $finalBalances[$player] -= $bet;
            }
        }

        $roundDetails[] = [
            'round' => $game,
            'scores' => $roundScores,
            'dice' => $diceRolls,
            'winners' => $winners
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Results</title>
    <style>
    /* Bright Animated Background */
    body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #f9d423, #ff4e50, #a044ff, #44bd32);
        background-size: 400% 400%;
        animation: backgroundShift 12s ease infinite;
        color: #fff;
        min-height: 100vh;
        padding: 2rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    @keyframes backgroundShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Heading */
    h1 {
        font-size: 2.5rem;
        color: #f1c40f;
        margin-bottom: 2rem;
    }

    h2 {
        margin-top: 2rem;
        color: #ffffff;
    }

    /* Table Styling */
    table {
        width: 90%;
        max-width: 900px;
        margin: 1.5rem auto;
        border-collapse: collapse;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        overflow: hidden;
        backdrop-filter: blur(10px);
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.15);
    }

    th, td {
        padding: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        color: #fdfdfd;
    }

    th {
        background-color: rgba(255, 255, 255, 0.15);
        color: #f1c40f;
        font-weight: bold;
        text-transform: uppercase;
    }

    td {
        background-color: rgba(255, 255, 255, 0.05);
    }

    /* Dice display */
    .dice {
        display: flex;
        justify-content: center;
        gap: 6px;
    }

    .dice img {
        height: 35px;
        width: 35px;
        transition: transform 0.2s ease;
    }

    .dice img:hover {
        transform: scale(1.2);
    }

    /* Winner styling */
    .winner {
        color: #f39c12;
        font-size: 1.4rem;
        font-weight: bold;
        margin-top: 1rem;
    }

    /* Final Balances */
    .final {
        margin-top: 2rem;
        font-size: 1.5rem;
        color: #1abc9c;
    }

    .final ul {
        list-style: none;
        padding: 0;
    }

    .final li {
        margin: 0.4rem 0;
        font-weight: bold;
    }

    /* Responsive */
    @media (max-width: 700px) {
        table {
            font-size: 0.9rem;
        }

        h1 {
            font-size: 2rem;
        }

        .dice img {
            height: 28px;
            width: 28px;
        }
    }
    .startBtn {
    margin-top: 1rem;
    padding: 0.8rem 2rem;
    font-size: 1.1rem;
    font-weight: bold;
    border: none;
    border-radius: 12px;
    background: linear-gradient(45deg, #e67e22, #f39c12);
    color: #fff;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
}

.startBtn:hover {
    background: linear-gradient(45deg, #f1c40f, #e67e22);
    transform: scale(1.05);
}

</style>

</head>
<body>

    <h1>Dice Game Results</h1>

    <?php foreach ($roundDetails as $round): ?>
        <h2>Round <?= $round['round'] ?></h2>
        <table>
            <thead>
                <tr>
                    <th>Player</th>
                    <th>Dice Rolled</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($round['scores'] as $player => $score): ?>
                    <tr>
                        <td><?= htmlspecialchars($player) ?></td>
                        <td>
                            <div class="dice">
                                <?php foreach ($round['dice'][$player] as $roll): ?>
                                    <img src="images/dice<?= $roll ?>.png" alt="Dice <?= $roll ?>">
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td><?= $score ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="winner">
            <?php if (count($round['winners']) === 1): ?>
                Winner: <?= htmlspecialchars($round['winners'][0]) ?>
            <?php else: ?>
                Tie between: <?= implode(', ', array_map('htmlspecialchars', $round['winners'])) ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <div class="final">
        <h2>Final Balances</h2>
        <ul>
            <?php foreach ($finalBalances as $player => $balance): ?>
                <li><?= htmlspecialchars($player) ?>: $<?= $balance ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    

    <div class="final">
        <p>You will be redirected to the main page in <span id="countdown">10</span> seconds...</p>
        <button onclick="location.href='index.php'" class="startBtn">Play Again Now</button>
    </div>

    <script>
        let seconds = 10;
        const countdownElement = document.getElementById('countdown');
        const countdownInterval = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(countdownInterval);
                window.location.href = 'index.php';
            }
        }, 1000);
    </script>

</body>
</html>
