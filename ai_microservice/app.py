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

# 3. ENDPOINT DE L'API
@app.route('/predict-cost', methods=['POST'])
def predict_cost():
    try:
        # Obtenir les dades de la petició
        data = request.get_json()
        
        # Validació bàsica
        if not data:
            return jsonify({'success': False, 'error': 'No JSON received'}), 400
            
        event_type = data.get('type', 'party')
        assistants = int(data.get('assistants', 0))
        workers = int(data.get('workers', 0))
        hours = int(data.get('hours', 0))
        cost_per_hour = float(data.get('cost_per_hour', 0.0))
        
        # Preparar les dades per predir (el model espera un DataFrame 2D)
        input_data = pd.DataFrame([[event_type, assistants, workers, hours, cost_per_hour]], 
                                  columns=['type', 'assistants', 'workers', 'hours', 'cost_per_hour'])
        
        # Fer la predicció
        predicted_cost = model.predict(input_data)[0]
        
        # Evitar costos negatius
        final_cost = max(0, predicted_cost)
        
        return jsonify({
            'success': True,
            'predicted_cost': round(final_cost, 2),
            'currency': 'EUR',
            'inputs_used': {
                'type': event_type,
                'assistants': assistants,
                'workers': workers,
                'hours': hours,
                'cost_per_hour': cost_per_hour
            }
        })
        
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 400

# Ruta simple per comprovar que el servei funciona
@app.route('/', methods=['GET'])
def health_check():
    return jsonify({"status": "AI Microservice is running!"})

if __name__ == '__main__':
    # Arrancar en el port 5000
    app.run(host='0.0.0.0', port=5000, debug=True)
