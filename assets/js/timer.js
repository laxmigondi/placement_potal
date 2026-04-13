document.addEventListener('DOMContentLoaded', function () {
    const timerEl = document.getElementById('timer');
    const formEl = document.getElementById('testForm');
    if (!timerEl || !formEl) return;

    let secondsLeft = parseInt(timerEl.dataset.seconds || '0', 10);
    function updateTimer() {
        const minutes = Math.floor(secondsLeft / 60);
        const seconds = secondsLeft % 60;
        timerEl.textContent = `${minutes}:${String(seconds).padStart(2, '0')}`;
        if (secondsLeft <= 0) {
            formEl.submit();
            return;
        }
        secondsLeft -= 1;
    }
    updateTimer();
    setInterval(updateTimer, 1000);
});
