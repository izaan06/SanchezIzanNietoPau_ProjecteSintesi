<template>
  <div class="landing-container">
    <!-- Navigation Bar (Simple) -->
    <nav class="navbar">
      <div class="logo">
        <span class="logo-icon">✨</span>
        <span class="logo-text">EventAI<span class="logo-sub">Manager</span></span>
      </div>
      <div class="nav-links">
        <router-link to="/features">Característiques</router-link>
        <router-link to="/about">Nosaltres</router-link>
        <div class="auth-buttons">
          <router-link to="/login" class="btn-login">Inicia Sessió</router-link>
          <router-link to="/login?mode=register" class="btn-register">Registra't</router-link>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
      <div class="hero-content">
        <div class="badge">IA Generativa per a Esdeveniments</div>
        <h1>Organitza esdeveniments inoblidables amb el poder de la <span>IA</span></h1>
        <p>Gestiona cites, estima pressupostos en temps real i assigna el millor equip per als teus esdeveniments corporatius i socials amb tecnologia de darrera generació.</p>
        <div class="hero-actions">
          <router-link to="/login" class="btn-primary">Entrar a l'App</router-link>
          <router-link to="/features" class="btn-secondary">Saber-ne més</router-link>
        </div>
      </div>
      <div class="hero-visual">
        <div class="glass-card">
          <div class="card-header">
            <div class="dot red"></div>
            <div class="dot yellow"></div>
            <div class="dot green"></div>
          </div>
          <div class="card-body">
            <div class="stats-row">
              <div class="stat-item">
                <span class="stat-val">98%</span>
                <span class="stat-label">Precisió IA</span>
              </div>
              <div class="stat-item">
                <span class="stat-val">+1.2k</span>
                <span class="stat-label">Esdeveniments</span>
              </div>
            </div>
            <div class="chart-mock">
              <div class="bar" style="height: 40%"></div>
              <div class="bar" style="height: 70%"></div>
              <div class="bar" style="height: 50%"></div>
              <div class="bar" style="height: 90%"></div>
              <div class="bar" style="height: 60%"></div>
            </div>
          </div>
        </div>
        <div class="floating-elements">
          <div class="float-item icon-1">📅</div>
          <div class="float-item icon-2">💰</div>
          <div class="float-item icon-3">👥</div>
        </div>
      </div>
    </header>

    <!-- Background Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
  </div>
</template>

<script setup>
// Landing page logic
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap');

.landing-container {
  font-family: 'Outfit', sans-serif;
  background-color: #0f172a;
  color: white;
  min-height: 100vh;
  overflow-x: hidden;
  position: relative;
}

/* --- Navbar --- */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 5%;
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(15, 23, 42, 0.8);
  backdrop-filter: blur(10px);
}

.logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.5rem;
  font-weight: 800;
}

.logo-sub {
  color: #6366f1;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 2.5rem;
}

.nav-links a {
  color: #94a3b8;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}

.nav-links a:hover {
  color: white;
}

.auth-buttons {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.btn-login {
  padding: 0.6rem 1.2rem;
  color: white;
  text-decoration: none;
  font-weight: 600;
  border-radius: 0.5rem;
  transition: background 0.3s;
}

.btn-login:hover {
  background: rgba(255, 255, 255, 0.1);
}

.btn-register {
  padding: 0.6rem 1.5rem;
  background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
  color: white;
  text-decoration: none;
  font-weight: 600;
  border-radius: 0.5rem;
  box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
  transition: transform 0.3s, box-shadow 0.3s;
}

.btn-register:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6);
}

/* --- Hero --- */
.hero {
  display: grid;
  grid-template-columns: 1.2fr 1fr;
  padding: 5rem 5% 10rem;
  align-items: center;
  gap: 4rem;
}

.hero-content {
  max-width: 700px;
  position: relative;
  z-index: 10;
}

.badge {
  display: inline-block;
  padding: 0.4rem 1rem;
  background: rgba(99, 102, 241, 0.1);
  border: 1px solid rgba(99, 102, 241, 0.3);
  border-radius: 2rem;
  color: #818cf8;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
}

h1 {
  font-size: 4.5rem;
  line-height: 1.1;
  font-weight: 800;
  margin-bottom: 1.5rem;
}

h1 span {
  background: linear-gradient(to right, #6366f1, #ec4899);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

p {
  font-size: 1.2rem;
  color: #94a3b8;
  line-height: 1.6;
  margin-bottom: 2.5rem;
}

.hero-actions {
  display: flex;
  gap: 1.5rem;
}

.btn-primary {
  padding: 1rem 2rem;
  background: #6366f1;
  color: white;
  text-decoration: none;
  font-weight: 700;
  border-radius: 0.75rem;
  transition: background 0.3s;
}

.btn-primary:hover {
  background: #4f46e5;
}

.btn-secondary {
  padding: 1rem 2rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: white;
  text-decoration: none;
  font-weight: 700;
  border-radius: 0.75rem;
  transition: background 0.3s;
}

/* --- Visuals --- */
.hero-visual {
  position: relative;
  display: flex;
  justify-content: center;
}

.glass-card {
  width: 100%;
  max-width: 400px;
  background: rgba(255, 255, 255, 0.03);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 1.5rem;
  padding: 1.5rem;
  z-index: 2;
}

.card-header {
  display: flex;
  gap: 6px;
  margin-bottom: 2rem;
}

.dot { width: 10px; height: 10px; border-radius: 50%; }
.red { background: #ff5f56; }
.yellow { background: #ffbd2e; }
.green { background: #27c93f; }

.stats-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 2rem;
}

.stat-val { font-size: 2rem; font-weight: 800; display: block; }
.stat-label { color: #64748b; font-size: 0.9rem; }

.chart-mock {
  display: flex;
  align-items: flex-end;
  gap: 12px;
  height: 100px;
}

.bar {
  flex: 1;
  background: linear-gradient(to top, #6366f1, #a855f7);
  border-radius: 4px 4px 0 0;
  animation: grow 2s ease-out forwards;
}

@keyframes grow {
  from { height: 0; }
}

.floating-elements .float-item {
  position: absolute;
  width: 60px;
  height: 60px;
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(5px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  animation: float 4s ease-in-out infinite;
}

.icon-1 { top: -20px; right: 0; animation-delay: 0s; }
.icon-2 { bottom: 20px; left: -30px; animation-delay: 1s; }
.icon-3 { top: 50%; left: -50px; animation-delay: 2s; }

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-20px); }
}

/* --- Orbs --- */
.orb {
  position: absolute;
  width: 500px;
  height: 500px;
  border-radius: 50%;
  filter: blur(100px);
  z-index: 0;
  opacity: 0.4;
}

.orb-1 {
  background: #6366f1;
  top: -100px;
  right: -100px;
}

.orb-2 {
  background: #ec4899;
  bottom: -100px;
  left: -100px;
}

@media (max-width: 1024px) {
  .hero { grid-template-columns: 1fr; text-align: center; }
  h1 { font-size: 3rem; }
  .nav-links { display: none; }
  .hero-visual { margin-top: 3rem; }
}
</style>
