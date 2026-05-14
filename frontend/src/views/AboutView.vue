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
      <div class="badge">Sobre Nosaltres</div>
      <h1>L'equip darrera d'<span>EventAI</span></h1>
      
      <div class="about-content">
        <p>Som un equip compromès amb la innovació en el sector de la gestió d'esdeveniments. La nostra missió és democratitzar l'ús de la Intel·ligència Artificial perquè qualsevol empresa pugui optimitzar els seus recursos i oferir millors pressupostos.</p>
        
        <div class="values-grid">
          <div class="value-item">
            <strong>Innovació</strong>
            <span>Sempre a l'última en tecnologia.</span>
          </div>
          <div class="value-item">
            <strong>Precisió</strong>
            <span>Dades reals per a decisions reals.</span>
          </div>
          <div class="value-item">
            <strong>Facilitat</strong>
            <span>Eines potents però fàcils d'utilitzar.</span>
          </div>
        </div>
      </div>

      <div class="cta-section">
        <router-link to="/" class="btn-secondary">Tornar a la Portada</router-link>
      </div>
    </div>

    <!-- Decorative Orbs -->
    <div class="orb orb-2"></div>
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
  max-width: 800px;
  margin: 0 auto;
  padding: 5rem 5%;
  text-align: center;
  position: relative;
  z-index: 2;
}

.badge {
  display: inline-block;
  padding: 0.4rem 1rem;
  background: rgba(236, 72, 153, 0.1);
  border: 1px solid rgba(236, 72, 153, 0.3);
  border-radius: 2rem;
  color: #f472b6;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
}

h1 { 
  font-size: 3.5rem; 
  font-weight: 800; 
  margin-bottom: 2.5rem; 
  line-height: 1.1; 
}
h1 span { background: linear-gradient(to right, #ec4899, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

.about-content p {
  font-size: 1.2rem;
  color: #94a3b8;
  line-height: 1.8;
  margin-bottom: 4rem;
}

.cta-section { margin-top: 6rem; }

.values-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
  text-align: left;
}

.value-item {
  background: rgba(255, 255, 255, 0.03);
  padding: 1.5rem;
  border-radius: 1rem;
  border-left: 4px solid #ec4899;
}

.value-item strong { display: block; font-size: 1.1rem; margin-bottom: 0.5rem; }
.value-item span { color: #64748b; font-size: 0.9rem; }

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

.orb { position: absolute; width: 600px; height: 600px; border-radius: 50%; filter: blur(120px); z-index: 0; opacity: 0.2; }
.orb-2 { background: #ec4899; bottom: -100px; left: -100px; }
</style>
