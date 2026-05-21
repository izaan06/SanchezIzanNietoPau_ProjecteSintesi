import axios from 'axios';

// Creem una instància personalitzada d'Axios per centralitzar totes les crides a l'API
const api = axios.create({
  // URL base per a totes les peticions. A Kubernetes, Nginx actuarà com a proxy invers redirigint les consultes a '/api' cap al backend de Laravel
  baseURL: '/api', 
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  }
});

// Interceptor de petició (Request): S'executa AUTOMÀTICAMENT abans que surti qualsevol crida HTTP al servidor
api.interceptors.request.use(
  (config) => {
    // Recuperem el token de seguretat (Laravel Sanctum) del localStorage del navegador
    const token = localStorage.getItem('token');
    
    // Si tenim un token guardat, l'afegim automàticament a la capçalera de la petició en format Bearer Token
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Interceptor de resposta (Response): S'executa AUTOMÀTICAMENT quan arriba qualsevol resposta des del backend
api.interceptors.response.use(
  (response) => response, // Si la petició ha tingut èxit, es retorna la resposta intacta
  (error) => {
    // Si el servidor ens retorna un error HTTP 401 (No autoritzat / Sessió caducada / Token invàlid)
    if (error.response && error.response.status === 401) {
      // Esborrem el token inservible o expirat de l'emmagatzematge local per motius de seguretat
      localStorage.removeItem('token');
      
      // Redirigim de forma forçada l'usuari a la pantalla d'inici de sessió (Login)
      // Nota: En una SPA típica es faria via router.push('/login'), però fer servir window.location és més segur per forçar el refresc global d'estat
      window.location.href = '/login'; 
    }
    return Promise.reject(error);
  }
);

export default api;
