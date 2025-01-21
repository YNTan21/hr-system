from flask import Flask, request, jsonify
from flask_cors import CORS
from keras.models import load_model
import numpy as np
import cv2

# Initialize Flask app
app = Flask(__name__)
CORS(app)  # Allow all origins

# Load the pre-trained model
model = load_model('model.h5')

def preprocess_image(image, size=(90, 90)):
    """Preprocess fingerprint image for model input"""
    # Apply Gaussian blur and adaptive thresholding
    img = cv2.GaussianBlur(image, (5, 5), 0)
    img = cv2.adaptiveThreshold(img, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY, 11, 2)
    
    # Resize and normalize
    img = cv2.resize(img, size)
    img = img.astype(np.float32) / 255.0
    img = np.expand_dims(img, axis=-1)
    return img

@app.route('/verify', methods=['POST'])
def verify():
    try:
        # Get uploaded fingerprint
        fingerprint = request.files['fingerprint']
        img_array = np.frombuffer(fingerprint.read(), np.uint8)
        img = cv2.imdecode(img_array, cv2.IMREAD_GRAYSCALE)
        input_fp = preprocess_image(img)

        # Get template fingerprints
        template_scores = []
        for i in range(1, 6):
            template = request.files[f'template{i}']
            template_array = np.frombuffer(template.read(), np.uint8)
            template_img = cv2.imdecode(template_array, cv2.IMREAD_GRAYSCALE)
            template_fp = preprocess_image(template_img)
            
            # Predict match score
            score = model.predict([
                np.expand_dims(input_fp, axis=0),
                np.expand_dims(template_fp, axis=0)
            ])[0][0]
            
            template_scores.append(float(score))

        # Get best match score
        best_score = max(template_scores)
        threshold = 0.7  # Configurable threshold
        is_match = best_score >= threshold

        response = {
            "match": bool(is_match),
            "confidence": best_score * 100,  # Convert to percentage
            "template_scores": [score * 100 for score in template_scores],  # All scores as percentages
            "status": "success" if is_match else "failure",
            "message": f"Fingerprint {'matched' if is_match else 'not matched'} with {best_score * 100:.2f}% confidence"
        }

        return jsonify(response), 200

    except Exception as e:
        return jsonify({
            "status": "error",
            "message": str(e)
        }), 500

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000, debug=True)
