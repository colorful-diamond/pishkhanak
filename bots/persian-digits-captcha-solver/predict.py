"""
6-STEP DYNAMIC ORDERED COLOR-BASED CAPTCHA PREDICTION SYSTEM WITH SEQUENTIAL CHAINING

🔗 SEQUENTIAL CHAINING BEHAVIOR:
Steps are executed sequentially where each step receives the OUTPUT of the previous step as INPUT.

Examples:
- STEP_ORDER = [4, 3]:  Do Step 4 first → pass result to Step 3 → final output
- STEP_ORDER = [5, 1, 3]: Do Step 5 first → pass result to Step 1 → pass result to Step 3 → final output
- STEP_ORDER = [1, 2, 3, 4]: Step 1 → Step 2 → Step 3 → Step 4 (classic pipeline)

🔧 DYNAMIC STEP ORDERING:
Control the ORDER of preprocessing steps by modifying STEP_ORDER around line 1054:

STEP_ORDER = [1, 2, 3, 4, 5]   # Normal pipeline: isolated → outside → color → neighbor → line detection
STEP_ORDER = [5, 1, 2, 3, 4]   # Line detection FIRST: line → isolated → outside → color → neighbor
STEP_ORDER = [3, 4]            # Only color analysis → neighbor replacement
STEP_ORDER = [5, 3, 4]         # Line removal → color analysis → neighbor replacement
STEP_ORDER = [1, 5, 3]         # Denoising → line removal → color analysis

⚙️ AUTOMATIC DATA TYPE CONVERSION:
The system automatically handles conversions between BGR images and binary images:
- Steps 1, 2, 5: Work on BGR images (3-channel color)
- Steps 3: Takes BGR input, produces binary output + target colors
- Steps 4, 6: Work on binary images (1-channel) and require target colors from Step 3

🔧 SIMPLE STEP CONTROL:
Steps are controlled ONLY by STEP_ORDER - if a step is not in STEP_ORDER, it won't run.
No separate enable/disable flags needed!

🎯 THE 6 SEQUENTIAL STEPS:
Step 1: Isolated pixel removal (BGR → BGR) - Remove noise pixels
Step 2: Outside-box pixel removal (BGR → BGR) - Focus on digit areas  
Step 3: Color analysis (BGR → Binary + target_colors) - Find digit colors, create binary
Step 4: Neighbor replacement (Binary → Binary) - Fill gaps using 4-connectivity [needs target_colors]
Step 5: Line detection (BGR → BGR) - Remove interfering lines
Step 6: White pixel filling (Binary → Binary) - Fill isolated white pixels [needs target_colors]

💡 SMART DEPENDENCY HANDLING:
- Steps 4 and 6 need target_colors from Step 3
- If target_colors not available, emergency conversion is applied
- Automatic BGR ↔ Binary conversion ensures compatibility
- Each step gets the appropriate input format

Steps are controlled ONLY by STEP_ORDER - clean and simple!
"""

import cv2
import keras
import random
import numpy as np
import os
from os import listdir, path
import tensorflow as tf
import matplotlib
matplotlib.use('Agg')  # Use Agg backend for WSL2 compatibility
import matplotlib.pyplot as plt
from collections import Counter

# --- CPU Configuration for Inference ---
print("Configuring CPU for inference...")
tf.config.set_visible_devices([], 'GPU')
print("GPU devices disabled. Inference will use CPU.")

# --- Settings ---
MODEL_PATH = './persian_digit.keras'
CAPTCHA_DIR = 'test/'
IMG_WIDTH_MODEL = 30
IMG_HEIGHT_MODEL = 40
NUM_DIGITS_EXPECTED = 5
PERSIAN_DIGITS = "۰۱۲۳۴۵۶۷۸۹"

# --- Load Model ---
print(f"Loading model from {MODEL_PATH}...")
try:
    model = keras.models.load_model(MODEL_PATH)
    print("Model loaded successfully.")
except Exception as e:
    print(f"Error loading model: {e}")
    exit()

# --- Load and Preprocess Captcha Image ---
captcha_files = [f for f in listdir(CAPTCHA_DIR) if f.lower().endswith(('.png', '.jpg', '.jpeg'))]

if not captcha_files:
    print(f"No valid images found in '{CAPTCHA_DIR}' directory.")
    exit()

selected_file = random.choice(captcha_files)
CAPTCHA_IMAGE_PATH = path.join(CAPTCHA_DIR, selected_file)
print(f"Selected test image: {CAPTCHA_IMAGE_PATH}")

# Read image with OpenCV
image_bgr = cv2.imread(CAPTCHA_IMAGE_PATH)
if image_bgr is None:
    print(f"Error: Unable to read image from path '{CAPTCHA_IMAGE_PATH}'")
    exit()

def extract_boxes_rgb_values(image_bgr):
    """
    Extract RGB values from each of the 5 digit boxes individually
    Returns box-by-box color analysis data
    """
    height, width = image_bgr.shape[:2]
    
    # Fixed ROI positions for 5 Persian digits (same as in original code)
    roi_positions = [
        (5, 4, 25, 32),    # Digit 1
        (37, 4, 25, 32),   # Digit 2
        (69, 4, 25, 32),   # Digit 3
        (101, 4, 25, 32),  # Digit 4
        (133, 4, 25, 32),  # Digit 5
    ]
    
    box_images = []
    box_colors = []  # List of color lists for each box
    
    for i, (x, y, w, h) in enumerate(roi_positions):
        # Extract ROI with bounds checking
        x1, y1 = max(0, x), max(0, y)
        x2, y2 = min(width, x + w), min(height, y + h)
        
        if x2 > x1 and y2 > y1:
            roi = image_bgr[y1:y2, x1:x2]
            box_images.append(roi)
            
            # Get all colors in this specific box
            roi_reshaped = roi.reshape(-1, 3)
            box_color_list = [tuple(color) for color in roi_reshaped]
            box_colors.append(box_color_list)
        else:
            box_images.append(np.zeros((h, w, 3), dtype=np.uint8))
            box_colors.append([])
    
    return box_colors, box_images, roi_positions

def find_most_repeated_color_in_box(box_colors, exclude_background=True):
    """
    Find the most repeated color in a single box
    Optionally exclude very light colors (background)
    """
    if not box_colors:
        return None, 0
        
    color_counts = Counter(box_colors)
    
    if exclude_background:
        # Filter out very light colors (likely background)
        # Remove colors where all RGB values are > 200 (very light/white)
        filtered_counts = {}
        for color, count in color_counts.items():
            r, g, b = color
            if not (r > 200 and g > 200 and b > 200):  # Not very light
                filtered_counts[color] = count
        
        if filtered_counts:
            # Convert back to Counter object
            color_counts = Counter(filtered_counts)
    
    if not color_counts:
        # If no colors after filtering, return the most common original color
        color_counts = Counter(box_colors)
    
    # Get the most common color
    most_common_color = color_counts.most_common(1)[0]
    return most_common_color[0], most_common_color[1]

def is_warm_shadow(pixel_bgr, target_color_bgr, tolerance=50):
    """
    Detect if a pixel is a warm shadow (left side pattern) based on the discovered lighting pattern.
    Warm shadows have increased red/orange tones relative to the main color.
    """
    pixel_rgb = [pixel_bgr[2], pixel_bgr[1], pixel_bgr[0]]  # Convert BGR to RGB for easier analysis
    target_rgb = [target_color_bgr[2], target_color_bgr[1], target_color_bgr[0]]
    
    # Warm shadow characteristics (based on examples: BC512C, D37B2C, FFC183, E9A05C, 89192C)
    # - Generally lighter than target color
    # - Red/orange shift (higher R, often higher G, lower B relative to the shift)
    
    # Check if pixel is brighter (typical for shadows)
    pixel_brightness = sum(pixel_rgb) / 3
    target_brightness = sum(target_rgb) / 3
    
    # Check for warm tone shift (red/orange increase)
    red_increase = pixel_rgb[0] - target_rgb[0]
    green_change = pixel_rgb[1] - target_rgb[1] 
    blue_change = pixel_rgb[2] - target_rgb[2]
    
    # Warm shadow criteria:
    # 1. Generally brighter than target
    # 2. Red channel increases more than blue
    # 3. Color distance within tolerance
    color_distance = np.sqrt(sum((np.array(pixel_rgb) - np.array(target_rgb))**2))
    
    is_brighter = pixel_brightness > target_brightness - 10  # Allow slight darker
    has_warm_shift = red_increase > blue_change  # Red increases more than blue
    within_tolerance = color_distance <= tolerance
    
    return is_brighter and has_warm_shift and within_tolerance

def is_cool_shadow(pixel_bgr, target_color_bgr, tolerance=50):
    """
    Detect if a pixel is a cool shadow (right side pattern) based on the discovered lighting pattern.
    Cool shadows have increased blue/purple tones relative to the main color.
    """
    pixel_rgb = [pixel_bgr[2], pixel_bgr[1], pixel_bgr[0]]  # Convert BGR to RGB for easier analysis
    target_rgb = [target_color_bgr[2], target_color_bgr[1], target_color_bgr[0]]
    
    # Cool shadow characteristics (based on examples: 6C51A5, A3C1FF, 89A0E2, 6C7BC4, D3FFFF, A3A0E2)
    # - Often lighter than target color
    # - Blue/purple shift (higher B, often higher G, lower R relative to shift)
    
    # Check if pixel is brighter or has blue shift (typical for cool shadows)
    pixel_brightness = sum(pixel_rgb) / 3
    target_brightness = sum(target_rgb) / 3
    
    # Check for cool tone shift (blue/purple increase)
    red_change = pixel_rgb[0] - target_rgb[0]
    green_change = pixel_rgb[1] - target_rgb[1]
    blue_increase = pixel_rgb[2] - target_rgb[2]
    
    # Cool shadow criteria:
    # 1. Generally brighter or has significant blue shift
    # 2. Blue channel increases more than red
    # 3. Color distance within tolerance
    color_distance = np.sqrt(sum((np.array(pixel_rgb) - np.array(target_rgb))**2))
    
    is_brighter_or_blue_shift = pixel_brightness > target_brightness - 15 or blue_increase > 20
    has_cool_shift = blue_increase > red_change  # Blue increases more than red
    within_tolerance = color_distance <= tolerance
    
    return is_brighter_or_blue_shift and has_cool_shift and within_tolerance

def create_color_based_binary_image_for_box(box_image, target_color, tolerance=20, preserve_digit_continuity=True, use_pattern_shadow_detection=True):
    """
    Create a 3-level binary image for a single box - DEBUGGING AND FIXING VERSION:
    
    ISSUE: When lines cross digits, neighbor pixels should be preserved to maintain digit continuity
    SOLUTION: Shadows kept as gray to preserve line structure
    
    NEW 3-LEVEL BEHAVIOR:
    - Keep exact target color pixels as BLACK (0) - digit pixels
    - Keep neighbor pixels as BLACK (0) - to preserve digit continuity when lines cross
    - Keep shadows as GRAY (128) - detected warm/cool shadows preserve line structure
    - Keep other pixels as WHITE (255) - background
    
    Parameters:
    - tolerance: Strictness for exact target color matching
    - preserve_digit_continuity: Enable neighbor preservation for digit continuity
    - use_pattern_shadow_detection: Use intelligent shadow detection
    
    Returns:
    - 3-level grayscale image: 0=target+neighbors, 128=shadows, 255=background
    """
    if target_color is None:
        # If no target color found, return a white image
        return np.ones_like(box_image[:,:,0]) * 255
    
    # Convert to numpy array for easier processing
    target_color = np.array(target_color)
    height, width = box_image.shape[:2]
    
    # Create a mask for pixels similar to target color (ONLY exact matches)
    target_mask = np.all(np.abs(box_image.astype(int) - target_color.astype(int)) <= tolerance, axis=2)
    
    print(f"       🐛 DEBUG MODE: Analyzing pixel types and neighbor relationships (tolerance={tolerance})")
    print(f"       🚨🚨🚨 STEP 3 FUNCTION IS RUNNING - BOX IMAGE SHAPE: {box_image.shape} 🚨🚨🚨")
    print(f"       🚨🚨🚨 TARGET COLOR: {target_color} 🚨🚨🚨")
    
    # Initialize enhanced mask with target pixels
    enhanced_mask = target_mask.copy()
    
    # Create separate masks for shadows
    warm_shadow_mask = np.zeros_like(target_mask, dtype=bool)
    cool_shadow_mask = np.zeros_like(target_mask, dtype=bool)
    
    # Count different pixel types for debugging
    target_pixels = np.sum(target_mask)
    warm_shadows_detected = 0
    cool_shadows_detected = 0
    neighbor_pixels_preserved = 0
    other_pixels = 0
    
    # Define neighbor offsets (4-connected: up, down, left, right)
    neighbor_offsets = [(-1, 0), (1, 0), (0, -1), (0, 1)]
    
    # Analyze each non-target pixel
    for y in range(height):
        for x in range(width):
            if not target_mask[y, x]:  # If pixel is NOT exact target color
                pixel_bgr = box_image[y, x]
                
                # First check if this pixel neighbors target pixels
                has_target_neighbor = False
                neighbor_positions = []
                
                for dy, dx in neighbor_offsets:
                    ny, nx = y + dy, x + dx
                    # Check bounds and if neighbor is target color
                    if (0 <= ny < height and 0 <= nx < width and target_mask[ny, nx]):
                        has_target_neighbor = True
                        neighbor_positions.append((nx, ny))
                
                if has_target_neighbor:
                    # This pixel neighbors a digit - now check what type it is
                    is_warm_shadow_pixel = is_warm_shadow(pixel_bgr, target_color)
                    is_cool_shadow_pixel = is_cool_shadow(pixel_bgr, target_color)
                    
                    if is_warm_shadow_pixel:
                        # Neighbor + warm shadow = make gray
                        warm_shadows_detected += 1
                        warm_shadow_mask[y, x] = True
                        print(f"         🌅 Warm shadow NEIGHBOR at ({x},{y}): BGR{tuple(pixel_bgr)} - GRAY (neighbors: {neighbor_positions})")
                    elif is_cool_shadow_pixel:
                        # Neighbor + cool shadow = make gray
                        cool_shadows_detected += 1
                        cool_shadow_mask[y, x] = True
                        print(f"         🌌 Cool shadow NEIGHBOR at ({x},{y}): BGR{tuple(pixel_bgr)} - GRAY (neighbors: {neighbor_positions})")
                    else:
                        # Neighbor but not shadow = make black for continuity
                        enhanced_mask[y, x] = True
                        neighbor_pixels_preserved += 1
                        print(f"         🔗 NEIGHBOR PRESERVED at ({x},{y}): BGR{tuple(pixel_bgr)} - BLACK! (neighbors: {neighbor_positions})")
                else:
                    # Not a neighbor to any target pixel = make white (ignore shadow patterns)
                    other_pixels += 1
                    print(f"         ⚪ Other pixel at ({x},{y}): BGR{tuple(pixel_bgr)} - WHITE (no target neighbors)")
    
    print(f"       📊 DEBUGGING RESULTS:")
    print(f"         ✅ Target pixels (black): {target_pixels}")
    print(f"         🔗 Neighbor pixels preserved (black): {neighbor_pixels_preserved}")
    print(f"         🌅 Warm shadows detected (GRAY): {warm_shadows_detected}")
    print(f"         🌌 Cool shadows detected (GRAY): {cool_shadows_detected}")
    print(f"         ⚪ Other pixels (white): {other_pixels}")
    print(f"         🎯 Total black pixels: {target_pixels + neighbor_pixels_preserved}")
    print(f"         🔘 Total gray pixels: {warm_shadows_detected + cool_shadows_detected}")
    
    # Use enhanced mask that includes both target pixels and neighbors
    final_mask = enhanced_mask
    
    # Create 3-level binary image: black (0), gray (128), white (255)
    binary_image = np.ones_like(box_image[:,:,0]) * 255  # Start with white (255)
    
    # Apply masks in order: shadows first, then target+neighbors (to prioritize digits)
    combined_shadow_mask = warm_shadow_mask | cool_shadow_mask
    binary_image[combined_shadow_mask] = 128  # Make shadows gray (128)
    binary_image[final_mask] = 0  # Make target pixels AND neighbors black (0) - this overrides shadows
    
    total_black_pixels = np.sum(binary_image == 0)
    total_gray_pixels = np.sum(binary_image == 128)
    total_white_pixels = np.sum(binary_image == 255)
    
    print(f"       🎯 FINAL 3-LEVEL BINARY RESULT:")
    print(f"         🔵 Total BLACK pixels (target+neighbors): {total_black_pixels}")
    print(f"         🔘 Total GRAY pixels (shadows): {total_gray_pixels}")
    print(f"         ⚪ Total WHITE pixels (other): {total_white_pixels}")
    print(f"       🚨🚨🚨 STEP 3 FUNCTION COMPLETED 🚨🚨🚨")
    
    return binary_image

def remove_isolated_pixels(image_bgr):
    """
    Remove pixels that are surrounded by white on 3+ sides (noise removal)
    This is your proven technique for cleaning up isolated artifacts.
    """
    print("   🧹 Removing pixels surrounded by white on 3+ sides...")
    
    # Convert to grayscale for processing
    gray = cv2.cvtColor(image_bgr, cv2.COLOR_BGR2GRAY)
    cleaned_result = gray.copy()
    height, width = gray.shape
    
    pixels_removed = 0
    
    # Check each pixel (skip borders to avoid index errors)
    for y in range(1, height-1):
        for x in range(1, width-1):
            current_pixel = gray[y, x]
            
            # Check 8-connected neighbors
            neighbors = [
                gray[y-1, x-1], gray[y-1, x], gray[y-1, x+1],  # Top row
                gray[y, x-1],                  gray[y, x+1],    # Middle row (skip center)
                gray[y+1, x-1], gray[y+1, x], gray[y+1, x+1]   # Bottom row
            ]
            
            # Count white neighbors (background pixels with value > 200)
            white_neighbors = sum(1 for n in neighbors if n > 200)
            
            # If surrounded by white on 3+ sides (out of 8), make it white (remove it)
            if white_neighbors >= 3:
                cleaned_result[y, x] = 255  # Make it white
                pixels_removed += 1
    
    print(f"       ✨ Removed {pixels_removed} isolated pixels")
    
    # Convert back to BGR for compatibility
    cleaned_bgr = cv2.cvtColor(cleaned_result, cv2.COLOR_GRAY2BGR)
    return cleaned_bgr

def remove_pixels_outside_boxes(image_bgr, roi_positions):
    """
    Remove all pixels outside the 5 digit boxes by setting them to white.
    This eliminates background noise and focuses only on box contents.
    """
    height, width = image_bgr.shape[:2]
    
    # Create a mask for the 5 boxes
    mask = np.zeros((height, width), dtype=np.uint8)
    
    for x, y, w, h in roi_positions:
        # Set box areas to 1 in the mask
        x1, y1 = max(0, x), max(0, y)
        x2, y2 = min(width, x + w), min(height, y + h)
        if x2 > x1 and y2 > y1:
            mask[y1:y2, x1:x2] = 1
    
    # Create cleaned image: keep box contents, set everything else to white
    cleaned_image = image_bgr.copy()
    
    # Set pixels outside boxes to white (255, 255, 255)
    for c in range(3):  # BGR channels
        cleaned_image[:, :, c] = np.where(mask == 1, image_bgr[:, :, c], 255)
    
    return cleaned_image

def preprocessing_step1_isolated_pixels(image_bgr, enabled=True):
    """
    Step 1: Remove isolated pixels surrounded by white (3+ sides)
    Your proven noise removal technique
    """
    if not enabled:
        print("   ⏭️  Step 1: Isolated pixel removal - DISABLED")
        return image_bgr
    
    print("   🚫 Step 1: Removing isolated pixels surrounded by white (3+ sides)...")
    return remove_isolated_pixels(image_bgr)

def preprocessing_step2_outside_boxes(image_bgr, enabled=True):
    """
    Step 2: Remove pixels outside the 5 digit boxes
    Focus only on box contents
    """
    # Define ROI positions
    roi_positions = [
        (5, 6, 25, 32),    # Digit 1
        (37, 6, 25, 32),   # Digit 2
        (69, 6, 25, 32),   # Digit 3
        (101, 6, 25, 32),  # Digit 4
        (133, 6, 25, 32),  # Digit 5
    ]
    
    if not enabled:
        print("   ⏭️  Step 2: Outside-box removal - DISABLED")
        return image_bgr, roi_positions
    
    print("   🧹 Step 2: Removing pixels outside digit boxes...")
    cleaned_image = remove_pixels_outside_boxes(image_bgr, roi_positions)
    return cleaned_image, roi_positions

def preprocessing_step3_color_analysis(image_bgr, roi_positions, tolerance=25, use_pattern_shadow_detection=True, enabled=True):
    """
    Step 3: Box-by-box color analysis with digit continuity preservation and intelligent shadow detection
    - Finds target digit color in each box
    - Keeps all target color pixels (the actual digit)
    - Uses discovered lighting pattern to detect warm/cool shadows and preserve them as white
    - Converts only line pixels (different from digit/shadow patterns) that neighbor digit pixels to digit color
    - This prevents white gaps when lines cross through digits while preserving natural shadows
    
    Parameters:
    - tolerance: Strictness for exact digit color matching (default: 25)
    - use_pattern_shadow_detection: Use intelligent pattern-based shadow detection (default: True)
    """
    if not enabled:
        print("   ⏭️  Step 3: Color analysis - DISABLED")
        # Return simple binary conversion as fallback
        gray = cv2.cvtColor(image_bgr, cv2.COLOR_BGR2GRAY)
        _, simple_binary = cv2.threshold(gray, 127, 255, cv2.THRESH_BINARY)
        
        # Create dummy data for compatibility
        target_colors = [None] * 5
        box_images = []
        for x, y, w, h in roi_positions:
            x1, y1 = max(0, x), max(0, y)
            x2, y2 = min(image_bgr.shape[1], x + w), min(image_bgr.shape[0], y + h)
            if x2 > x1 and y2 > y1:
                box_images.append(image_bgr[y1:y2, x1:x2])
            else:
                box_images.append(np.zeros((h, w, 3), dtype=np.uint8))
        
        return simple_binary, target_colors, box_images
    
    print(f"   🎨 Step 3: Color analysis with digit continuity and intelligent shadow detection (tolerance={tolerance}, pattern_shadows={use_pattern_shadow_detection})...")
    
    # Extract colors from boxes individually
    box_colors, box_images, _ = extract_boxes_rgb_values(image_bgr)
    print(f"       📦 Extracted colors from {len(box_images)} boxes individually")
    
    # Process each box individually - NO neighbor replacement here
    target_colors = []
    box_binary_images = []
    
    for i, (box_color_list, box_image) in enumerate(zip(box_colors, box_images)):
        if len(box_color_list) > 0:
            # Find most repeated color in this specific box
            target_color, count = find_most_repeated_color_in_box(box_color_list)
            target_colors.append(target_color)
            
            # Create binary image for this box WITH digit continuity preservation
            box_binary = create_color_based_binary_image_for_box(box_image, target_color, tolerance, preserve_digit_continuity=True, use_pattern_shadow_detection=use_pattern_shadow_detection)
            box_binary_images.append(box_binary)
            
            print(f"       📦 Box {i+1}: Target color BGR{target_color} (appeared {count} times)")
        else:
            target_colors.append(None)
            box_binary_images.append(np.ones((26, 25), dtype=np.uint8) * 255)  # white box
            print(f"       📦 Box {i+1}: Empty box, skipping")
    
    # Create combined binary image by placing processed boxes back in original positions
    height, width = image_bgr.shape[:2]
    combined_binary = np.ones((height, width), dtype=np.uint8) * 255  # Start with white
    
    for i, (box_binary, (x, y, w, h)) in enumerate(zip(box_binary_images, roi_positions)):
        if box_binary.size > 0:
            # Place the processed box back in the combined image
            x1, y1 = max(0, x), max(0, y)
            x2, y2 = min(width, x + w), min(height, y + h)
            
            if x2 > x1 and y2 > y1:
                # Resize box_binary to match the exact ROI size
                roi_h, roi_w = y2 - y1, x2 - x1
                box_binary_resized = cv2.resize(box_binary, (roi_w, roi_h))
                combined_binary[y1:y2, x1:x2] = box_binary_resized
    
    print(f"       ✨ Created combined binary image (without neighbor replacement)")
    print(f"       🎯 Box target colors: {target_colors}")
    
    return combined_binary, target_colors, box_images

def preprocessing_step4_neighbor_replacement(binary_image, roi_positions, target_colors, enabled=True):
    """
    Step 4: Intelligent neighbor replacement (4-connected) - DISABLED to prevent neighbor conversion
    Apply neighbor-based color replacement to improve digit continuity
    """
    if not enabled:
        print("   ⏭️  Step 4: Neighbor replacement - DISABLED (no neighbor conversion)")
        return binary_image
    
    # Note: This step is controlled by STEP_ORDER only
    
    print("   🔄 Step 4: Applying neighbor replacement (4-connected)...")
    
    # Work on a copy
    enhanced_binary = binary_image.copy()
    
    # Apply neighbor replacement to each box region
    for i, ((x, y, w, h), target_color) in enumerate(zip(roi_positions, target_colors)):
        if target_color is None:
            continue
            
        # Extract the box region from binary image
        x1, y1 = max(0, x), max(0, y)
        x2, y2 = min(binary_image.shape[1], x + w), min(binary_image.shape[0], y + h)
        
        if x2 > x1 and y2 > y1:
            box_binary = enhanced_binary[y1:y2, x1:x2].copy()
            height, width = box_binary.shape
            
            # Apply neighbor replacement logic
            pixels_added = 0
            
            # Create mask for current target pixels (black pixels = 0)
            target_mask = (box_binary == 0)
            enhanced_mask = target_mask.copy()
            
            # Define 4-connected neighbor offsets
            neighbor_offsets = [(-1, 0), (1, 0), (0, -1), (0, 1)]  # up, down, left, right
            
            # Check each pixel
            for by in range(1, height-1):
                for bx in range(1, width-1):
                    if not target_mask[by, bx]:  # If pixel is NOT target color (white = 255)
                        # Check if any neighbors are target color (black = 0)
                        has_target_neighbor = False
                        
                        for dy, dx in neighbor_offsets:
                            ny, nx = by + dy, bx + dx
                            # Check bounds and if neighbor is target color
                            if (0 <= ny < height and 0 <= nx < width and target_mask[ny, nx]):
                                has_target_neighbor = True
                                break
                        
                        # If has target neighbor, convert this pixel to target color
                        if has_target_neighbor:
                            enhanced_mask[by, bx] = True
                            pixels_added += 1
            
            # Apply the enhanced mask back to the box region
            box_binary_enhanced = np.ones_like(box_binary) * 255  # Start with white
            box_binary_enhanced[enhanced_mask] = 0  # Make target pixels black
            
            # Place the enhanced box back
            enhanced_binary[y1:y2, x1:x2] = box_binary_enhanced
            
            print(f"       📦 Box {i+1}: Added {pixels_added} pixels through neighbor replacement")
    
    print("       ✨ Neighbor replacement completed")
    return enhanced_binary

def preprocessing_step5_line_detection(image_bgr, roi_positions, min_line_length=10, min_connectivity=3, enabled=True):
    """
    Step 5: Enhanced thin line detection and removal (optimized for 1-pixel width lines)
    Detects thin lines by analyzing connected pixels outside digit boxes and removes them entirely
    """
    if not enabled:
        print("   ⏭️  Step 5: Line detection - DISABLED")
        return image_bgr
    
    print("   📏 Step 5: Detecting and removing thin lines (1-pixel width optimized)...")
    
    # Work on a copy
    cleaned_image = image_bgr.copy()
    height, width = image_bgr.shape[:2]
    
    # Create mask for areas OUTSIDE the boxes
    outside_mask = np.ones((height, width), dtype=np.uint8)
    
    for x, y, w, h in roi_positions:
        # Set box areas to 0 in the outside mask
        x1, y1 = max(0, x), max(0, y)
        x2, y2 = min(width, x + w), min(height, y + h)
        if x2 > x1 and y2 > y1:
            outside_mask[y1:y2, x1:x2] = 0
    
    # Find all unique colors outside the boxes
    outside_colors = {}
    colors_to_remove = []
    
    for y in range(height):
        for x in range(width):
            if outside_mask[y, x] == 1:  # Pixel is outside boxes
                color = tuple(image_bgr[y, x])
                if color not in outside_colors:
                    outside_colors[color] = []
                outside_colors[color].append((x, y))
    
    print(f"       🔍 Found {len(outside_colors)} unique colors outside boxes")
    
    # Analyze each color to determine if it forms thin lines
    for color, pixel_positions in outside_colors.items():
        if len(pixel_positions) < min_connectivity:
            continue
            
        # Skip very light colors (likely background)
        r, g, b = color
        if r > 240 and g > 240 and b > 240:
            continue
        
        # Create a binary image for this color
        color_mask = np.zeros((height, width), dtype=np.uint8)
        for px, py in pixel_positions:
            color_mask[py, px] = 255
        
        # Find connected components
        num_labels, labels, stats, centroids = cv2.connectedComponentsWithStats(color_mask, connectivity=8)
        
        is_line_color = False
        line_components = []
        
        # Analyze each connected component for thin line characteristics
        for i in range(1, num_labels):  # Skip background (0)
            area = stats[i, cv2.CC_STAT_AREA]
            width_comp = stats[i, cv2.CC_STAT_WIDTH]
            height_comp = stats[i, cv2.CC_STAT_HEIGHT]
            
            # Enhanced detection for 1-pixel width lines
            if area >= min_connectivity:
                max_dimension = max(width_comp, height_comp)
                min_dimension = min(width_comp, height_comp)
                
                # Calculate multiple line indicators
                aspect_ratio = max_dimension / max(min_dimension, 1)
                density = area / (width_comp * height_comp) if (width_comp * height_comp) > 0 else 0
                
                # Enhanced criteria for thin lines (1-pixel width)
                is_thin_line = False
                
                # Criterion 1: High aspect ratio (elongated)
                if aspect_ratio >= 2.5 and max_dimension >= min_line_length:
                    is_thin_line = True
                    line_type = "High Aspect"
                
                # Criterion 2: Very thin (1-2 pixel width) with reasonable length
                elif min_dimension <= 2 and max_dimension >= min_line_length:
                    is_thin_line = True
                    line_type = "Thin Width"
                
                # Criterion 3: High density (compact, line-like structure)
                elif density > 0.3 and max_dimension >= min_line_length and aspect_ratio >= 2.0:
                    is_thin_line = True
                    line_type = "High Density"
                
                # Criterion 4: Linear arrangement check (for 1-pixel lines)
                elif min_dimension == 1 and max_dimension >= min_line_length:
                    is_thin_line = True
                    line_type = "1-Pixel Line"
                
                if is_thin_line:
                    is_line_color = True
                    line_components.append((max_dimension, aspect_ratio, density, line_type))
                    print(f"       📏 Detected {line_type}: Color {color}, Length={max_dimension}, Aspect={aspect_ratio:.1f}, Density={density:.2f}")
        
        if is_line_color:
            colors_to_remove.append(color)
            # Show summary of line components for this color
            total_length = sum(comp[0] for comp in line_components)
            print(f"       🎯 Color {color}: {len(line_components)} line components, total length={total_length}")
    
    # Remove all pixels of detected line colors from the entire image
    pixels_removed = 0
    for color in colors_to_remove:
        color_pixels = 0
        for y in range(height):
            for x in range(width):
                if tuple(cleaned_image[y, x]) == color:
                    cleaned_image[y, x] = [255, 255, 255]  # Make white
                    pixels_removed += 1
                    color_pixels += 1
        print(f"       🗑️ Removed color {color}: {color_pixels} pixels")
    
    print(f"       ✨ Removed {len(colors_to_remove)} line colors, {pixels_removed} pixels total")
    
    return cleaned_image

def preprocessing_step6_white_pixel_filling(image, roi_positions, target_colors, min_surrounding_sides=3, enabled=True):
    """
    Step 6: White pixel filling (fill white pixels surrounded by target color on 3+ sides)
    Fills white pixels that are surrounded by the target color to connect broken digit parts
    """
    if not enabled:
        print("   ⏭️  Step 6: White pixel filling - DISABLED")
        return image
    
    print(f"   🔳 Step 6: Filling white pixels surrounded by target color ({min_surrounding_sides}+ sides)...")
    
    # Work on a copy
    filled_image = image.copy()
    height, width = image.shape[:2]
    
    # 4-connected neighbors (up, down, left, right)
    neighbors = [(-1, 0), (1, 0), (0, -1), (0, 1)]
    
    total_filled = 0
    
    # Process each box individually
    for box_idx, (x, y, w, h) in enumerate(roi_positions):
        if box_idx >= len(target_colors):
            continue
            
        target_color = target_colors[box_idx]
        if target_color is None:
            continue
        
        # Get box boundaries
        x1, y1 = max(0, x), max(0, y)
        x2, y2 = min(width, x + w), min(height, y + h)
        
        if x2 <= x1 or y2 <= y1:
            continue
        
        box_filled = 0
        
        # Check each pixel in the box
        for py in range(y1, y2):
            for px in range(x1, x2):
                # Only process white pixels
                current_pixel = filled_image[py, px]
                
                # Check if pixel is white (binary image: [255, 255, 255] or grayscale: 255)
                is_white = False
                if len(current_pixel.shape) == 0:  # Grayscale
                    is_white = current_pixel == 255
                else:  # BGR
                    is_white = np.all(current_pixel >= 250)  # Almost white
                
                if not is_white:
                    continue
                
                # Count neighbors with target color
                target_neighbors = 0
                valid_neighbors = 0
                
                for dy, dx in neighbors:
                    ny, nx = py + dy, px + dx
                    
                    # Check if neighbor is within image bounds
                    if 0 <= ny < height and 0 <= nx < width:
                        valid_neighbors += 1
                        neighbor_pixel = filled_image[ny, nx]
                        
                        # Check if neighbor has target color
                        neighbor_matches = False
                        if len(neighbor_pixel.shape) == 0:  # Grayscale
                            # For binary images, target color should be black (0)
                            neighbor_matches = neighbor_pixel == 0
                        else:  # BGR
                            # Check if neighbor matches target color within tolerance
                            color_diff = np.sqrt(np.sum((neighbor_pixel.astype(int) - np.array(target_color).astype(int))**2))
                            neighbor_matches = color_diff <= 30  # Tolerance for color matching
                        
                        if neighbor_matches:
                            target_neighbors += 1
                
                # Fill white pixel if surrounded by target color on enough sides
                if target_neighbors >= min_surrounding_sides and valid_neighbors >= min_surrounding_sides:
                    # Fill with target color or black for binary images
                    if len(filled_image[py, px].shape) == 0:  # Grayscale
                        filled_image[py, px] = 0  # Black for binary
                    else:  # BGR
                        filled_image[py, px] = target_color
                    
                    box_filled += 1
                    total_filled += 1
        
        if box_filled > 0:
            print(f"       📦 Box {box_idx+1}: Filled {box_filled} white pixels")
    
    print(f"       ✨ Total filled pixels: {total_filled}")
    return filled_image

def color_based_preprocessing_ordered(image_bgr, 
                                   step_order=[1, 2, 3, 4, 5, 6],
                                   tolerance=25,
                                   use_pattern_shadow_detection=True,
                                   min_line_length=10,
                                   min_connectivity=3,
                                   min_surrounding_sides=3):
    """
    Enhanced modular preprocessing with configurable step ORDER (SIMPLIFIED):
    
    Parameters:
    - step_order: List defining the order of steps [1,2,3,4,5,6] = normal, [5,1,2,3,4,6] = line detection first, etc.
                  ONLY steps in this list will execute - no separate enable/disable flags needed!
    - tolerance: Color matching tolerance for exact digit color
    - use_pattern_shadow_detection: Use intelligent pattern-based shadow detection (warm/cool shadows)
    - min_line_length: Minimum length for line detection (optimized for thin lines)
    - min_connectivity: Minimum connectivity for line detection (optimized for thin lines)
    - min_surrounding_sides: Minimum sides for white pixel filling (3 or 4)
    """
    print("🎨 Starting ordered modular color-based preprocessing...")
    print(f"   🔧 Step Order: {step_order}")
    print(f"   🔧 Advanced: tolerance={tolerance}, pattern_shadows={use_pattern_shadow_detection}, line_length={min_line_length}, connectivity={min_connectivity}, surrounding_sides={min_surrounding_sides}")
    
    # Initialize processing chain
    current_image = image_bgr
    current_binary = None
    
    # Initialize variables for tracking
    denoised_image = image_bgr  # Will be updated if step 1 runs
    cleaned_image = image_bgr   # Will be updated if step 2 runs
    line_removed_image = image_bgr  # Will be updated if step 5 runs
    roi_positions = [(5, 6, 25, 32), (37, 6, 25, 32), (69, 6, 25, 32), (101, 6, 25, 32), (133, 6, 25, 32)]
    processed_binary = None
    target_colors = [None] * 5
    box_images = []
    
    # Execute steps in specified order with proper chaining
    # Each step receives the output from the previous step as input
    for step_index, step_num in enumerate(step_order):
        step_position = step_index + 1
        print(f"   🔄 Executing Step {step_num} at position {step_position} (step order: {step_order})")
        
        if step_num == 1:
            print(f"       Step 1 (Isolated Pixels): Processing {'current_image' if current_binary is None else 'binary→BGR conversion'}")
            # Step 1 works on BGR images
            if current_binary is not None:
                # Convert binary back to BGR for step 1
                input_image = cv2.cvtColor(current_binary, cv2.COLOR_GRAY2BGR)
                print(f"       ⚙️  Converted binary to BGR for Step 1")
            else:
                input_image = current_image
            
            current_image = preprocessing_step1_isolated_pixels(input_image, enabled=True)
            current_binary = None  # Reset binary since we now have a BGR result
            denoised_image = current_image  # Keep track for visualization
            print(f"       ✅ Step 1 output: BGR image {current_image.shape}")
            
        elif step_num == 2:
            print(f"       Step 2 (Outside Boxes): Processing {'current_image' if current_binary is None else 'binary→BGR conversion'}")
            # Step 2 works on BGR images
            if current_binary is not None:
                # Convert binary back to BGR for step 2
                input_image = cv2.cvtColor(current_binary, cv2.COLOR_GRAY2BGR)
                print(f"       ⚙️  Converted binary to BGR for Step 2")
            else:
                input_image = current_image
                
            current_image, roi_positions = preprocessing_step2_outside_boxes(input_image, enabled=True)
            current_binary = None  # Reset binary since we now have a BGR result
            cleaned_image = current_image  # Keep track for visualization
            print(f"       ✅ Step 2 output: BGR image {current_image.shape}")
            
        elif step_num == 3:
            print(f"       Step 3 (Color Analysis): Processing {'current_image' if current_binary is None else 'current_binary (will be ignored)'}")
            # Step 3 always works on BGR images (it creates binary output)
            if current_binary is not None:
                print(f"       ⚠️  Warning: Step 3 ignores binary input and uses current_image instead")
            
            current_binary, target_colors, box_images = preprocessing_step3_color_analysis(
                current_image, roi_positions, tolerance, use_pattern_shadow_detection, enabled=True
            )
            processed_binary = current_binary  # Keep track for visualization
            print(f"       ✅ Step 3 output: Binary image {current_binary.shape if current_binary is not None else None}, target_colors: {len(target_colors) if target_colors else 0}")
            
        elif step_num == 4:
            print(f"       Step 4 (Neighbor Replacement): Processing current_binary")
            # Step 4 works on binary images and requires target_colors from step 3
            if current_binary is not None and target_colors is not None:
                current_binary = preprocessing_step4_neighbor_replacement(current_binary, roi_positions, target_colors, enabled=True)
                processed_binary = current_binary  # Update final binary
                print(f"       ✅ Step 4 output: Binary image {current_binary.shape}")
            elif current_binary is None:
                print("       ⚠️  Warning: Step 4 requires binary image input. Converting current_image to binary first.")
                # Emergency conversion: convert current BGR to binary
                gray = cv2.cvtColor(current_image, cv2.COLOR_BGR2GRAY)
                _, current_binary = cv2.threshold(gray, 127, 255, cv2.THRESH_BINARY)
                if target_colors is not None:
                    current_binary = preprocessing_step4_neighbor_replacement(current_binary, roi_positions, target_colors, enabled=True)
                    processed_binary = current_binary
                    print(f"       ✅ Step 4 output: Binary image {current_binary.shape} (emergency conversion)")
                else:
                    print("       ❌ Step 4 also requires target_colors from Step 3. Skipping.")
            else:
                print("       ❌ Step 4 requires target_colors from Step 3. Skipping.")
                
        elif step_num == 5:
            print(f"       Step 5 (Line Detection): Processing {'current_image' if current_binary is None else 'binary→BGR conversion'}")
            # Step 5 works on BGR images
            if current_binary is not None:
                # Convert binary back to BGR for step 5
                input_image = cv2.cvtColor(current_binary, cv2.COLOR_GRAY2BGR)
                print(f"       ⚙️  Converted binary to BGR for Step 5")
            else:
                input_image = current_image
                
            current_image = preprocessing_step5_line_detection(input_image, roi_positions, min_line_length, min_connectivity, enabled=True)
            current_binary = None  # Reset binary since we now have a BGR result
            line_removed_image = current_image  # Keep track for visualization
            print(f"       ✅ Step 5 output: BGR image {current_image.shape}")
            
        elif step_num == 6:
            print(f"       Step 6 (White Pixel Filling): Processing current_binary")
            # Step 6 works on binary images and requires target_colors from step 3
            if current_binary is not None and target_colors is not None:
                current_binary = preprocessing_step6_white_pixel_filling(
                    current_binary, roi_positions, target_colors, min_surrounding_sides, enabled=True
                )
                processed_binary = current_binary  # Update final binary
                print(f"       ✅ Step 6 output: Binary image {current_binary.shape}")
            elif current_binary is None:
                print("       ⚠️  Warning: Step 6 requires binary image input. Converting current_image to binary first.")
                # Emergency conversion: convert current BGR to binary
                gray = cv2.cvtColor(current_image, cv2.COLOR_BGR2GRAY)
                _, current_binary = cv2.threshold(gray, 127, 255, cv2.THRESH_BINARY)
                if target_colors is not None:
                    current_binary = preprocessing_step6_white_pixel_filling(
                        current_binary, roi_positions, target_colors, min_surrounding_sides, enabled=True
                    )
                    processed_binary = current_binary
                    print(f"       ✅ Step 6 output: Binary image {current_binary.shape} (emergency conversion)")
                else:
                    print("       ❌ Step 6 also requires target_colors from Step 3. Skipping.")
            else:
                print("       ❌ Step 6 requires target_colors from Step 3. Skipping.")
        
        else:
            print(f"       ❌ Unknown step number: {step_num}")
        
        print(f"       📊 Current state after Step {step_num}: current_image={current_image.shape if current_image is not None else None}, current_binary={current_binary.shape if current_binary is not None else None}")
        print()
    
    # If color analysis wasn't run, create a simple binary as fallback
    if processed_binary is None:
        print("   ⚠️  No color analysis step - creating simple binary fallback")
        gray = cv2.cvtColor(current_image, cv2.COLOR_BGR2GRAY)
        _, processed_binary = cv2.threshold(gray, 127, 255, cv2.THRESH_BINARY)
        
        # Create dummy box images for visualization
        if not box_images:
            for x, y, w, h in roi_positions:
                x1, y1 = max(0, x), max(0, y)
                x2, y2 = min(current_image.shape[1], x + w), min(current_image.shape[0], y + h)
                if x2 > x1 and y2 > y1:
                    box_images.append(current_image[y1:y2, x1:x2])
                else:
                    box_images.append(np.zeros((h, w, 3), dtype=np.uint8))
    
    print("   ✅ Ordered modular preprocessing completed!")
    
    return processed_binary, target_colors, box_images, roi_positions, cleaned_image, denoised_image, line_removed_image

# Legacy function for backward compatibility
def color_based_preprocessing(image_bgr, 
                            tolerance=25,
                            use_pattern_shadow_detection=True):
    """Legacy function - calls the new ordered version with default order [1,2,3,4,5]"""
    return color_based_preprocessing_ordered(
        image_bgr, 
        step_order=[1, 2, 3, 4, 5],
        tolerance=tolerance,
        use_pattern_shadow_detection=use_pattern_shadow_detection
    )

def extract_character_rois_from_binary(binary_image, roi_positions):
    """
    Extract character ROIs from the color-based binary image
    """
    height, width = binary_image.shape
    digit_images = []
    
    for i, (x, y, w, h) in enumerate(roi_positions):
        # Extract ROI with bounds checking
        x1, y1 = max(0, x), max(0, y)
        x2, y2 = min(width, x + w), min(height, y + h)
        
        if x2 > x1 and y2 > y1:
            roi = binary_image[y1:y2, x1:x2]
            # Resize to match CNN input size
            roi_resized = cv2.resize(roi, (IMG_WIDTH_MODEL, IMG_HEIGHT_MODEL))
            digit_images.append(roi_resized)
        else:
            # Create empty ROI if bounds are invalid
            digit_images.append(np.zeros((IMG_HEIGHT_MODEL, IMG_WIDTH_MODEL), dtype=np.uint8))
    
    return digit_images

# Apply modular color-based preprocessing - easily configure which steps to use!
print("🚀 Applying modular color-based preprocessing...")

# 🔧 EASY CONFIGURATION - Enable/Disable and ORDER each step:
# 
# STEP ORDER CONTROL:
STEP_ORDER = [3]    # Sequential chaining: Step 3 (color analysis) → Step 4 (neighbor replacement)
#   📊 Data Flow: Original BGR → Color Analysis (→ Binary + target_colors) → Neighbor Replacement (→ Enhanced Binary)
# STEP_ORDER = [5, 1, 2, 3, 4]      # 🔥 LINE DETECTION FIRST: line detection → isolated pixels → outside boxes → color analysis → neighbor replacement
#   📊 Data Flow: Original BGR → Line Detection → Isolated Pixels → Outside Boxes → Color Analysis → Neighbor Replacement
# STEP_ORDER = [2, 3, 4, 5, 1]      # 🔥 ISOLATED PIXELS LAST: outside boxes → color analysis → neighbor replacement → line detection → isolated pixels
#   📊 Data Flow: Original BGR → Outside Boxes → Color Analysis → Neighbor Replacement → Line Detection → Isolated Pixels

# 🔧 SIMPLE CONTROL: Only STEP_ORDER matters - no separate enable/disable flags needed!

# ADVANCED SETTINGS:
COLOR_TOLERANCE = 30                  # Color matching tolerance for exact digit color
USE_PATTERN_SHADOW_DETECTION = True   # Use intelligent pattern-based shadow detection (warm/cool shadows)
MIN_LINE_LENGTH = 10                  # Minimum length to consider as line (optimized for 1-pixel lines)
MIN_CONNECTIVITY = 3                  # Minimum connected pixels for line detection (optimized for thin lines)
MIN_SURROUNDING_SIDES = 3             # Minimum sides for white pixel filling (3 or 4)

# 🎛️ STEP ORDER EXAMPLES WITH SEQUENTIAL CHAINING:
# 
# Normal pipeline (classic order):
# STEP_ORDER = [1, 2, 3, 4, 5, 6]     # Step 1 → Step 2 → Step 3 → Step 4 → Step 5 → Step 6
#   Flow: Original BGR → Denoised BGR → Cleaned BGR → Binary+Colors → Enhanced Binary → Line-free BGR → Final Binary
# 
# Line detection FIRST (remove lines before processing):
# STEP_ORDER = [5, 1, 2, 3, 4, 6]     # Step 5 → Step 1 → Step 2 → Step 3 → Step 4 → Step 6
#   Flow: Original BGR → Line-free BGR → Denoised BGR → Cleaned BGR → Binary+Colors → Enhanced Binary → Final Binary
# 
# Simple color processing only:
# STEP_ORDER = [3, 4]                 # Step 3 → Step 4
#   Flow: Original BGR → Binary+Colors → Enhanced Binary
# 
# Line removal + color analysis:
# STEP_ORDER = [5, 3, 4]              # Step 5 → Step 3 → Step 4
#   Flow: Original BGR → Line-free BGR → Binary+Colors → Enhanced Binary
# 
# Denoising + line removal + color analysis:
# STEP_ORDER = [1, 5, 3]              # Step 1 → Step 5 → Step 3
#   Flow: Original BGR → Denoised BGR → Line-free BGR → Binary+Colors
# 
# Binary-focused processing:
# STEP_ORDER = [3, 6, 4]              # Step 3 → Step 6 → Step 4
#   Flow: Original BGR → Binary+Colors → Filled Binary → Enhanced Binary
# 
# Only preprocessing (no binary output):
# STEP_ORDER = [1, 2, 5]              # Step 1 → Step 2 → Step 5
#   Flow: Original BGR → Denoised BGR → Cleaned BGR → Line-free BGR

# 📋 SIMPLE STEP ORDER EXAMPLES:
# 
# Only line detection:
# STEP_ORDER = [5]
# 
# Line detection + color analysis + neighbor replacement:
# STEP_ORDER = [5, 3, 4]
# 
# All steps except line detection:
# STEP_ORDER = [1, 2, 3, 4, 6]
# 
# Only color analysis:
# STEP_ORDER = [3]

image_processed, target_colors, box_images, roi_positions, cleaned_image, denoised_image, line_removed_image = color_based_preprocessing_ordered(
    image_bgr, 
    step_order=STEP_ORDER,
    tolerance=COLOR_TOLERANCE,
    use_pattern_shadow_detection=USE_PATTERN_SHADOW_DETECTION,
    min_line_length=MIN_LINE_LENGTH,
    min_connectivity=MIN_CONNECTIVITY,
    min_surrounding_sides=MIN_SURROUNDING_SIDES
)

# Extract character ROIs
print("📋 Extracting character ROIs from color-based binary image...")
digit_images = extract_character_rois_from_binary(image_processed, roi_positions)
print(f"✅ Extracted {len(digit_images)} characters")

# --- Predict each digit ---
predicted_text = ""
output_image = image_bgr.copy()

print("🔮 Making predictions using CNN model...")

for i, digit_roi in enumerate(digit_images):
    if digit_roi.size == 0:
        print(f"   Digit {i+1}: Empty ROI, skipping")
        continue
    
    # Prepare for model input (normalize, add batch and channel dimensions)
    processed_digit = digit_roi.astype('float32') / 255.0
    processed_digit = np.expand_dims(processed_digit, axis=-1)
    processed_digit = np.expand_dims(processed_digit, axis=0)

    # Make prediction using CNN model
    prediction = model.predict(processed_digit, verbose=0)
    predicted_class_index = np.argmax(prediction, axis=1)[0]
    confidence = np.max(prediction) * 100
    predicted_digit_char = PERSIAN_DIGITS[predicted_class_index]

    print(f"   Digit {i+1}: '{predicted_digit_char}' (confidence: {confidence:.1f}%)")
    predicted_text += predicted_digit_char
    
    # Draw bounding box for visualization
    if i < len(roi_positions):
        x, y, w, h = roi_positions[i]
        cv2.rectangle(output_image, (x, y), (x + w, y + h), (0, 255, 0), 2)
        cv2.putText(output_image, predicted_digit_char, (x, y-5), 
                   cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)

print(f"Image: {CAPTCHA_IMAGE_PATH}")
print(f"Step Order: {STEP_ORDER}")
print(f"Only steps in STEP_ORDER will execute - no separate enable/disable flags!")
print(f"Advanced Settings: Tolerance={COLOR_TOLERANCE}, PatternShadows={USE_PATTERN_SHADOW_DETECTION}, LineLength={MIN_LINE_LENGTH}, Connectivity={MIN_CONNECTIVITY}, SurroundingSides={MIN_SURROUNDING_SIDES}")
print(f"Target Colors (BGR): {target_colors}")
print(f"Predicted Text: {predicted_text}")

# Create visualization
plt.figure(figsize=(28, 12))

# Show original image
plt.subplot(3, 8, 1)
plt.imshow(cv2.cvtColor(image_bgr, cv2.COLOR_BGR2RGB))
plt.title("1. Original Captcha", fontsize=10, fontweight='bold')
plt.axis('off')

# Show denoised image (isolated pixels removed)
plt.subplot(3, 8, 2)
plt.imshow(cv2.cvtColor(denoised_image, cv2.COLOR_BGR2RGB))
step1_status = "✅ ON" if 1 in STEP_ORDER else "⏭️ OFF"
plt.title(f"2. Denoised ({step1_status})\n(Isolated Pixels Removed)", fontsize=10, fontweight='bold')
plt.axis('off')

# Show cleaned image (outside boxes removed)
plt.subplot(3, 8, 3)
plt.imshow(cv2.cvtColor(cleaned_image, cv2.COLOR_BGR2RGB))
step2_status = "✅ ON" if 2 in STEP_ORDER else "⏭️ OFF"
plt.title(f"3. Boxes Only ({step2_status})\n(Outside Removed)", fontsize=10, fontweight='bold')
plt.axis('off')

# Show target color patches for each box
plt.subplot(3, 8, 4)
# Create a combined color patch showing all target colors
color_patches = []
for i, target_color in enumerate(target_colors):
    if target_color is not None:
        # BGR to RGB conversion
        color_rgb = target_color[::-1]
        patch = np.full((10, 50, 3), color_rgb, dtype=np.uint8)
        color_patches.append(patch)
    else:
        # White patch for empty boxes
        patch = np.full((10, 50, 3), [255, 255, 255], dtype=np.uint8)
        color_patches.append(patch)

combined_patch = np.vstack(color_patches)
plt.imshow(combined_patch)
plt.title(f"4. Target Colors\n(Box 1-5)", fontsize=10, fontweight='bold')
plt.axis('off')

# Show color-based processed image
plt.subplot(3, 8, 5)
plt.imshow(image_processed, cmap='gray')
step3_status = "✅ ON" if 3 in STEP_ORDER else "⏭️ OFF"
step4_status = "✅ ON" if 4 in STEP_ORDER else "⏭️ OFF"
step6_status = "✅ ON" if 6 in STEP_ORDER else "⏭️ OFF"
plt.title(f"5. Binary (S3:{step3_status} S4:{step4_status} S6:{step6_status})\n(Color + Neighbor + Fill)", fontsize=10, fontweight='bold')
plt.axis('off')

# Show individual box images (original colors from cleaned image)
for i in range(min(3, len(box_images))):
    plt.subplot(3, 8, 6 + i)
    if box_images[i].size > 0:
        plt.imshow(cv2.cvtColor(box_images[i], cv2.COLOR_BGR2RGB))
    plt.title(f"Box {i+1}", fontsize=9)
    plt.axis('off')

# Show individual digit ROIs (processed)
for i in range(min(5, len(digit_images))):
    plt.subplot(3, 8, 9 + i)
    plt.imshow(digit_images[i], cmap='gray')
    char_prediction = predicted_text[i] if i < len(predicted_text) else '?'
    plt.title(f"Digit {i+1}: '{char_prediction}'", fontsize=9, fontweight='bold')
    plt.axis('off')

# Show final result
plt.subplot(3, 8, (15, 24))
plt.imshow(cv2.cvtColor(output_image, cv2.COLOR_BGR2RGB))
config_str = f"Order:{STEP_ORDER} (Only steps in order execute)"
plt.title(f"Final Result: {predicted_text}\n(6-Step Dynamic Order)", fontsize=10, fontweight='bold')
plt.axis('off')

# Save visualization
os.makedirs("results", exist_ok=True)
result_filename = f"color_based_result_{selected_file.split('.')[0]}.png"
result_path = os.path.join("results", result_filename)
plt.tight_layout()
plt.savefig(result_path, dpi=150, bbox_inches='tight')
print(f"Visualization saved to: {result_path}")

# Save the denoised image (isolated pixels removed)
denoised_filename = f"color_based_denoised_{selected_file}"
denoised_path = os.path.join("results", denoised_filename)
cv2.imwrite(denoised_path, denoised_image)
print(f"Denoised image saved to: {denoised_path}")

# Save the cleaned image (boxes only)
cleaned_filename = f"color_based_boxes_only_{selected_file}"
cleaned_path = os.path.join("results", cleaned_filename)
cv2.imwrite(cleaned_path, cleaned_image)
print(f"Boxes-only image saved to: {cleaned_path}")

# Save the line-removed image (lines detected and removed)
line_removed_filename = f"color_based_line_removed_{selected_file}"
line_removed_path = os.path.join("results", line_removed_filename)
cv2.imwrite(line_removed_path, line_removed_image)
print(f"Line-removed image saved to: {line_removed_path}")

# Save the processed binary image
binary_filename = f"color_based_binary_{selected_file}"
binary_path = os.path.join("results", binary_filename)
cv2.imwrite(binary_path, image_processed)
print(f"Binary image saved to: {binary_path}")

# Save the final result image
output_filename = f"color_based_output_{selected_file}"
output_path = os.path.join("results", output_filename)
cv2.imwrite(output_path, output_image)
print(f"Output image saved to: {output_path}")

plt.close()
print("✅ 6-Step dynamic ordered color-based prediction completed!")
print(f"🔧 Used step order: {STEP_ORDER}")
print(f"🔧 Only steps in STEP_ORDER executed - no separate enable/disable flags!")
print(f"📊 To test different orders, modify STEP_ORDER!")
print(f"📊 Simple control: only STEP_ORDER matters!")