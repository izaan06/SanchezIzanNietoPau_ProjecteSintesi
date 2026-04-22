# EventAI Manager 🚀

**EventAI Manager** és una plataforma integral de gestió d'esdeveniments que combina una arquitectura moderna amb intel·ligència artificial per optimitzar la planificació i el càlcul de costos. Aquest projecte ha estat desenvolupat com a part del projecte de síntesi del cicle de Grau Superior en Desenvolupament d'Aplicacions Web (DAW).

---

## 📋 Descripció del Projecte

La plataforma permet gestionar esdeveniments, clients i personal de manera centralitzada. El valor diferencial és la integració d'un **microservei d'IA** capaç de predir el cost estimat d'un esdeveniment basant-se en variables com el tipus d'esdeveniment, el nombre d'assistents, el personal necessari i la durada.

## 🏗️ Arquitectura

El projecte està estructurat com un monorepositori amb tres components principals:

1.  **Backend (`/backend`)**: API REST robusta construïda amb Laravel.
2.  **Frontend (`/frontend`)**: Interfície d'usuari dinàmica i moderna creada amb Vue.js 3.
3.  **AI Microservice (`/ai_microservice`)**: Servei de predicció basat en Python i Scikit-learn.

---

## 🛠️ Tecnologies Utilitzades

### **Backend (API)**
*   **Framework:** Laravel 11/12
*   **Llenguatge:** PHP
*   **Base de dades:** MySQL
*   **Autenticació:** Laravel Sanctum
*   **Eines:** Composer, Artisan

### **Frontend (SPA)**
*   **Framework:** Vue.js 3 (Composition API)
*   **Eina de construcció:** Vite
*   **Estils:** CSS Modern (Glassmorphism, Responsive Design)
*   **Estat:** Pinia / Vue Router
*   **Icones:** Lucide Vue / Heroicons

### **Microservei d'IA**
*   **Llenguatge:** Python 3.x
*   **Framework:** Flask
*   **IA/ML:** Scikit-learn (Linear Regression), Pandas, NumPy
*   **Servidor:** Flask Development Server

---

## 🚀 Instal·lació i Configuració

### **Requisits previs**
*   PHP >= 8.2
*   Composer
*   Node.js & npm
*   Python 3.x
*   MySQL

### **Passos per a la instal·lació**

1.  **Clonar el repositori:**
    ```bash
    git clone https://github.com/izaan06/SanchezzIzanNietoPau_ProjecteSintesi.git
    cd Projecte
    ```

2.  **Configuració del Backend:**
    ```bash
    cd backend
    composer install
    cp .env.example .env
    # Configura la teva base de dades al fitxer .env
    php artisan key:generate
    php artisan migrate --seed
    ```

3.  **Configuració del Frontend:**
    ```bash
    cd ../frontend
    npm install
    ```

4.  **Configuració del Microservei d'IA:**
    ```bash
    cd ../ai_microservice
    pip install flask flask-cors pandas numpy scikit-learn
    ```

---

## 💻 Com executar el projecte

Pots executar tots els serveis simultàniament des de l'arrel del projecte si tens `concurrently` instal·lat:

```bash
npm run dev
```

O bé, manualment en terminals separades:

*   **Backend:** `php artisan serve` (Port 8000)
*   **Frontend:** `npm run dev` (Port 5173)
*   **AI:** `python app.py` (Port 5000)

---

## ✨ Característiques Principals

*   🔐 **Autenticació segura:** Sistema de login i registre amb tokens.
*   📊 **Dashboard Premium:** Disseny visualment impactant amb estètica *Glassmorphism*.
*   🤖 **Predicció de Costos:** Motor d'IA que analitza paràmetres de l'esdeveniment per donar un pressupost realista.
*   👥 **Gestió de Clients:** CRUD complet per a la base de dades de clients.
*   📱 **Disseny Responsive:** Adaptat per a dispositius mòbils i escriptori.

---

## 👤 Autors

*   **Izan Sánchez** - [izaan06](https://github.com/izaan06)
*   **Pau Nieto**

---
⭐ *Projecte realitzat per al mòdul de Síntesi de 2n de DAW.*
