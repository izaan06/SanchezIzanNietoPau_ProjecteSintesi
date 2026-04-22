<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h2 class="login-title">{{ isRegisterMode ? 'Crea un compte' : 'Benvingut de nou' }}</h2>
        <p class="login-subtitle">
          {{ isRegisterMode ? 'Omple les dades per registrar-te' : 'Inicia sessió per continuar' }}
        </p>
      </div>

      <form @submit.prevent="handleSubmit" class="login-form">
        
        <div v-if="isRegisterMode" class="form-group">
          <label for="name">Nom Complet</label>
          <div class="input-wrapper">
            <input
              type="text"
              id="name"
              v-model="name"
              placeholder="El teu nom"
              required
              :disabled="isLoading"
            />
          </div>
        </div>

        <div class="form-group">
          <label for="email">Correu electrònic</label>
          <div class="input-wrapper">
            <input
              type="email"
              id="email"
              v-model="email"
              placeholder="exemple@correu.com"
              required
              :disabled="isLoading"
            />
          </div>
        </div>

        <div class="form-group">
          <label for="password">Contrasenya</label>
          <div class="input-wrapper">
            <input
              type="password"
              id="password"
              v-model="password"
              placeholder="••••••••"
              required
              :disabled="isLoading"
            />
          </div>
        </div>

        <div v-if="isRegisterMode" class="form-group">
          <label for="password_confirmation">Confirmar Contrasenya</label>
          <div class="input-wrapper">
            <input
              type="password"
              id="password_confirmation"
              v-model="password_confirmation"
              placeholder="••••••••"
              required
              :disabled="isLoading"
            />
          </div>
        </div>

        <div v-if="errorMessage" class="error-message">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          {{ errorMessage }}
        </div>
        
        <div v-if="successMessage" class="success-message">
          {{ successMessage }}
        </div>

        <button type="submit" class="login-button" :disabled="isLoading">
          <span v-if="isLoading" class="loader"></span>
          <span v-else>{{ isRegisterMode ? 'Registrar-se' : 'Iniciar sessió' }}</span>
        </button>
      </form>

      <div class="toggle-mode">
        <button type="button" class="btn-text" @click="toggleMode" :disabled="isLoading">
          {{ isRegisterMode ? 'Ja tens un compte? Inicia sessió' : 'No tens compte? Registra\'t' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api/axios';

const router = useRouter();

const isRegisterMode = ref(false);

const name = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref('');

const errorMessage = ref('');
const successMessage = ref('');
const isLoading = ref(false);

const toggleMode = () => {
  isRegisterMode.value = !isRegisterMode.value;
  errorMessage.value = '';
  successMessage.value = '';
};

const handleSubmit = async () => {
  if (isRegisterMode.value) {
    await handleRegister();
  } else {
    await handleLogin();
  }
};

const handleRegister = async () => {
  isLoading.value = true;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    const response = await api.post('/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value
    });

    successMessage.value = 'Registre completat correctament! Ara pots iniciar sessió.';
    setTimeout(() => {
      isRegisterMode.value = false;
      password.value = '';
      password_confirmation.value = '';
    }, 2000);

  } catch (error) {
    if (error.response?.data?.errors) {
      // Mostrar el primer error de validació
      const firstErrorKey = Object.keys(error.response.data.errors)[0];
      errorMessage.value = error.response.data.errors[firstErrorKey][0];
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = 'Error en el registre. Torna a provar-ho.';
    }
  } finally {
    isLoading.value = false;
  }
};

const handleLogin = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
    });

    const token = response.data.token || response.data.access_token;
    
    if (token) {
      localStorage.setItem('token', token);
      router.push({ name: 'dashboard' });
    } else {
      errorMessage.value = 'El servidor no ha retornat cap token.';
    }
  } catch (error) {
    if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = 'Credencials incorrectes o error de xarxa.';
    }
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #0f172a; /* Dark mode style background */
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  padding: 1rem;
}

.login-card {
  background: rgba(30, 41, 59, 0.7);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  padding: 3rem 2.5rem;
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  width: 100%;
  max-width: 420px;
}

.login-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.login-title {
  color: #f8fafc;
  margin: 0 0 0.5rem 0;
  font-size: 1.875rem;
  font-weight: 700;
  letter-spacing: -0.025em;
}

.login-subtitle {
  color: #94a3b8;
  margin: 0;
  font-size: 0.95rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #cbd5e1;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

input {
  width: 100%;
  padding: 0.875rem 1rem;
  background-color: rgba(15, 23, 42, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  font-size: 1rem;
  color: #f8fafc;
  transition: all 0.3s ease;
}

input::placeholder {
  color: #475569;
}

input:focus {
  outline: none;
  border-color: #3b82f6;
  background-color: rgba(15, 23, 42, 0.8);
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
}

input:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.error-message {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #fca5a5;
  font-size: 0.875rem;
  background-color: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.2);
  padding: 0.75rem 1rem;
  border-radius: 8px;
}

.success-message {
  color: #10b981;
  font-size: 0.875rem;
  background-color: rgba(16, 185, 129, 0.1);
  border: 1px solid rgba(16, 185, 129, 0.2);
  padding: 0.75rem 1rem;
  border-radius: 8px;
  text-align: center;
}

.login-button {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  padding: 0.875rem;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 0.5rem;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 50px;
}

.login-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
}

.login-button:active:not(:disabled) {
  transform: translateY(0);
}

.login-button:disabled {
  background: #475569;
  color: #94a3b8;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.toggle-mode {
  text-align: center;
  margin-top: 1.5rem;
}

.btn-text {
  background: none;
  border: none;
  color: #94a3b8;
  font-family: inherit;
  font-size: 0.9rem;
  cursor: pointer;
  transition: color 0.2s;
}

.btn-text:hover {
  color: #3b82f6;
  text-decoration: underline;
}

.loader {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: #fff;
  animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
