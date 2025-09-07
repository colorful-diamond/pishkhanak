#!/usr/bin/env python3
"""
Test script to verify the captcha API fix
"""

import requests
import base64
import numpy as np
import cv2
import json

def create_test_image():
    """Create a simple test captcha-like image"""
    # Create a 40x163 test image with some patterns
    image = np.random.randint(200, 255, (40, 163, 3), dtype=np.uint8)
    
    # Add some simple digit-like shapes in the expected positions
    positions = [(5, 6, 25, 32), (37, 6, 25, 32), (69, 6, 25, 32), (101, 6, 25, 32), (133, 6, 25, 32)]
    
    for i, (x, y, w, h) in enumerate(positions):
        # Add a simple rectangle pattern
        color = [50 + i*30, 100, 150]  # Different colors for each digit
        cv2.rectangle(image, (x+5, y+5), (x+w-5, y+h-5), color, -1)
        
        # Add some noise lines
        cv2.line(image, (x, y+10), (x+w, y+10), [200, 200, 200], 1)
        cv2.line(image, (x+10, y), (x+10, y+h), [200, 200, 200], 1)
    
    return image

def test_api():
    """Test the captcha API"""
    print("🧪 Testing Captcha API Fix")
    print("=" * 40)
    
    # Check if API is running
    try:
        health_response = requests.get('http://127.0.0.1:9090/health', timeout=5)
        if health_response.status_code == 200:
            health_data = health_response.json()
            print(f"✅ API is running")
            print(f"   Model loaded: {health_data.get('model_loaded', False)}")
            print(f"   Prediction count: {health_data.get('prediction_count', 0)}")
        else:
            print(f"❌ API health check failed: HTTP {health_response.status_code}")
            return False
    except requests.exceptions.RequestException as e:
        print(f"❌ Cannot connect to API: {e}")
        print("💡 Make sure the captcha API is running on port 8989")
        return False
    
    # Create test image
    print("\n🖼️ Creating test image...")
    test_image = create_test_image()
    
    # Convert to base64
    _, buffer = cv2.imencode('.png', test_image)
    image_base64 = base64.b64encode(buffer).decode('utf-8')
    
    # Test prediction
    print("🔮 Testing prediction...")
    try:
        prediction_data = {
            'image': image_base64
        }
        
        response = requests.post(
            'http://127.0.0.1:9090/predict',
            json=prediction_data,
            timeout=30
        )
        
        if response.status_code == 200:
            result = response.json()
            
            if result.get('success'):
                print("✅ Prediction successful!")
                print(f"   Predicted text: {result.get('predicted_text', 'N/A')}")
                print(f"   Predicted digits: {result.get('predicted_digits', [])}")
                print(f"   Confidence scores: {[f'{c:.3f}' for c in result.get('confidence_scores', [])]}")
                print(f"   Prediction time: {result.get('prediction_time', 0):.3f}s")
                
                # Check shape consistency
                predicted_digits = result.get('predicted_digits', [])
                if len(predicted_digits) == 5:
                    print("✅ Correct number of digits (5)")
                else:
                    print(f"⚠️  Unexpected number of digits: {len(predicted_digits)}")
                
                return True
            else:
                print(f"❌ Prediction failed: {result.get('error', 'Unknown error')}")
                return False
        else:
            print(f"❌ API request failed: HTTP {response.status_code}")
            try:
                error_data = response.json()
                print(f"   Error: {error_data.get('error', 'Unknown error')}")
            except:
                print(f"   Response: {response.text[:200]}...")
            return False
            
    except requests.exceptions.RequestException as e:
        print(f"❌ Request failed: {e}")
        return False
    except Exception as e:
        print(f"❌ Unexpected error: {e}")
        return False

def test_shape_compatibility():
    """Test shape compatibility locally"""
    print("\n🔬 Testing shape compatibility locally...")
    
    try:
        # Import the functions
        from captcha_processor import process_captcha_image, extract_digit_rois_for_prediction
        import keras
        
        # Load model
        model = keras.models.load_model('persian_digit.keras')
        print(f"   Model input shape: {model.input_shape}")
        
        # Create test image
        test_image = create_test_image()
        
        # Process image
        binary_image, target_colors, confidence_info = process_captcha_image(
            test_image,
            color_tolerance=30,
            use_pattern_shadow_detection=True
        )
        
        # Extract ROIs
        digit_images_array = extract_digit_rois_for_prediction(binary_image)
        print(f"   Extracted shape: {digit_images_array.shape}")
        
        # Check compatibility
        expected_shape = model.input_shape[1:]  # Remove batch dimension
        actual_shape = digit_images_array.shape[1:]
        
        if actual_shape == expected_shape:
            print("✅ Shape compatibility: PASS")
            
            # Test prediction
            predictions = model.predict(digit_images_array, verbose=0)
            predicted_digits = np.argmax(predictions, axis=1)
            print(f"   Local prediction: {predicted_digits.tolist()}")
            return True
        else:
            print(f"❌ Shape mismatch: expected {expected_shape}, got {actual_shape}")
            return False
            
    except Exception as e:
        print(f"❌ Local test failed: {e}")
        import traceback
        traceback.print_exc()
        return False

if __name__ == '__main__':
    print("🚀 Captcha API Fix Verification")
    print("=" * 50)
    
    # Test shape compatibility first
    local_test_passed = test_shape_compatibility()
    
    # Test API
    api_test_passed = test_api()
    
    print("\n" + "=" * 50)
    print("📊 Test Results:")
    print(f"   Local shape test: {'✅ PASS' if local_test_passed else '❌ FAIL'}")
    print(f"   API test: {'✅ PASS' if api_test_passed else '❌ FAIL'}")
    
    if local_test_passed and api_test_passed:
        print("\n🎉 All tests passed! The captcha API fix is working correctly.")
    elif local_test_passed and not api_test_passed:
        print("\n⚠️  Local test passed but API test failed. Try restarting the API server.")
    else:
        print("\n❌ Tests failed. Check the error messages above for details.")
    
    print("\n💡 To restart the API server: ./restart-captcha-api.sh")