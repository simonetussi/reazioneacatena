const startButton = document.getElementById('start');
const scoreDisplay = document.getElementById('score');
const lastScoreDisplay = document.getElementById('lastScore');
const bestScoreDisplay = document.getElementById('bestScore');
const nicknameInput = document.getElementById('nickname');
let isGameActive = false;

document.querySelectorAll('.color').forEach(btn => {
    btn.style.opacity = '0.3';
    btn.addEventListener('click', handleColorClick);
    btn.addEventListener('mousedown', () => btn.style.opacity = '1');
    btn.addEventListener('mouseup', () => btn.style.opacity = '0.3');
    btn.addEventListener('mouseleave', () => btn.style.opacity = '0.3');
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
    setTimeout(() => btn.style.opacity = '0.3', 500);
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
        
        const text = await response.text();
        const data = JSON.parse(text);
        
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
        gameOver(0);
    }
}

function gameOver(score) {
    isGameActive = false;
    startButton.disabled = false;
    enableButtons();
    
    lastScoreDisplay.textContent = score;
    const currentBest = parseInt(bestScoreDisplay.textContent) || 0;
    bestScoreDisplay.textContent = Math.max(score, currentBest);
    
    document.querySelector('.game-container').classList.add('error');
    setTimeout(() => {
        document.querySelector('.game-container').classList.remove('error');
        document.querySelector('.game-container').classList.add('game-over');
        setTimeout(() => {
            document.querySelector('.game-container').classList.remove('game-over');
        }, 1000);
    }, 500);
}

startButton.addEventListener('click', async () => {
    if (isGameActive) return;
    
    const nickname = nicknameInput.value.trim();
    if (!nickname) {
        alert('Inserisci un nickname valido!');
        return;
    }
    
    isGameActive = true;
    startButton.disabled = true;
    scoreDisplay.textContent = '0';
    
    try {
        const response = await fetch('/reazioneacatena/startGame.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nickname })
        });
        
        const data = await response.json();
        playSequence(data.sequence);
    } catch (error) {
        console.error('Errore:', error);
        isGameActive = false;
        startButton.disabled = false;
    }
});