from flask import Flask, request, jsonify
from flask_cors import CORS
import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression
from sklearn.preprocessing import OneHotEncoder
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline

app = Flask(__name__)
CORS(app) # Permet connexions des del frontend Vue.js

print("Iniciant entrenament del model d'IA...")

# 1. GENERACIÓ DE DADES SINTÈTIQUES PER L'ENTRENAMENT
# Com que necessitem un model de regressió lineal actiu, crearem dades 
# fictícies basades en una lògica realista de costos d'esdeveniments.
types = ['wedding', 'conference', 'party', 'corporate']
data = []
for _ in range(1000):
    t = np.random.choice(types)
    assistants = np.random.randint(10, 1000)
    workers = np.random.randint(2, 50)
    hours = np.random.randint(2, 24)
    cost_per_hour = np.random.uniform(15.0, 40.0)
    
    # Lògica base del cost (Alta Gamma / Realista):
    # Cost fix de l'esdeveniment + 125€ per assistent (càtering/lloc) + salari treballadors
    type_premium = {'wedding': 8000, 'conference': 3000, 'corporate': 5000, 'party': 1500}[t]
    real_cost = 3000 + (assistants * 125) + (workers * hours * cost_per_hour) + type_premium
    
    # Afegim una mica de "soroll" (variància aleatòria) perquè el model l'hagi d'aprendre
    noise = np.random.normal(0, 150) 
    
    data.append([t, assistants, workers, hours, cost_per_hour, real_cost + noise])

df = pd.DataFrame(data, columns=['type', 'assistants', 'workers', 'hours', 'cost_per_hour', 'cost'])

X = df[['type', 'assistants', 'workers', 'hours', 'cost_per_hour']]
y = df['cost']

# 2. CREACIÓ DEL MODEL DE REGRESSIÓ LINEAL AMB PIPELINE
# Transformem el camp de text 'type' en números usant OneHotEncoder
preprocessor = ColumnTransformer(
    transformers=[
        ('cat', OneHotEncoder(handle_unknown='ignore'), ['type'])
    ],
    remainder='passthrough' # Mantenim les columnes numèriques tal qual
)

# Acoblem el preprocessador i l'algorisme de Regressió Lineal
model = Pipeline(steps=[
    ('preprocessor', preprocessor),
    ('regressor', LinearRegression())
])

# Entrenem el model
model.fit(X, y)
print("Model entrenat correctament! A punt per fer prediccions.")

# 3. ENDPOINTS DE L'API

@app.route('/predict-cost', methods=['POST'])
def predict_cost():
    try:
        data = request.get_json()
        if not data: return jsonify({'success': False, 'error': 'No JSON received'}), 400
            
        event_type = data.get('type', 'party')
        assistants = int(data.get('assistants', 0))
        workers = int(data.get('workers', 0))
        hours = int(data.get('hours', 0))
        cost_per_hour = float(data.get('cost_per_hour', 0.0))
        
        input_data = pd.DataFrame([[event_type, assistants, workers, hours, cost_per_hour]], 
                                  columns=['type', 'assistants', 'workers', 'hours', 'cost_per_hour'])
        
        predicted_cost = model.predict(input_data)[0]
        final_cost = max(0, predicted_cost)
        
        return jsonify({
            'success': True,
            'predicted_cost': round(final_cost, 2),
            'currency': 'EUR'
        })
    except Exception as e:
        return jsonify({'success': False, 'error': str(e)}), 400

@app.route('/predict-budget', methods=['POST'])
def predict_budget():
    """Predicció ràpida del pressupost per a una sol·licitud de cita."""
    try:
        data = request.get_json()
        event_type = data.get('event_type', 'party').lower()
        attendees = int(data.get('attendees', 50))
        location = data.get('location_name', '')
        
        # Lògica heurística d'IA:
        # 1. Cost Base (el que li costa a l'empresa: menjar, cadires, local, etc.)
        cost_per_person = 85 # Cost mitjà de càtering i logística
        base_costs = {'wedding': 5000, 'conference': 2500, 'corporate': 3500, 'party': 1000, 'concert': 7000}
        
        operational_cost = base_costs.get(event_type, 1500) + (attendees * cost_per_person)
        
        # 2. Margen de Benefici (25% per defecte)
        margin = 0.25
        profit = operational_cost * margin
        
        # 3. Preu Final (el que paga el client)
        final_price = operational_cost + profit
        
        # Afegir variància per simular intel·ligència
        final_price = final_price * np.random.uniform(0.95, 1.05)
        
        # Simulació de geocodificació
        predicted_address = "Adreça no identificada"
        if location:
            if any(word in location.lower() for word in ['barcelona', 'bcn']):
                predicted_address = f"Barcelona (Prop de {location})"
            elif any(word in location.lower() for word in ['madrid', 'mad']):
                predicted_address = f"Madrid (Cerca de {location})"
            else:
                predicted_address = f"{location} (Zona centre)"

        # Feedback de pressupost
        budget_feedback = "Pressupost no especificat."
        if data.get('client_budget'):
            c_budget = float(data.get('client_budget'))
            if c_budget >= final_price:
                budget_feedback = "El pressupost del client cobreix el preu recomanat."
            else:
                budget_feedback = f"El pressupost és un {int((1 - c_budget/final_price)*100)}% insuficient."

        return jsonify({
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
        })
    except Exception as e:
        return jsonify({'success': False, 'error': str(e)}), 400

@app.route('/recommend-workers', methods=['POST'])
def recommend_workers():
    """Recomana els millors treballadors basant-se en especialitat, puntuació i proximitat."""
    try:
        data = request.get_json()
        event_type = data.get('event_type', '').lower()
        workers_list = data.get('workers', [])
        
        if not workers_list:
            return jsonify({'success': False, 'error': 'No workers provided'}), 400

        scored_workers = []
        for w in workers_list:
            score = float(w.get('rating', 0)) * 10
            
            # Bonus per especialitat
            tags = [t.lower() for t in w.get('specialization_tags', [])]
            if event_type in tags:
                score += 30
            
            # Bonus per disponibilitat
            if w.get('availability'):
                score += 20
                
            scored_workers.append({
                'id': w.get('id'),
                'name': w.get('name'),
                'score': score,
                'reason': f"Coincidència de perfil del {int(score)}%"
            })
            
        # Ordenar per puntuació descendent
        scored_workers.sort(key=lambda x: x['score'], reverse=True)
        
        return jsonify({
            'success': True,
            'recommendations': scored_workers[:5] # Retornar els 5 millors
        })
    except Exception as e:
        return jsonify({'success': False, 'error': str(e)}), 400

@app.route('/', methods=['GET'])
def health_check():
    return jsonify({"status": "AI Microservice is running!"})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
