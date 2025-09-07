#!/usr/bin/env python3
"""
Test script to demonstrate the new API visualization features
"""

import requests
import json
import base64
from PIL import Image
import io

def test_api_with_visualization():
    """Test the enhanced API endpoint with visualization features."""
    
    # API endpoint
    api_url = "http://127.0.0.1:9090/predict"
    
    # Load a test image
    test_image_path = "test/sample_captcha.png"  # Replace with actual test image
    
    try:
        # Read and encode image
        with open(test_image_path, "rb") as f:
            image_data = f.read()
            image_base64 = base64.b64encode(image_data).decode('utf-8')
        
        # Prepare request payload
        payload = {
            "image": image_base64
        }
        
        # Make API request
        print("🚀 Sending request to API...")
        response = requests.post(api_url, json=payload, timeout=30)
        
        if response.status_code == 200:
            result = response.json()
            
            if result['success']:
                print("✅ API Response successful!")
                print(f"📝 Predicted Text: {result['predicted_text']}")
                print(f"⏱️  Processing Time: {result['prediction_time']}s")
                print(f"🎯 Average Confidence: {result['processing_info']['average_confidence']:.3f}")
                
                # Show individual results
                print("\n📊 Individual Digit Results:")
                for i, individual in enumerate(result['individual_results']):
                    print(f"  Digit {individual['position']}: '{individual['predicted_digit']}' "
                          f"(confidence: {individual['confidence']:.3f})")
                    print(f"    ROI: {individual['roi_coordinates']}")
                    if individual['target_color']:
                        print(f"    Target Color: BGR{individual['target_color']}")
                
                # Show available images
                print("\n🖼️  Available Images:")
                if 'images' in result:
                    for image_type, image_b64 in result['images'].items():
                        if image_b64:
                            print(f"  ✅ {image_type}: {len(image_b64)} characters")
                            
                            # Optionally save images to files
                            if image_type == 'processing_steps':
                                # Save the comprehensive visualization
                                image_data = base64.b64decode(image_b64)
                                with open(f"result_{image_type}.png", "wb") as f:
                                    f.write(image_data)
                                print(f"    💾 Saved as: result_{image_type}.png")
                        else:
                            print(f"  ❌ {image_type}: Not available")
                
                # Show ROI positions
                print(f"\n📦 ROI Positions: {len(result['roi_positions'])} boxes")
                for i, roi in enumerate(result['roi_positions']):
                    print(f"  Box {i+1}: x={roi['x']}, y={roi['y']}, w={roi['width']}, h={roi['height']}")
                
                return True
                
            else:
                print(f"❌ API Error: {result.get('error', 'Unknown error')}")
                return False
        else:
            print(f"❌ HTTP Error: {response.status_code}")
            try:
                error_data = response.json()
                print(f"Error details: {error_data}")
            except:
                print(f"Response text: {response.text}")
            return False
            
    except FileNotFoundError:
        print(f"❌ Test image not found: {test_image_path}")
        print("Please place a test captcha image in the test/ directory")
        return False
    except requests.exceptions.ConnectionError:
        print("❌ Cannot connect to API server")
        print("Make sure the API server is running: python captcha_api_production.py")
        return False
    except Exception as e:
        print(f"❌ Unexpected error: {e}")
        return False

def display_api_capabilities():
    """Display the new API capabilities."""
    print("🎯 Enhanced Captcha API - New Visualization Features")
    print("=" * 55)
    print()
    print("📊 New Response Data:")
    print("  ✅ predicted_text - The predicted captcha text")
    print("  ✅ confidence_scores - Confidence for each digit")
    print("  ✅ individual_results - Detailed per-digit analysis")
    print("  ✅ target_colors - Detected colors for each box")
    print("  ✅ roi_positions - Exact ROI coordinates")
    print("  ✅ processing_info - Statistics and metadata")
    print()
    print("🖼️  New Image Outputs (Base64 encoded):")
    print("  ✅ original_image - Input image")
    print("  ✅ roi_visualization - Image with ROI boxes and predictions")
    print("  ✅ processing_steps - Comprehensive step-by-step visualization")
    print()
    print("📋 Individual Results Include:")
    print("  • Position and ROI coordinates")
    print("  • Target color for each box")
    print("  • Predicted digit and confidence")
    print("  • Individual digit ROI image (base64)")
    print("  • Error details if processing failed")
    print()
    print("🚀 To test: python test_api_visualization.py")
    print()

if __name__ == "__main__":
    display_api_capabilities()
    
    # Uncomment to run the actual test
    # test_api_with_visualization()