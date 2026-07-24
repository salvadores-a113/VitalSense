import os
import google.generativeai as genai
from flask import Flask, render_template, request, jsonify
import requests
import re
from dotenv import load_dotenv

# Cargar variables de entorno
load_dotenv()

# Configura la API Key de Gemini desde variables de entorno
GENAI_API_KEY = os.getenv("GENAI_API_KEY")
GOOGLE_API_KEY = os.getenv("GOOGLE_API_KEY")
CX_ID = os.getenv("CX_ID")

if not GENAI_API_KEY or not GOOGLE_API_KEY or not CX_ID:
    raise ValueError("Faltan claves API en las variables de entorno")

genai.configure(api_key=GENAI_API_KEY)
app = Flask(__name__)

def limpiar_texto(texto):
    texto_limpio = re.sub(r"\*\*", "", texto)  # Elimina asteriscos dobles
    texto_limpio = re.sub(r"\s+", " ", texto_limpio)  # Reemplaza múltiples espacios por uno solo
    return texto_limpio.strip()

def extraer_info(texto):
    patrones = {
        "cuidado": r"(?:cuidado|recomendaciones de cuidado)[:\-]?\s*(.*?)(?=(\n|$))",
        "riego": r"(?:riego|recomendaciones de riego)[:\-]?\s*(.*?)(?=(\n|$))",
        "luz": r"(?:luz|recomendaciones de luz)[:\-]?\s*(.*?)(?=(\n|$))",
        "uso": r"(?:uso|usos (gastronómicos|medicinales))[:\-]?\s*(.*?)(?=(\n|$))"
    }
    
    info = {clave: "No disponible" for clave in patrones}
    for clave, patron in patrones.items():
        match = re.search(patron, texto, re.IGNORECASE | re.DOTALL)
        if match:
            info[clave] = match.group(1).strip()
    
    return info

@app.route("/get_planta_info", methods=["POST"])
def get_planta_info():
    data = request.get_json()
    planta = data.get("planta")

    if not planta:
        return jsonify({"error": "No se proporcionó el nombre de la planta"}), 400

    try:
        model = genai.GenerativeModel("gemini-1.5-pro")
        response = model.generate_content(f"Dame recomendaciones de cuidado, riego, luz y usos de {planta}.")
        texto_limpio = limpiar_texto(response.text)
        info_extraida = extraer_info(texto_limpio)
    except Exception as e:
        return jsonify({"error": f"Error al obtener información de Gemini: {str(e)}"}), 500

    image_url = None
    try:
        search_url = f"https://www.googleapis.com/customsearch/v1?q={planta}&searchType=image&key={GOOGLE_API_KEY}&cx={CX_ID}"
        response = requests.get(search_url)
        response.raise_for_status()
        results = response.json()
        if "items" in results:
            image_url = results["items"][0]["link"]
    except requests.exceptions.RequestException as e:
        print(f"Error al obtener la imagen: {e}")

    return jsonify({
        "info": info_extraida,
        "image_url": image_url or "No disponible"
    })

@app.route("/", methods=["GET"])
def index():
    return render_template("index.html")

if __name__ == "__main__":
    app.run(debug=True)
