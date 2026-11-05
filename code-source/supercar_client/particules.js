// Canvas
const particules = document.getElementById("particule");
const ctx = particules.getContext("2d");// utilisation en 2D du canvas

particules.width = particules.offsetWidth;
particules.height = particules.offsetHeight;

// Classe Particule (braise)
class Particule {
    constructor(x, y) {
        this.x = x;
        this.y = y;
        this.size = Math.random() * 3 + 2; 
        this.vx = (Math.random() - 0.5) * 0.5; 
        this.vy = -Math.random() * 1 - 0.5; 
        this.color = `rgba(${200 + Math.random()*55}, ${50 + Math.random()*100}, 0, ${0.7 + Math.random()*0.3})`; // couleur orangée avec variation generer par IA
    }

    draw() {
        ctx.beginPath();// commence le dessin
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fillStyle = this.color;
        ctx.shadowColor = this.color;
        ctx.shadowBlur = 10; // glow (generer par IA)
        ctx.fill();
    }
    update() {
        this.x += this.vx;
        this.y += this.vy;

        // Réapparaît en bas si sort du canvas
        if (this.y < 0) {
            this.y = particules.height;
            this.x = Math.random() * particules.width;
        }

        this.draw();
    }
}
// Crée 100 particules
const creationparticules = [];
for (let i = 0; i < 100; i++) {
    creationparticules.push(new Particule(
        Math.random() * particules.width,
        Math.random() * particules.height
    ));
}
// Animation
function animer() {
    ctx.clearRect(0, 0, particules.width, particules.height);
    creationparticules.forEach(p => p.update());
    requestAnimationFrame(animer);
}animer();