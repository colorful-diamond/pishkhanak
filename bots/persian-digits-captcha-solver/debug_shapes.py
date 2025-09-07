#!/usr/bin/env python3
"""
Debug script to check the shapes produced by the captcha processor
"""

import numpy as np
import cv2
from captcha_processor import process_captcha_image, extract_digit_rois_for_prediction

# Create a simple test image 
test_image = np.random.randint(0, 255, (40, 163, 3), dtype=np.uint8)

print("🔍 Debug: Captcha Processor Shape Analysis")
print("=" * 50)

print(f"1. Input image shape: {test_image.shape}")

# Process the image
try:
    binary_image, target_colors, confidence_info = process_captcha_image(
        test_image,
        color_tolerance=30,
        use_pattern_shadow_detection=True
    )
    
    print(f"2. Binary image shape: {binary_image.shape}")
    print(f"3. Target colors count: {len(target_colors) if target_colors else 0}")
    
    # Extract ROIs
    digit_images_array = extract_digit_rois_for_prediction(binary_image)
    
    print(f"4. Digit images array shape: {digit_images_array.shape}")
    print(f"5. Expected shape: (5, 40, 30, 1)")
    
    # Check individual shapes
    print("\nIndividual digit shapes:")
    for i in range(min(5, digit_images_array.shape[0])):
        print(f"   Digit {i+1}: {digit_images_array[i].shape}")
    
    # Test with custom target size
    print("\n" + "="*30)
    print("Testing with explicit target size (30, 40):")
    
    digit_images_array2 = extract_digit_rois_for_prediction(binary_image, target_size=(30, 40))
    print(f"Custom target size result: {digit_images_array2.shape}")
    
    # Check what cv2.resize actually produces
    print("\n" + "="*30)
    print("Testing cv2.resize directly:")
    
    test_roi = binary_image[6:38, 5:30]  # Roughly one digit region
    print(f"Original ROI shape: {test_roi.shape}")
    
    resized_30_40 = cv2.resize(test_roi, (30, 40))
    print(f"cv2.resize(roi, (30, 40)) produces shape: {resized_30_40.shape}")
    
    resized_40_30 = cv2.resize(test_roi, (40, 30))
    print(f"cv2.resize(roi, (40, 30)) produces shape: {resized_40_30.shape}")
    
except Exception as e:
    print(f"❌ Error during processing: {e}")
    import traceback
    traceback.print_exc()

print("\n✅ Debug completed")