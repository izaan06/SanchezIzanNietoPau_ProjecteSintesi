import pytest
import sys
import os

# Afegim la carpeta pare al path perquè Python pugui trobar app.py
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from fastapi.testclient import TestClient
from app import app  # Importem l'aplicació FastAPI

# Creem un client de proves per enviar peticions HTTP sense necessitat d'engegar el servidor
client = TestClient(app)

def test_health_check():
    """
    Test bàsic per comprovar que l'API està funcionant.
    Fa una petició GET a l'arrel (/) i espera un codi 200 (OK).
    """
    response = client.get("/")
    assert response.status_code == 200
    assert response.json() == {"status": "FastAPI AI Microservice is running!"}

def test_predict_budget_success():
    """
    Test per comprovar que l'endpoint de predicció de pressupost funciona
    correctament quan se li passen dades vàlides.
    """
    # Dades de prova
    payload = {
        "event_type": "festa privada",
        "attendees": 100,
        "location_name": "Barcelona",
        "client_budget": 5000
    }
    
    response = client.post("/predict-budget", json=payload)
    
    # Comprovem que la petició ha tingut èxit
    assert response.status_code == 200
    
    data = response.json()
    # Comprovem que retorna un diccionari amb èxit i costos
    assert data["success"] is True
    assert "estimated_cost" in data
    assert "recommended_price" in data
    
def test_predict_cost_invalid_data():
    """
    Test per verificar que si enviem tipus de dades incorrectes, 
    FastAPI (Pydantic) ens retorna un error 422 (Unprocessable Entity).
    """
    payload = {
        "type": "boda",
        "assistants": "cent" # Error: hauria de ser un número (int), no un string
    }
    
    response = client.post("/predict-cost", json=payload)
    assert response.status_code == 422

# =====================================================================
# INSTRUCCIONS PER EXECUTAR AQUESTS TESTS:
# 1. Obre un terminal a la carpeta 'ai_microservice'
# 2. Activa l'entorn virtual: .\.venv\Scripts\Activate.ps1
# 3. Assegura't d'instal·lar les dependències: pip install -r requirements.txt
# 4. Executa la comanda: pytest tests/
# =====================================================================
