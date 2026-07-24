import google.generativeai as genai

# Configura tu API Key directamente
genai.configure(api_key="AIzaSyBwCgAQ2lxJ29nB3EvoB2Hm09mcpnmX5do")

# Verifica los modelos disponibles
models = genai.list_models()
for model in models:
    print(model.name)
