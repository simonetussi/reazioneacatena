const startButton = document.getElementById('start');
const scoreDisplay = document.getElementById('score');
const lastScoreDisplay = document.getElementById('lastScore');
const bestScoreDisplay = document.getElementById('bestScore');
const nicknameInput = document.getElementById('nickname');
let isGameActive = false;

// Inizializza best score dal localStorage
let bestScore = localStorage.getItem('bestScore') || 0;
bestScoreDisplay.textContent = bestScore;

document.querySelectorAll('.color').forEach(btn => {
    btn.style.opacity = '0.3';
    btn.addEventListener('click', handleColorClick);
    
    // Migliorata interazione hover/touch
    btn.addEventListener('mousedown', () => btn.style.opacity = '1');
    btn.addEventListener('mouseup', () => btn.style.opacity = '0.3');
    btn.addEventListener('mouseleave', () => btn.style.opacity = '0.3');
    btn.addEventListener('touchstart', () => btn.style.opacity = '1');
    btn.addEventListener('touchend', () => btn.style.opacity = '0.3');
});

function playSequence(sequence) {
    disableButtons();
    
    let i = 0;
    const playNext = () => {
        if (i >= sequence.length) {
            enableButtons();
            return;
        }
        
        flashColor(sequence[i]);
        i++;
        setTimeout(playNext, 800);
    };
    
    playNext();
}

function flashColor(color) {
    const btn = document.getElementById(color);
    btn.style.opacity = '1';
    setTimeout(() => {
        if (btn.style.opacity === '1') btn.style.opacity = '0.3';
    }, 500);
}

function disableButtons() {
    document.querySelectorAll('.color').forEach(btn => {
        btn.style.pointerEvents = 'none';
    });
}

function enableButtons() {
    document.querySelectorAll('.color').forEach(btn => {
        btn.style.pointerEvents = 'auto';
    });
}

async function handleColorClick(e) {
    if (!isGameActive) return;
    
    const color = e.target.id;
    flashColor(color);
    
    try {
        const response = await fetch('/reazioneacatena/handleClick.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ color })
        });
        
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const data = await response.json();
        
        if (data.status === 'gameOver') {
            gameOver(data.score);
        } else {
            scoreDisplay.textContent = data.score;
            if (data.sequence) {
                disableButtons();
                setTimeout(() => playSequence(data.sequence), 1000);
            }
        }
    } catch (error) {
        console.error('Errore:', error);
        gameOver(scoreDisplay.textContent);
    }
}

async function gameOver(score) {
    isGameActive = false;
    startButton.disabled = false;
    enableButtons();
    
    // Aggiorna last score
    lastScoreDisplay.textContent = score;
    
    try {
        const updateResponse = await fetch('/reazioneacatena/updateScore.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                nickname: nicknameInput.value.trim(),
                score: parseInt(score)
            })
        });

        const updateData = await updateResponse.json();
        
        if (!updateResponse.ok || !updateData.success) {
            throw new Error(updateData.error || 'Errore sconosciuto');
        }

        // Aggiorna best score
        const newBest = parseInt(updateData.newBestScore) || bestScore;
        if (newBest > bestScore) {
            bestScore = newBest;
            localStorage.setItem('bestScore', bestScore);
            bestScoreDisplay.textContent = bestScore;
        }

    } catch (error) {
        console.error('Errore aggiornamento punteggio:', error);
        alert(`Errore salvataggio punteggio: ${error.message}`);
    }

    // Animazione game over
    const gameContainer = document.querySelector('.game-container');
    gameContainer.classList.add('shake');
    
    setTimeout(() => {
        gameContainer.classList.remove('shake');
    }, 1000);
}

startButton.addEventListener('click', async () => {
    if (isGameActive) return;
    startButton.disabled = true;
    
    const nickname = nicknameInput.value.trim();
    if (!nickname) {
        alert('Inserisci un nickname');
        startButton.disabled = false;
        return;
    }

    try {
        const response = await fetch('/reazioneacatena/checkNickname.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nickname })
        });

        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.error || 'Errore di connessione');
        }

        if (!data.exists) {
            if (confirm('Nickname non registrato. Vuoi registrarti?')) {
                window.location.href = 'playerRegister.php';
            }
            return;
        }

        // Inizia il gioco
        isGameActive = true;
        scoreDisplay.textContent = '0';
        bestScoreDisplay.textContent = Math.max(data.bestScore || 0, bestScore);
        
        const gameStart = await fetch('/reazioneacatena/startGame.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nickname })
        });

        const gameData = await gameStart.json();
        
        if (!gameData.sequence) {
            throw new Error('Errore inizializzazione gioco');
        }

        playSequence(gameData.sequence);

    } catch (error) {
        console.error('Errore:', error);
        alert(error.message);
        isGameActive = false;
        startButton.disabled = false;
    }
});