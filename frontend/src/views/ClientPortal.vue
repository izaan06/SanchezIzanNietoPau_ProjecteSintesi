<template>
  <div class="client-portal">
    <!-- Navigation Tabs eliminades d'aquí per passar a la sidebar -->
    
    <!-- 0. HOME TAB (Inici / Inspiració) -->
    <div v-if="activeTab === 'home' || !activeTab" class="tab-content">
      <!-- Hero Section -->
      <section class="hero-section glass-card">
        <div class="hero-content">
          <h1>Transformem la teva visió en <span class="text-gradient">Realitat</span></h1>
          <p>EventAI Manager utilitza la tecnologia més avancada per planificar esdeveniments memorables amb pressupostos precisos.</p>
          <div class="hero-actions">
            <button @click="$router.push('/client-portal?tab=form')" class="btn-primary">Sol·licitar Cita Ara</button>
            <button @click="activeTab = 'menus'" class="btn-outline">Veure Menús</button>
          </div>
        </div>
        <div class="hero-visual">
          <div class="abstract-shape shape-1"></div>
          <div class="abstract-shape shape-2"></div>
          <div class="floating-card">
            <Sparkles class="icon-accent" />
            <span>IA-Powered Planning</span>
          </div>
        </div>
      </section>

      <!-- Services Grid -->
      <section class="services-section">
        <h2 class="section-title">Els nostres Serveis</h2>
        <div class="services-grid">
          <div v-for="service in services" :key="service.id" class="service-card glass-card">
            <div class="service-icon" :style="{ background: service.color }">
              <component :is="service.icon" class="icon-white" />
            </div>
            <h3>{{ service.title }}</h3>
            <p>{{ service.description }}</p>
          </div>
        </div>
      </section>

      <!-- Testimonials Section -->
      <section class="testimonials-section">
        <h2 class="section-title">Experiències Reals</h2>
        <div class="testimonials-grid">
          <div v-for="testimonial in testimonials" :key="testimonial.id" class="testimonial-card glass-card">
            <div class="testimonial-rating">
              <Star v-for="n in 5" :key="n" class="icon-star" :class="{ filled: n <= testimonial.rating }" />
            </div>
            <p class="testimonial-text">"{{ testimonial.text }}"</p>
            <div class="testimonial-author">
              <img :src="testimonial.avatar" :alt="testimonial.name" class="author-img" />
              <div class="author-info">
                <span class="author-name">{{ testimonial.name }}</span>
                <span class="author-event">{{ testimonial.event }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- FAQ Section -->
      <section class="faq-section">
        <h2 class="section-title">Preguntes Freqüents</h2>
        <div class="faq-container">
          <div v-for="(faq, index) in faqs" :key="index" class="faq-item glass-card" :class="{ active: faq.open }">
            <div class="faq-question" @click="faq.open = !faq.open">
              <span>{{ faq.question }}</span>
              <ChevronDown class="icon-chevron" :class="{ rotated: faq.open }" />
            </div>
            <transition name="slide">
              <div v-if="faq.open" class="faq-answer">
                <p>{{ faq.answer }}</p>
              </div>
            </transition>
          </div>
        </div>
      </section>
    </div>

    <!-- 1. FORM TAB (Només el formulari) -->
    <div v-if="activeTab === 'form'" class="tab-content">
      <!-- Appointment Form -->
      <section id="appointment-form" class="appointment-section glass-card">
        <div class="form-container">
          <div class="form-header">
            <h2>Sol·licita el teu Esdeveniment</h2>
            <p>Explica'ns què tens en ment i el nostre motor d'IA t'ajudarà amb el pressupost.</p>
          </div>

          <form @submit.prevent="submitForm" class="appointment-form">
            <!-- Group 1: Personal Data -->
            <div class="form-group-section">
              <h3 class="group-title">Dades de Contacte</h3>
              <div class="form-row">
                <div class="form-group">
                  <label>Nom Complet</label>
                  <input v-model="form.name" type="text" placeholder="El teu nom" required />
                </div>
                <div class="form-group">
                  <label>Telèfon de Contacte</label>
                  <input v-model="form.phone" type="tel" maxlength="9" @input="form.phone = form.phone.replace(/[^0-9]/g, '')" placeholder="600000000" />
                </div>
              </div>
              <div class="form-group">
                <label>Correu Electrònic</label>
                <input v-model="form.email" type="email" placeholder="correu@exemple.com" required />
              </div>
            </div>

            <!-- Group 2: Event Details -->
            <div class="form-group-section">
              <h3 class="group-title">Detalls de l'Esdeveniment</h3>
              <div class="form-row">
                <div class="form-group">
                  <label>Tipus d'Esdeveniment</label>
                  <select v-model="form.event_type" required class="custom-select">
                    <option value="" disabled>Selecciona un tipus</option>
                    <option value="Boda">Boda</option>
                    <option value="Corporatiu">Esdeveniment Corporatiu</option>
                    <option value="Aniversari">Aniversari</option>
                    <option value="Concert">Concert / Festival</option>
                    <option value="Festa Privada">Festa Privada</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Data Desitjada</label>
                  <input v-model="form.desired_date" type="date" :min="today" required />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>Hora Inici / Final</label>
                  <div class="time-inputs">
                    <input v-model="form.start_time" type="time" />
                    <span class="time-separator">fins a</span>
                    <input v-model="form.end_time" type="time" />
                  </div>
                </div>
                <div class="form-group">
                  <label>Nombre d'Assistents</label>
                  <input v-model="form.attendees" type="number" placeholder="Ex: 100" />
                </div>
              </div>

              <div class="form-group">
                <label>Ubicació o Nom del Local</label>
                <input v-model="form.location_name" type="text" placeholder="Hotel, Masia, Restaurant, Sala..." />
              </div>
              
              <div class="form-group" style="margin-top: 2.5rem;">
                <label>Pressupost aproximat que tens al cap (€)</label>
                <div class="budget-input-wrapper">
                  <input v-model="form.client_budget" type="number" placeholder="Ex: 5000" />
                  <span class="currency-label">€</span>
                </div>
              </div>
            </div>

            <!-- Group 3: Services & Preferences -->
            <div class="form-group-section">
              <h3 class="group-title">Serveis i Preferències</h3>
              <div class="form-row">
                <div class="form-group">
                  <label>Música / DJ</label>
                  <select v-model="form.wants_music" class="custom-select">
                    <option :value="false">No necessito música</option>
                    <option :value="true">Sí, vull música / DJ</option>
                  </select>
                </div>
                <div v-if="form.wants_music" class="form-group">
                  <label>Estil de Música</label>
                  <input v-model="form.music_type" type="text" placeholder="Ex: Pop, Electrònica, Jazz..." />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>Tipus de Menú / Càtering</label>
                  <select v-model="form.menu_type" class="custom-select">
                    <option value="">Sense càtering</option>
                    <option value="Gourmet">Menú Gourmet</option>
                    <option value="Buffet">Buffet Lliure</option>
                    <option value="Finger Food">Finger Food / Pica-pica</option>
                    <option value="Personalitzat">Personalitzat</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Al·lèrgies o Requisits Dietètics</label>
                  <input v-model="form.dietary_requirements" type="text" placeholder="Ex: Vegà, Celíac..." />
                </div>
              </div>

              <div class="form-group">
                <label>Missatge / Altres Detalls d'interès</label>
                <textarea v-model="form.message" rows="4" placeholder="Explica'ns qualsevol altre detall que vulguis que la nostra IA tingui en compte..."></textarea>
              </div>
            </div>

            <!-- Success/Error Messages -->
            <div class="form-alerts">
              <transition name="fade">
                <div v-if="successMsg" class="success-alert">{{ successMsg }}</div>
              </transition>
              <transition name="fade">
                <div v-if="errorMsg" class="error-alert">{{ errorMsg }}</div>
              </transition>
            </div>

            <div class="form-footer">
              <p class="privacy-note">En enviar aquest formulari, la nostra IA calcularà els teus costos operatius al moment.</p>
              <br>
              <button type="submit" class="btn-submit" :disabled="submitting">
                <span v-if="submitting" class="loader-sm"></span>
                <span v-else>Calcular i Enviar Sol·licitud</span>
              </button>
            </div>
          </form>
        </div>
      </section>
    </div>

    <!-- 2. STATUS TAB -->
    <div v-if="activeTab === 'status'" class="tab-content">
      <section class="status-section glass-card">
        <h2 class="section-title">Estat de les meves Sol·licituds</h2>
        <div v-if="myAppointments.length === 0" class="no-data">
          Encara no has realitzat cap sol·licitud.
        </div>
        <div class="status-grid">
          <div v-for="app in myAppointments" :key="app.id" class="status-card" :class="app.status">
            <div class="status-header">
              <h3>{{ app.event_type }}</h3>
              <span class="status-badge">{{ app.status }}</span>
            </div>
            <div class="status-body">
              <p><CalendarDays class="icon-xs" /> {{ formatDate(app.desired_date) }}</p>
              <p><MapPin class="icon-xs" /> {{ app.location_name || 'Ubicació a confirmar' }}</p>
              <div class="ai-preview" v-if="app.ai_estimated_budget">
                <Sparkles class="icon-xs" /> Pressupost Estimat IA: <strong>{{ app.ai_estimated_budget }}€</strong>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- 3. MENUS TAB -->
    <div v-if="activeTab === 'menus'" class="tab-content">
      <section class="menus-section">
        <h2 class="section-title">El Nostre Catàleg Gastronòmic</h2>
        <div class="menus-grid">
          <div v-for="menu in menuCatalog" :key="menu.id" class="menu-card glass-card">
            <img :src="menu.image" :alt="menu.name" class="menu-img" />
            <div class="menu-info">
              <div class="menu-header">
                <h3>{{ menu.name }}</h3>
                <span class="menu-price">{{ menu.price }}€/pers</span>
              </div>
              <p>{{ menu.description }}</p>
              <ul class="menu-features">
                <li v-for="feat in menu.features" :key="feat">✓ {{ feat }}</li>
              </ul>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- 4. GALLERY TAB -->
    <div v-if="activeTab === 'gallery'" class="tab-content">
      <section class="gallery-section">
        <h2 class="section-title">Galeria d'Esdeveniments Memorables</h2>
        <div class="gallery-grid">
          <div v-for="img in galleryImages" :key="img.url" class="gallery-item">
            <img :src="img.url" :alt="img.title" />
            <div class="gallery-overlay">
              <span>{{ img.title }}</span>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { 
  Sparkles, 
  Music, 
  Cake, 
  Briefcase, 
  Heart,
  CalendarDays,
  Utensils,
  Image,
  MapPin,
  Star,
  ChevronDown
} from 'lucide-vue-next'
import api from '../api/axios'

const route = useRoute()
const activeTab = ref(route.query.tab || 'home')

const faqs = ref([
  {
    question: 'Amb quanta antelació he de sol·licitar l\'esdeveniment?',
    answer: 'Per garantir la millor qualitat i disponibilitat de personal, demanem un mínim d\'un mes d\'antelació. Per a bodes, recomanem entre 6 i 12 mesos.',
    open: false
  },
  {
    question: 'Com funciona el pressupost estimat per IA?',
    answer: 'El nostre motor d\'IA analitza milers de dades de mercat, costos operatius i disponibilitat de treballadors per oferir-te una xifra molt propera a la realitat en pocs segons.',
    open: false
  },
  {
    question: 'Puc modificar la meva sol·licitud un cop enviada?',
    answer: 'Sí, pots contactar amb nosaltres a través del correu de confirmació. Un cop acceptada, tindràs un gestor assignat per a qualsevol canvi d\'última hora.',
    open: false
  },
  {
    question: 'Quins mètodes de pagament accepteu?',
    answer: 'Acceptem transferència bancària, targeta de crèdit i pagaments a terminis per a esdeveniments de gran format com bodes o congressos.',
    open: false
  }
])

const testimonials = [
  {
    id: 1,
    name: 'Laura i Marc',
    event: 'Boda al Castell',
    rating: 5,
    text: 'Increïble! La IA ens va ajudar a ajustar el pressupost i el dia de la boda va ser tot perfecte. Moltes gràcies per l\'atenció.',
    avatar: 'https://i.pravatar.cc/150?u=laura'
  },
  {
    id: 2,
    name: 'Robert Sánchez',
    event: 'Conferència Tech 2025',
    rating: 5,
    text: 'Un servei impecable. Els treballadors assignats eren super professionals i la logística va funcionar com un rellotge.',
    avatar: 'https://i.pravatar.cc/150?u=robert'
  },
  {
    id: 3,
    name: 'Carla Vilà',
    event: 'Aniversari 40 anys',
    rating: 4,
    text: 'Molt bon menjar i música. El procés de sol·licitud és super ràpid i et dona molta tranquil·litat veure els preus estimats.',
    avatar: 'https://i.pravatar.cc/150?u=carla'
  }
]

// Sincronitzar la pestanya amb la URL
watch(() => route.query.tab, (newTab) => {
  if (newTab) activeTab.value = newTab
})
const minDate = new Date()
minDate.setMonth(minDate.getMonth() + 1)
const today = minDate.toISOString().split('T')[0]

const submitting = ref(false)
const successMsg = ref('')
const errorMsg = ref('')
const myAppointments = ref([])

// Mock de dades per al catàleg gastronòmic
const menuCatalog = [
  { 
    id: 1, 
    name: 'Menú Gourmet', 
    price: 125, 
    image: 'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&q=80&w=800',
    description: 'Una experiència gastronòmica de 5 estrelles per a paladars exigents.',
    features: ['Maridatge de vins premium', 'Servei de cambrers de gala', 'Plats d\'autor de temporada']
  },
  { 
    id: 2, 
    name: 'Buffet Internacional', 
    price: 65, 
    image: 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&q=80&w=800',
    description: 'Varietat de cuines del món en un format dinàmic i interactiu.',
    features: ['Estacions de cuina en viu', 'Show cooking de postres', 'Opcions globals per a tothom']
  },
  { 
    id: 3, 
    name: 'Finger Food & Cocktail', 
    price: 45, 
    image: 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&q=80&w=800',
    description: 'Ideal per a esdeveniments corporatius i networking relaxat.',
    features: ['Barra de cocktails d\'autor', 'Aperitius creatius', 'Format de peu dinàmic']
  }
]

const galleryImages = [
  { title: 'Boda al Castell', url: 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&q=80&w=800' },
  { title: 'Conferència Tech', url: 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&q=80&w=800' },
  { title: 'Aniversari VIP', url: 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&q=80&w=800' },
  { title: 'Sopar d\'Empresa', url: 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&q=80&w=800' },
  { title: 'Gala Benèfica', url: 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?auto=format&fit=crop&q=80&w=800' },
  { title: 'Festival Musical', url: 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80&w=800' }
]

const services = [
  { id: 1, title: 'Bodes', icon: Heart, color: '#ec4899', description: 'Creem el dia més especial de la teva vida amb tot el luxe de detalls.' },
  { id: 2, title: 'Corporatiu', icon: Briefcase, color: '#3b82f6', description: 'Congressos i sopars d\'empresa que reflecteixen la teva marca.' },
  { id: 3, title: 'Festes', icon: Cake, color: '#f59e0b', description: 'Aniversaris i celebracions privades inoblidables.' },
  { id: 4, title: 'Música', icon: Music, color: '#8b5cf6', description: 'Gestió de so, il·luminació i artistes per a grans esdeveniments.' }
]

const form = ref({
  name: '',
  email: '',
  phone: '',
  client_budget: 5000,
  event_type: '',
  desired_date: '',
  attendees: 100,
  start_time: '18:00',
  end_time: '23:00',
  location_name: '',
  wants_music: false,
  music_type: '',
  menu_type: '',
  dietary_requirements: '',
  message: ''
})

const fetchMyAppointments = async () => {
  try {
    const res = await api.get('/my-appointments')
    myAppointments.value = res.data.data
  } catch (err) {
    console.error('Error carregant sol·licituds', err)
  }
}

const scrollToForm = () => {
  const el = document.getElementById('appointment-form')
  if (el) el.scrollIntoView({ behavior: 'smooth' })
}

const submitForm = async () => {
  try {
    submitting.value = true
    errorMsg.value = ''
    successMsg.value = ''
    
    await api.post('/appointment-request', form.value)
    
    successMsg.value = 'Sol·licitud enviada correctament! La nostra IA ja està processant-la.'
    // Reset form
    Object.keys(form.value).forEach(key => {
      if (typeof form.value[key] === 'string') form.value[key] = ''
      if (typeof form.value[key] === 'number') form.value[key] = 0
      if (typeof form.value[key] === 'boolean') form.value[key] = false
    })
    form.value.attendees = 100
    form.value.client_budget = 5000
    
    // Refresh status if we are on that tab
    if (activeTab.value === 'status') fetchMyAppointments()
  } catch (err) {
    if (err.response && err.response.data && err.response.data.message) {
      errorMsg.value = err.response.data.message
    } else {
      errorMsg.value = 'Hi ha hagut un error enviant la sol·licitud. Torna-ho a provar.'
    }
  } finally {
    submitting.value = false
  }
}

const formatDate = (date) => date ? new Date(date).toLocaleDateString() : 'A concretar'

watch(activeTab, (newTab) => {
  if (newTab === 'status') {
    fetchMyAppointments()
  }
})

onMounted(() => {
  // Check if we have a token (user logged in)
  if (localStorage.getItem('token')) {
    fetchMyAppointments()
  }
})
</script>

<style scoped>
/* ---------------------------------------------------------
   DIRECCIONS GLOBALS I LAYOUT
   --------------------------------------------------------- */
.client-portal {
  display: flex;
  flex-direction: column;
  gap: 8rem; /* Espai generós entre Hero, Serveis i Formulari */
  padding: 2rem;
  padding-bottom: 8rem;
  max-width: 1400px;
  margin: 0 auto;
}

.tab-content {
  display: flex;
  flex-direction: column;
  gap: 8rem; /* Separa les seccions internes de cada pestanya */
  animation: fadeIn 0.5s ease-out;
}

/* ---------------------------------------------------------
   HERO SECTION
   --------------------------------------------------------- */
.hero-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  padding: 4rem;
  align-items: center;
  gap: 2rem;
  min-height: 500px;
}

.hero-content h1 {
  font-size: 3.5rem;
  line-height: 1.1;
  margin-bottom: 1.5rem;
  font-weight: 800;
}

.text-gradient {
  background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-content p {
  font-size: 1.25rem;
  color: var(--text-secondary);
  margin-bottom: 2.5rem;
  max-width: 500px;
}

.hero-actions {
  display: flex;
  gap: 1rem;
}

.btn-primary {
  background: var(--accent-primary);
  color: white;
  padding: 0.8rem 1.8rem;
  border-radius: 12px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
}

.btn-outline {
  background: transparent;
  border: 1px solid var(--accent-primary);
  color: var(--accent-primary);
  padding: 0.8rem 1.8rem;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.hero-visual {
  position: relative;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.abstract-shape {
  position: absolute;
  filter: blur(60px);
  opacity: 0.4;
  border-radius: 50%;
}

.shape-1 {
  width: 250px; height: 250px;
  background: var(--accent-primary);
  top: 0; right: 0;
  animation: float 6s infinite ease-in-out;
}

.shape-2 {
  width: 200px; height: 200px;
  background: var(--accent-secondary);
  bottom: 0; left: 0;
  animation: float 8s infinite ease-in-out reverse;
}

.floating-card {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  padding: 2rem;
  border-radius: 24px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  z-index: 2;
  box-shadow: 0 20px 40px rgba(0,0,0,0.3);
  animation: float 5s infinite ease-in-out;
}

/* ---------------------------------------------------------
   TESTIMONIALS SECTION
   --------------------------------------------------------- */
.testimonials-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2.5rem;
}

.testimonial-card {
  padding: 3rem 2.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  position: relative;
}

.testimonial-card::before {
  content: '“';
  position: absolute;
  top: 1rem;
  right: 2rem;
  font-size: 5rem;
  color: var(--accent-primary);
  opacity: 0.1;
  font-family: serif;
}

.testimonial-rating {
  display: flex;
  gap: 0.25rem;
}

.icon-star {
  width: 18px;
  height: 18px;
  color: rgba(255, 255, 255, 0.2);
}

.icon-star.filled {
  color: #fbbf24;
  fill: #fbbf24;
}

.testimonial-text {
  font-size: 1.1rem;
  line-height: 1.6;
  font-style: italic;
  color: var(--text-secondary);
}

.testimonial-author {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-top: 1rem;
}

.author-img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--accent-primary);
}

.author-name {
  display: block;
  font-weight: 800;
  font-size: 1rem;
}

.author-event {
  display: block;
  font-size: 0.85rem;
  color: var(--text-muted);
}

/* ---------------------------------------------------------
   FAQ SECTION
   --------------------------------------------------------- */
.faq-container {
  max-width: 800px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.faq-item {
  cursor: pointer;
  padding: 0 !important;
  overflow: hidden;
  transition: all 0.3s ease;
}

.faq-item:hover {
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(99, 102, 241, 0.3);
}

.faq-item.active {
  background: rgba(255, 255, 255, 0.04);
  border-color: var(--accent-primary);
}

.faq-question {
  padding: 1.5rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 700;
  font-size: 1.1rem;
}

.icon-chevron {
  width: 20px;
  height: 20px;
  color: var(--accent-primary);
  transition: transform 0.3s ease;
}

.icon-chevron.rotated {
  transform: rotate(180deg);
}

.faq-answer {
  padding: 0 2rem 1.5rem;
  color: var(--text-secondary);
  line-height: 1.6;
  font-size: 1rem;
}

.slide-enter-active, .slide-leave-active {
  transition: all 0.3s ease;
  max-height: 200px;
}

.slide-enter-from, .slide-leave-to {
  max-height: 0;
  opacity: 0;
  transform: translateY(-10px);
}

.section-title {
  font-size: 2.5rem;
  text-align: center;
  margin-bottom: 4rem;
  font-weight: 800;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
}

.service-card {
  padding: 3rem 2rem;
  text-align: center;
  transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.service-card:hover {
  transform: translateY(-15px);
}

.service-icon {
  width: 70px; height: 70px;
  border-radius: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto 2rem;
}

/* ---------------------------------------------------------
   APPOINTMENT FORM SECTION
   --------------------------------------------------------- */
.appointment-section {
  width: 100%;
  max-width: 1000px;
  margin: 0 auto;
  padding: 5rem 4rem;
}

.form-header {
  text-align: center;
  margin-bottom: 4rem;
}

.form-header h2 {
  font-size: 2.8rem;
  margin-bottom: 1rem;
  font-weight: 800;
}

.appointment-form {
  display: flex;
  flex-direction: column;
  gap: 3.5rem;
}

.form-group-section {
  background: rgba(255, 255, 255, 0.02);
  padding: 3rem;
  border-radius: 24px;
  border: 1px solid rgba(255, 255, 255, 0.05);
}

.group-title {
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--accent-primary);
  margin-bottom: 2.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.group-title::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(to right, rgba(99, 102, 241, 0.3), transparent);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2.5rem;
  margin-bottom: 2rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

label {
  font-size: 0.8rem;
  color: var(--text-secondary);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.5px;
}

input, select, textarea {
  background: rgba(15, 23, 42, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.1);
  padding: 1.25rem;
  border-radius: 14px;
  color: white;
  font-size: 1rem;
  transition: all 0.3s;
}

input:focus, select:focus, textarea:focus {
  outline: none;
  border-color: var(--accent-primary);
  background: rgba(15, 23, 42, 0.8);
  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.time-inputs {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.time-inputs input { flex: 1; }

.budget-input-wrapper {
  position: relative;
  display: inline-flex;
  align-items: center;
  width: 250px; /* Fixem el tamany del contenidor */
}

.budget-input-wrapper input {
  width: 100%; /* L'input ocupa el tamany del contenidor */
  padding-right: 3rem; /* Espai pel símbol de l'euro */
}

.currency-label {
  position: absolute;
  right: 1.25rem;
  color: var(--accent-primary);
  font-weight: 800;
}

.form-footer {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.2rem;
  margin-top: -3.5rem;
}

.privacy-note {
  color: var(--text-muted);
  font-size: 0.9rem;
  text-align: center;
  max-width: 600px;
}

.btn-submit {
  width: 100%;
  max-width: 450px;
  background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
  color: white;
  padding: 1.5rem;
  border: none;
  border-radius: 16px;
  font-weight: 800;
  font-size: 1.2rem;
  cursor: pointer;
  box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
  transition: all 0.3s;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 0 15px 35px rgba(99, 102, 241, 0.4);
}

/* ---------------------------------------------------------
   STATUS, MENUS & GALLERY
   --------------------------------------------------------- */
.status-grid, .menus-grid, .gallery-grid {
  display: grid;
  gap: 3.5rem; /* Augmentat per a més aire */
  margin-top: 3rem;
}

.status-grid { grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); }

.status-section {
  padding: 4rem; /* Afegit padding per a que el contingut no estigui pegat */
}

.no-data {
  padding: 3rem;
  text-align: center;
  color: var(--text-muted);
  font-style: italic;
  font-size: 1.1rem;
  background: rgba(255, 255, 255, 0.02);
  border-radius: 16px;
  border: 1px dashed rgba(255, 255, 255, 0.1);
}
.menus-grid { grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); }
.gallery-grid { grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); }

.status-card {
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 20px;
  padding: 2rem;
  border-left: 6px solid #64748b;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.status-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.4);
  background: rgba(30, 41, 59, 0.8);
}

.status-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  padding-bottom: 1.5rem;
}

.status-header h3 {
  font-size: 1.4rem;
  font-weight: 800;
  color: #fff;
  margin: 0;
}

.status-badge {
  padding: 0.4rem 1rem;
  border-radius: 50px;
  font-size: 0.8rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 1px;
  background: rgba(100, 116, 139, 0.15);
  color: #cbd5e1;
  border: 1px solid rgba(100, 116, 139, 0.3);
}

.status-card.accepted { border-left-color: #10b981; }
.status-card.accepted .status-badge { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }

.status-card.pending { border-left-color: #f59e0b; }
.status-card.pending .status-badge { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }

.status-card.rejected { border-left-color: #ef4444; }
.status-card.rejected .status-badge { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }

.status-body {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.status-body p {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  color: var(--text-secondary);
  font-size: 1.05rem;
  margin: 0;
}

.ai-preview {
  margin-top: 0.5rem;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
  border: 1px solid rgba(99, 102, 241, 0.2);
  padding: 1.25rem;
  border-radius: 14px;
  display: flex;
  align-items: center;
  gap: 0.8rem;
  color: #e0e7ff;
  font-size: 1.1rem;
}

.ai-preview strong {
  color: #fff;
  font-size: 1.25rem;
}

.ai-preview .icon-xs {
  color: #8b5cf6;
}

.menu-card {
  overflow: hidden;
  border-radius: 24px;
}

.menu-img { width: 100%; height: 220px; object-fit: cover; }

.menu-info {
  padding: 2.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.menu-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.menu-header h3 {
  font-size: 1.5rem;
  font-weight: 800;
  color: #fff;
}

.menu-price {
  background: rgba(99, 102, 241, 0.1);
  color: var(--accent-primary);
  padding: 0.5rem 1rem;
  border-radius: 10px;
  font-weight: 800;
  font-size: 0.9rem;
}

.menu-info p {
  color: var(--text-secondary);
  line-height: 1.6;
  font-size: 1.05rem;
}

.menu-features {
  list-style: none;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.menu-features li {
  color: var(--text-muted);
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.gallery-item {
  border-radius: 18px;
  overflow: hidden;
  aspect-ratio: 16/10;
}

/* ---------------------------------------------------------
   ANIMATIONS & UTILS
   --------------------------------------------------------- */
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-20px); }
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.success-alert { color: #10b981; background: rgba(16, 185, 129, 0.1); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(16, 185, 129, 0.2); margin-bottom: 2rem; text-align: center; }
.error-alert { color: #ef4444; background: rgba(239, 68, 68, 0.1); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(239, 68, 68, 0.2); margin-bottom: 2rem; text-align: center; }

@media (max-width: 1024px) {
  .hero-section { grid-template-columns: 1fr; text-align: center; }
  .hero-content p { margin: 0 auto 2.5rem; }
  .hero-actions { justify-content: center; }
  .form-row { grid-template-columns: 1fr; }
}
</style>
