document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Animate Progress Bars on Page Load
    const progressBars = document.querySelectorAll('.progress-bar');
    
    progressBars.forEach(bar => {
        // Store the target width from the style attribute
        const targetWidth = bar.style.width;
        // Reset to 0 initially
        bar.style.width = '0';
        
        // Animate to target width with a small delay
        setTimeout(() => {
            bar.style.transition = 'width 1.5s cubic-bezier(0.1, 0.7, 1.0, 0.1)';
            bar.style.width = targetWidth;
        }, 300);
    });

    // 2. Interactive Buttons (Simulation)
    const buttons = document.querySelectorAll('.btn-start');
    
    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Visual feedback
            const originalText = this.innerText;
            this.innerText = 'Loading...';
            this.style.opacity = '0.7';

            setTimeout(() => {
                alert("Mission Loading! ");
                this.innerText = originalText;
                this.style.opacity = '1';
                
                // Simulate earning points occasionally
                addPoints(10);
            }, 800);
        });
    });

    // 3. Simple Point System Simulation
    let points = 160;
    const pointsDisplay = document.getElementById('points-display');

    function addPoints(amount) {
        // Number counting animation
        const start = points;
        const end = points + amount;
        const duration = 1000;
        const startTime = performance.now();

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function for smooth count
            const easeOut = 1 - Math.pow(1 - progress, 3);
            
            points = Math.floor(start + (end - start) * easeOut);
            pointsDisplay.innerText = points;

            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }
        
        requestAnimationFrame(update);
    }
});