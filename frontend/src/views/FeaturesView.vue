<template>
  <div class="page-container">
    <nav class="navbar">
      <router-link to="/" class="logo">
        <span class="logo-icon">✨</span>
        <span class="logo-text">EventAI<span class="logo-sub">Manager</span></span>
      </router-link>
      <div class="auth-buttons">
        <router-link to="/login" class="btn-login">Inicia Sessió</router-link>
      </div>
    </nav>

    <div class="content">
      <div class="badge">Funcionalitats Premium</div>
      <h1>Tecnologia IA al servei dels teus <span>Esdeveniments</span></h1>
      
      <div class="features-grid">
        <div class="feature-card">
          <div class="icon">💰</div>
          <h3>Pressupostos en Temps Real</h3>
          <p>La nostra IA analitza milers de dades per oferir-te una estimació de cost ultra-precisa en segons.</p>
        </div>
        <div class="feature-card">
          <div class="icon">👥</div>
          <h3>Assignació Intel·ligent</h3>
          <p>T'ajudem a triar els treballadors més adequats per a cada tipus d'esdeveniment basant-nos en el seu perfil.</p>
        </div>
        <div class="feature-card">
          <div class="icon">📊</div>
          <h3>Gestió Integral</h3>
          <p>Control total des d'un sol lloc: clients, esdeveniments, personal i horaris.</p>
        </div>
      </div>

      <div class="cta-section">
        <router-link to="/login?mode=register" class="btn-primary">Vull provar-ho</router-link>
        <router-link to="/" class="btn-secondary">Tornar a l'inici</router-link>
      </div>
    </div>

    <!-- Decorative Orbs -->
    <div class="orb orb-1"></div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap');

.page-container {
  font-family: 'Outfit', sans-serif;
  background-color: #0f172a;
  color: white;
  min-height: 100vh;
  position: relative;
  overflow: hidden;
}

.navbar {
  display: flex;
  justify-content: space-between;
  padding: 1.5rem 5%;
  background: rgba(15, 23, 42, 0.8);
  backdrop-filter: blur(10px);
}

.logo { display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: white; font-size: 1.5rem; font-weight: 800; }
.logo-sub { color: #6366f1; }

.content {
  max-width: 1100px;
  margin: 0 auto;
  padding: 5rem 5%;
  text-align: center;
  position: relative;
  z-index: 2;
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
  font-size: 3.5rem; 
  font-weight: 800; 
  margin-bottom: 4rem; 
  line-height: 1.1; 
}
h1 span { background: linear-gradient(to right, #6366f1, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin-bottom: 5rem;
}

.feature-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.1);
  padding: 2.5rem;
  border-radius: 1.5rem;
  transition: transform 0.3s;
}

.feature-card:hover { transform: translateY(-10px); background: rgba(255, 255, 255, 0.05); }

.icon { font-size: 3rem; margin-bottom: 1.5rem; }
h3 { font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; }
p { color: #94a3b8; line-height: 1.6; }

.cta-section { 
  margin-top: 6rem; 
  display: flex;
  gap: 1.5rem;
  justify-content: center;
  align-items: center;
}

.btn-secondary {
  padding: 1rem 2rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: white;
  text-decoration: none;
  font-weight: 700;
  border-radius: 0.75rem;
  transition: 0.3s;
}

.btn-secondary:hover { background: rgba(255, 255, 255, 0.1); }
.btn-primary {
  padding: 1rem 2.5rem;
  background: #6366f1;
  color: white;
  text-decoration: none;
  font-weight: 700;
  border-radius: 0.75rem;
  transition: 0.3s;
}
.btn-primary:hover { background: #4f46e5; box-shadow: 0 0 20px rgba(99, 102, 241, 0.4); }

.orb { position: absolute; width: 500px; height: 500px; border-radius: 50%; filter: blur(120px); z-index: 0; opacity: 0.2; }
.orb-1 { background: #6366f1; top: 10%; right: -100px; }
</style>
