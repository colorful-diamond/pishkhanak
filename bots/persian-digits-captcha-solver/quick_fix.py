#!/usr/bin/env python3
"""
Quick fix script - test the API prediction with the corrected shapes
"""

import numpy as np
import cv2
import keras
import base64
import json
from captcha_processor import process_captcha_image, extract_digit_rois_for_prediction

# Load model
print("Loading model...")
model = keras.models.load_model('persian_digit.keras')
print(f"Model input shape: {model.input_shape}")

# Persian digits mapping
PERSIAN_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹']

# Create a test image or load one
test_image = np.random.randint(0, 255, (40, 163, 3), dtype=np.uint8)

print("Testing the fixed prediction pipeline...")
print("=" * 50)

try:
    # Step 1: Process captcha image
    binary_image, target_colors, confidence_info = process_captcha_image(
        test_image,
        color_tolerance=30,
        use_pattern_shadow_detection=True
    )
    
    print(f"✅ Step 1: Binary image shape: {binary_image.shape}")
    
    # Step 2: Extract digit ROIs
    digit_images_array = extract_digit_rois_for_prediction(binary_image)
    
    print(f"✅ Step 2: Digit array shape: {digit_images_array.shape}")
    print(f"   Expected: (5, 40, 30, 1)")
    print(f"   Model expects: {model.input_shape}")
    
    # Step 3: Make prediction
    if digit_images_array.shape[1:] == (40, 30, 1):
        predictions = model.predict(digit_images_array, verbose=0)
        predicted_digits = np.argmax(predictions, axis=1)
        confidence_scores = predictions.max(axis=1)
        
        # Convert to Persian digits
        predicted_text = ''.join([PERSIAN_DIGITS[digit] for digit in predicted_digits])
        
        print(f"✅ Step 3: Prediction successful!")
        print(f"   Predicted text: {predicted_text}")
        print(f"   Predicted digits: {predicted_digits.tolist()}")
        print(f"   Confidence scores: {confidence_scores.tolist()}")
        
        # Test the exact API response format
        api_response = {
            'success': True,
            'predicted_text': predicted_text,
            'predicted_digits': predicted_digits.tolist(),
            'confidence_scores': confidence_scores.tolist(),
            'target_colors': [color.tolist() if color is not None else None for color in target_colors],
            'processing_info': confidence_info,
        }
        
        print(f"✅ Step 4: API response format test passed")
        print(f"   Response: {json.dumps(api_response, indent=2)[:200]}...")
        
    else:
        print(f"❌ Shape mismatch: got {digit_images_array.shape[1:]} but model expects (40, 30, 1)")
        
except Exception as e:
    print(f"❌ Error: {e}")
    import traceback
    traceback.print_exc()

print("\n✅ Quick fix test completed")