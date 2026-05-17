from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List, Optional
import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression
from sklearn.preprocessing import OneHotEncoder
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline

# ==========================================
# 1. DEFINICIÓ DE MODELS DE DADES (Pydantic)
# ==========================================
# Aquests models validen automàticament que les dades JSON rebudes des del Backend siguin correctes.
class PredictCostRequest(BaseModel):
    type: str = "party"
    assistants: int = 0
    workers: int = 0
    hours: int = 0
    cost_per_hour: float = 0.0

class PredictBudgetRequest(BaseModel):
    event_type: str = "party"
    attendees: int = 50
    location_name: Optional[str] = ""
    client_budget: Optional[float] = None

class WorkerInfo(BaseModel):
    id: Optional[int]
    name: str
    rating: float = 0.0
    specialization_tags: List[str] = []
    availability: bool = True

class RecommendWorkersRequest(BaseModel):
    event_type: str
    workers: List[WorkerInfo]

# ==========================================
# 2. INICIALITZACIÓ DE L'APP (FastAPI)
# ==========================================
app = FastAPI(
    title="EventAI Prediction API",
    description="API de microserveis d'IA per a la gestió d'esdeveniments",
    version="2.0.0"
)

# Configuració de CORS (Intercanvi de Recursos d'Origen Creuat)
# Permet que el Backend de Laravel o Vue.js es comuniquin amb aquest servidor sense ser bloquejats.
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

print("Iniciant entrenament del model d'IA...")

# ==========================================
# 3. LÒGICA D'IA (Entrenament del model)
# ==========================================
# IMPORTANT: Actualment s'està generant dades i entrenant el model cada cop que s'engega el servidor.
# Per a producció, el model s'hauria d'entrenar un cop, guardar-lo en un fitxer .pkl i només carregar-lo aquí.
types = ['boda', 'corporatiu', 'aniversari', 'festa privada', 'concert']
data = []
for _ in range(1000):
    t = np.random.choice(types)
    assistants = np.random.randint(10, 1000)
    workers = np.random.randint(2, 50)
    hours = np.random.randint(2, 24)
    cost_per_hour = np.random.uniform(15.0, 40.0)
    
    type_premium = {'boda': 8000, 'corporatiu': 5000, 'aniversari': 1500, 'festa privada': 2000, 'concert': 10000}[t]
    real_cost = 3000 + (assistants * 125) + (workers * hours * cost_per_hour) + type_premium
    noise = np.random.normal(0, 150) 
    data.append([t, assistants, workers, hours, cost_per_hour, real_cost + noise])

df = pd.DataFrame(data, columns=['type', 'assistants', 'workers', 'hours', 'cost_per_hour', 'cost'])
X = df[['type', 'assistants', 'workers', 'hours', 'cost_per_hour']]
y = df['cost']

preprocessor = ColumnTransformer(
    transformers=[('cat', OneHotEncoder(handle_unknown='ignore'), ['type'])],
    remainder='passthrough'
)

model = Pipeline(steps=[
    ('preprocessor', preprocessor),
    ('regressor', LinearRegression())
])

model.fit(X, y)
print("Model entrenat correctament! FastAPI llista.")

# ==========================================
# 4. ENDPOINTS DE L'API (Rutes)
# ==========================================

@app.post('/predict-cost')
async def predict_cost(data: PredictCostRequest):
    """
    Endpoint per predir el cost de producció d'un esdeveniment
    utilitzant el model de regressió lineal entrenat prèviament.
    """
    try:
        input_df = pd.DataFrame([[data.type, data.assistants, data.workers, data.hours, data.cost_per_hour]], 
                                columns=['type', 'assistants', 'workers', 'hours', 'cost_per_hour'])
        
        predicted_cost = model.predict(input_df)[0]
        final_cost = max(0, predicted_cost)
        
        return {
            'success': True,
            'predicted_cost': round(final_cost, 2),
            'currency': 'EUR'
        }
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))

@app.post('/predict-budget')
async def predict_budget(data: PredictBudgetRequest):
    """
    Endpoint per generar un pressupost estimat i donar recomanacions intel·ligents.
    Utilitza lògica de negoci i condicions (sistema expert) en lloc de ML pur.
    """
    try:
        event_type = data.event_type.lower()
        attendees = data.attendees
        location = data.location_name
        
        cost_per_person = 85
        base_costs = {
            'boda': 5000, 
            'corporatiu': 3500, 
            'aniversari': 1000, 
            'festa privada': 1500, 
            'concert': 7000
        }
        
        operational_cost = base_costs.get(event_type, 1500) + (attendees * cost_per_person)
        margin = 0.25
        profit = operational_cost * margin
        final_price = (operational_cost + profit) * np.random.uniform(0.95, 1.05)
        
        predicted_address = "Adreça no identificada"
        if location:
            if any(word in location.lower() for word in ['barcelona', 'bcn']):
                predicted_address = f"Barcelona (Prop de {location})"
            elif any(word in location.lower() for word in ['madrid', 'mad']):
                predicted_address = f"Madrid (Cerca de {location})"
            else:
                predicted_address = f"{location} (Zona centre)"

        budget_feedback = "Pressupost no especificat."
        if data.client_budget:
            if data.client_budget >= final_price:
                budget_feedback = "El pressupost del client cobreix el preu recomanat."
            else:
                budget_feedback = f"El pressupost és un {int((1 - data.client_budget/final_price)*100)}% insuficient."

        return {
            'success': True,
            'estimated_cost': round(operational_cost, 2),
            'recommended_price': round(final_price, 2),
            'estimated_profit': round(profit, 2),
            'predicted_address': predicted_address,
            'recommendations': [
                f"S'estimen {max(2, attendees // 20)} treballadors per a aquest esdeveniment.",
                budget_feedback,
                "Es recomana espai amb ventilació natural.",
                "Incloure servei de seguretat segons la ubicació."
            ]
        }
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))

@app.post('/recommend-workers')
async def recommend_workers(data: RecommendWorkersRequest):
    try:
        scored_workers = []
        for w in data.workers:
            score = float(w.rating) * 10
            tags = [t.lower() for t in w.specialization_tags]
            if data.event_type.lower() in tags: score += 30
            if w.availability: score += 20
                
            scored_workers.append({
                'id': w.id,
                'name': w.name,
                'score': score,
                'reason': f"Coincidència de perfil del {int(score)}%"
            })
            
        scored_workers.sort(key=lambda x: x['score'], reverse=True)
        return {'success': True, 'recommendations': scored_workers[:5]}
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))

@app.get('/')
async def health_check():
    return {"status": "FastAPI AI Microservice is running!"}

if __name__ == '__main__':
    import uvicorn
    uvicorn.run(app, host='0.0.0.0', port=5000)
