<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Gambling Room</title>
</head>
<body>
    <div class="all">
    <form action="GameResoults.php" method="post" autocomplete="off" id="gameForm">
    <h1>Gambling Room</h1>
    <div class="container">
        <div id="players-container">
            <?php for ($i = 1; $i <= 2; $i++): ?>
                <div class="player-block" id="player-block-<?= $i ?>">
                    <label>Player <?= $i ?> Name:</label>
                    <input type="text" name="player[]" placeholder="Enter name" maxlength="10" required>

                    <label>Player <?= $i ?> Starting Balance:</label>
                    <input type="number" name="balance[]" placeholder="e.g. 100" min="0" required onkeydown="return preventInvalidInput(event)">


                    <label>Player <?= $i ?> Bet per Game:</label>
                    <input type="number" name="bet[]" placeholder="e.g. 10" min="1" required onkeydown="return preventInvalidInput(event)">
                </div>
            <?php endfor; ?>
        </div>

        <div class="btn-controls">
            <button type="button" id="add-player">+ Add Player</button>
            <button type="button" id="remove-player">– Remove Player</button>
        </div>

        <label>Number of Dice:</label>
        <select name="stKock" required>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
        </select>

        <label>Number of Rounds:</label>
        <select name="stIger" required>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <input class="startBtn" type="submit" value="Start Game">
</form>

<script>
    const playersContainer = document.getElementById('players-container');
    const addBtn = document.getElementById('add-player');
    const removeBtn = document.getElementById('remove-player');

    let playerCount = 2;
    const maxPlayers = 4;
    const minPlayers = 2;

    function updateButtonStates() {
        addBtn.disabled = playerCount >= maxPlayers;
        removeBtn.disabled = playerCount <= minPlayers;
    }

    addBtn.addEventListener('click', () => {
        if (playerCount < maxPlayers) {
            playerCount++;

            const div = document.createElement('div');
            div.className = 'player-block';
            div.id = `player-block-${playerCount}`;
            div.innerHTML = `
                <label>Player ${playerCount} Name:</label>
                <input type="text" name="player[]" placeholder="Enter name" maxlength="10" required>

                <label>Player ${playerCount} Starting Balance:</label>
                <input type="number" name="balance[]" placeholder="e.g. 100" min="0" required>

                <label>Player ${playerCount} Bet per Game:</label>
                <input type="number" name="bet[]" placeholder="e.g. 10" min="1" required>
            `;
            playersContainer.appendChild(div);

            updateButtonStates();
        }
    });

    removeBtn.addEventListener('click', () => {
        if (playerCount > minPlayers) {
            const toRemove = document.getElementById(`player-block-${playerCount}`);
            if (toRemove) {
                playersContainer.removeChild(toRemove);
                playerCount--;
                updateButtonStates();
            }
        }
    });

    // Initialize on load
    updateButtonStates();
</script>

<script>
    function preventInvalidInput(e) {
        const invalidKeys = ["e", "E", "+", "-", ".", ","];
        if (invalidKeys.includes(e.key)) {
            e.preventDefault();
            return false;
        }
        return true;
    }
</script>



    </div>
</body>
</html>
