"""
5-STEP DYNAMIC ORDERED COLOR-BASED CAPTCHA PREDICTION SYSTEM

🔧 DYNAMIC STEP ORDERING:
Control the ORDER of preprocessing steps by modifying STEP_ORDER around line 686:

STEP_ORDER = [1, 2, 3, 4, 5]   # Normal: isolated → outside → color → neighbor → line detection
STEP_ORDER = [5, 1, 2, 3, 4]   # Line detection FIRST: line → isolated → outside → color → neighbor
STEP_ORDER = [2, 3, 4, 5, 1]   # Isolated pixels LAST: outside → color → neighbor → line → isolated
STEP_ORDER = [5, 3, 4]         # Only line + color processing: line detection → color → neighbor
STEP_ORDER = [1, 2, 3, 4]      # Skip line detection: isolated → outside → color → neighbor

🔧 ENABLE/DISABLE CONFIGURATION:
Control which steps are active by modifying these variables around line 691:

ENABLE_STEP1_ISOLATED_PIXELS = True/False      # Remove pixels surrounded by white (3+ sides)
ENABLE_STEP2_OUTSIDE_BOXES = True/False        # Remove pixels outside digit boxes  
ENABLE_STEP3_COLOR_ANALYSIS = True/False       # Advanced color analysis per box
ENABLE_STEP4_NEIGHBOR_REPLACEMENT = True/False # Smart neighbor replacement (4-connected)
ENABLE_STEP5_LINE_DETECTION = True/False       # Line detection and removal (outside-box analysis)
ENABLE_STEP6_WHITE_PIXEL_FILLING = True/False     # White pixel filling (3+ sides surrounded by target color)

🎯 THE 6 INDEPENDENT STEPS:
Step 1: Isolated pixel removal (your proven technique)
Step 2: Outside-box pixel removal (focus on digit areas)  
Step 3: Color analysis (find target colors per box, preserve digit continuity, intelligent shadow detection)
Step 4: Neighbor replacement (smart connectivity enhancement)
Step 5: Thin line detection (analyze outside-box pixels, detect & remove 1-pixel line colors)
Step 6: White pixel filling (fill white pixels surrounded by target color on 3+ sides) ← NEW!

Each step can be independently enabled/disabled AND reordered for testing different combinations!
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
    Create a binary image for a single box with digit continuity preservation and intelligent shadow detection:
    
    preserve_digit_continuity=True + use_pattern_shadow_detection=True (NEW):
    - Keep all target color pixels (the actual digit)
    - For non-target pixels:
      - If they match the captcha's lighting pattern (warm/cool shadows) → keep as white (preserve shadows)
      - If they are different (lines) but neighbor target pixels → convert to target color (prevent gaps)
    - Uses discovered pattern: left shadows = warmer tones, right shadows = cooler tones
    
    use_pattern_shadow_detection=False (FALLBACK):
    - Uses simple color tolerance for shadow detection
    
    Parameters:
    - tolerance: Strictness for exact target color matching
    - use_pattern_shadow_detection: Use intelligent pattern-based shadow detection
    """
    if target_color is None:
        # If no target color found, return a white image
        return np.ones_like(box_image[:,:,0]) * 255
    
    # Convert to numpy array for easier processing
    target_color = np.array(target_color)
    height, width = box_image.shape[:2]
    
    # Create a mask for pixels similar to target color
    target_mask = np.all(np.abs(box_image.astype(int) - target_color.astype(int)) <= tolerance, axis=2)
    
    # Define neighbor offsets (4-connected neighborhood: up, down, left, right only)
    neighbor_offsets = [(-1, 0), (1, 0), (0, -1), (0, 1)]  # up, down, left, right
    
    if preserve_digit_continuity:
        # DIGIT CONTINUITY MODE: Keep target pixels + convert only line pixels (not shadows)
        enhanced_mask = target_mask.copy()
        
        if use_pattern_shadow_detection:
            print(f"       🔄 Using intelligent pattern-based shadow detection (tolerance={tolerance})...")
            
            converted_pixels = 0
            warm_shadows_preserved = 0
            cool_shadows_preserved = 0
            
            # Iterate through each pixel
            for y in range(height):
                for x in range(width):
                    if not target_mask[y, x]:  # If pixel is NOT exact target color
                        pixel_bgr = box_image[y, x]
                        
                        # Check if this pixel matches known shadow patterns
                        is_warm_shadow_pixel = is_warm_shadow(pixel_bgr, target_color)
                        is_cool_shadow_pixel = is_cool_shadow(pixel_bgr, target_color)
                        
                        if is_warm_shadow_pixel:
                            # This is a warm shadow pixel - keep as white (don't convert)
                            warm_shadows_preserved += 1
                        elif is_cool_shadow_pixel:
                            # This is a cool shadow pixel - keep as white (don't convert)
                            cool_shadows_preserved += 1
                        else:
                            # This is likely a line/different pixel - check if it neighbors digit pixels
                            has_target_neighbor = False
                            
                            for dy, dx in neighbor_offsets:
                                ny, nx = y + dy, x + dx
                                # Check bounds and if neighbor is target color
                                if (0 <= ny < height and 0 <= nx < width and target_mask[ny, nx]):
                                    has_target_neighbor = True
                                    break
                            
                            # If has target neighbor, convert this line pixel to digit color (prevent gaps)
                            if has_target_neighbor:
                                enhanced_mask[y, x] = True
                                converted_pixels += 1
            
            final_mask = enhanced_mask
            print(f"       ✨ Converted {converted_pixels} line pixels to digit color (preserved continuity)")
            print(f"       🌅 Preserved {warm_shadows_preserved} warm shadow pixels (left side pattern)")
            print(f"       🌌 Preserved {cool_shadows_preserved} cool shadow pixels (right side pattern)")
            
        else:
            # Fallback to simple tolerance-based detection
            print(f"       🔄 Using simple tolerance-based shadow detection (tolerance={tolerance})...")
            
            # Create shadow mask to identify pixels similar to target color (shadows)
            shadow_tolerance = 40  # Default fallback value
            shadow_mask = np.all(np.abs(box_image.astype(int) - target_color.astype(int)) <= shadow_tolerance, axis=2)
            
            converted_pixels = 0
            shadow_pixels_preserved = 0
            
            # Iterate through each pixel
            for y in range(height):
                for x in range(width):
                    if not target_mask[y, x]:  # If pixel is NOT exact target color
                        pixel_bgr = box_image[y, x]
                        
                        # Check if this pixel is a shadow (similar to target color)
                        is_shadow = shadow_mask[y, x]
                        
                        if is_shadow:
                            # This is a shadow pixel - keep as white (don't convert)
                            shadow_pixels_preserved += 1
                        else:
                            # This is a line/different pixel - check if it neighbors digit pixels
                            has_target_neighbor = False
                            
                            for dy, dx in neighbor_offsets:
                                ny, nx = y + dy, x + dx
                                # Check bounds and if neighbor is target color
                                if (0 <= ny < height and 0 <= nx < width and target_mask[ny, nx]):
                                    has_target_neighbor = True
                                    break
                            
                            # If has target neighbor, convert this line pixel to digit color (prevent gaps)
                            if has_target_neighbor:
                                enhanced_mask[y, x] = True
                                converted_pixels += 1
            
            final_mask = enhanced_mask
            print(f"       ✨ Converted {converted_pixels} line pixels to digit color (preserved continuity)")
            print(f"       🎨 Preserved {shadow_pixels_preserved} shadow pixels (kept as white)")
        
    else:
        # ORIGINAL MODE: Keep only exact target color pixels
        final_mask = target_mask
        print(f"       🔄 Simple mode: keeping only exact target color pixels...")
    
    # Create binary image
    binary_image = np.ones_like(box_image[:,:,0]) * 255  # Start with white
    binary_image[final_mask] = 0  # Make selected pixels black
    
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
    Step 4: Intelligent neighbor replacement (4-connected)
    Apply neighbor-based color replacement to improve digit continuity
    """
    if not enabled:
        print("   ⏭️  Step 4: Neighbor replacement - DISABLED")
        return binary_image
    
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
                                   step1_isolated_pixels=True,
                                   step2_outside_boxes=True, 
                                   step3_color_analysis=True,
                                   step4_neighbor_replacement=True,
                                   step5_line_detection=True,
                                   step6_white_pixel_filling=True,
                                   tolerance=25,
                                   use_pattern_shadow_detection=True,
                                   min_line_length=10,
                                   min_connectivity=3,
                                   min_surrounding_sides=3):
    """
    Enhanced modular preprocessing with configurable step ORDER:
    
    Parameters:
    - step_order: List defining the order of steps [1,2,3,4,5,6] = normal, [5,1,2,3,4,6] = line detection first, etc.
    - step1_isolated_pixels: Enable/disable isolated pixel removal
    - step2_outside_boxes: Enable/disable outside-box removal  
    - step3_color_analysis: Enable/disable color analysis
    - step4_neighbor_replacement: Enable/disable neighbor-based replacement
    - step5_line_detection: Enable/disable line detection and removal (optimized for 1-pixel lines)
    - step6_white_pixel_filling: Enable/disable white pixel filling (3+ sides surrounded by target color)
    - tolerance: Color matching tolerance for exact digit color
    - use_pattern_shadow_detection: Use intelligent pattern-based shadow detection (warm/cool shadows)
    - min_line_length: Minimum length for line detection (optimized for thin lines)
    - min_connectivity: Minimum connectivity for line detection (optimized for thin lines)
    - min_surrounding_sides: Minimum sides for white pixel filling (3 or 4)
    """
    print("🎨 Starting ordered modular color-based preprocessing...")
    print(f"   🔧 Step Order: {step_order}")
    print(f"   🔧 Configuration: Step1={step1_isolated_pixels}, Step2={step2_outside_boxes}, Step3={step3_color_analysis}, Step4={step4_neighbor_replacement}, Step5={step5_line_detection}, Step6={step6_white_pixel_filling}")
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
    
    # Execute steps in specified order
    for step_num in step_order:
        if step_num == 1:
            print(f"   🔄 Executing Step {step_num} (Isolated Pixels) at position {step_order.index(step_num) + 1}")
            current_image = preprocessing_step1_isolated_pixels(current_image, step1_isolated_pixels)
            denoised_image = current_image  # Keep track for visualization
            
        elif step_num == 2:
            print(f"   🔄 Executing Step {step_num} (Outside Boxes) at position {step_order.index(step_num) + 1}")
            current_image, roi_positions = preprocessing_step2_outside_boxes(current_image, step2_outside_boxes)
            cleaned_image = current_image  # Keep track for visualization
            
        elif step_num == 3:
            print(f"   🔄 Executing Step {step_num} (Color Analysis) at position {step_order.index(step_num) + 1}")
            current_binary, target_colors, box_images = preprocessing_step3_color_analysis(
                current_image, roi_positions, tolerance, use_pattern_shadow_detection, step3_color_analysis
            )
            processed_binary = current_binary  # Keep track for visualization
            
        elif step_num == 4:
            print(f"   🔄 Executing Step {step_num} (Neighbor Replacement) at position {step_order.index(step_num) + 1}")
            if current_binary is not None:
                current_binary = preprocessing_step4_neighbor_replacement(current_binary, roi_positions, target_colors, step4_neighbor_replacement)
                processed_binary = current_binary  # Update final binary
            else:
                print("       ⚠️  Warning: Step 4 requires binary image from Step 3. Skipping.")
                
        elif step_num == 5:
            print(f"   🔄 Executing Step {step_num} (Line Detection) at position {step_order.index(step_num) + 1}")
            current_image = preprocessing_step5_line_detection(current_image, roi_positions, min_line_length, min_connectivity, step5_line_detection)
            line_removed_image = current_image  # Keep track for visualization
            
        elif step_num == 6:
            print(f"   🔄 Executing Step {step_num} (White Pixel Filling) at position {step_order.index(step_num) + 1}")
            if current_binary is not None and target_colors is not None:
                current_binary = preprocessing_step6_white_pixel_filling(
                    current_binary, roi_positions, target_colors, min_surrounding_sides, step6_white_pixel_filling
                )
                processed_binary = current_binary  # Update final binary
            else:
                print("       ⚠️  Warning: Step 6 requires binary image and target colors from Step 3. Skipping.")
    
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
                            step1_isolated_pixels=True,
                            step2_outside_boxes=True, 
                            step3_color_analysis=True,
                            neighbor_replacement=True,
                            tolerance=25,
                            use_pattern_shadow_detection=True):
    """Legacy function - calls the new ordered version with default order [1,2,3,4,5,6]"""
    return color_based_preprocessing_ordered(
        image_bgr, 
        step_order=[1, 2, 3, 4, 5],
        step1_isolated_pixels=step1_isolated_pixels,
        step2_outside_boxes=step2_outside_boxes,
        step3_color_analysis=step3_color_analysis,
        step4_neighbor_replacement=neighbor_replacement,
        step5_line_detection=True,
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
STEP_ORDER = [3]        # Normal order: isolated pixels → outside boxes → color analysis → neighbor replacement → line detection
# STEP_ORDER = [5, 1, 2, 3, 4]      # 🔥 LINE DETECTION FIRST: line detection → isolated pixels → outside boxes → color analysis → neighbor replacement
# STEP_ORDER = [2, 3, 4, 5, 1]      # 🔥 ISOLATED PIXELS LAST: outside boxes → color analysis → neighbor replacement → line detection → isolated pixels

# STEP ENABLE/DISABLE:
ENABLE_STEP1_ISOLATED_PIXELS = True   # Your proven noise removal technique
ENABLE_STEP2_OUTSIDE_BOXES = True     # Remove background outside boxes
ENABLE_STEP3_COLOR_ANALYSIS = True    # Advanced color analysis per box
ENABLE_STEP4_NEIGHBOR_REPLACEMENT = True  # Smart neighbor replacement (4-connected)
ENABLE_STEP5_LINE_DETECTION = True    # Thin line detection and removal (optimized for 1-pixel lines)
ENABLE_STEP6_WHITE_PIXEL_FILLING = True  # White pixel filling (3+ sides surrounded by target color)

# ADVANCED SETTINGS:
COLOR_TOLERANCE = 30                  # Color matching tolerance for exact digit color
USE_PATTERN_SHADOW_DETECTION = True   # Use intelligent pattern-based shadow detection (warm/cool shadows)
MIN_LINE_LENGTH = 10                  # Minimum length to consider as line (optimized for 1-pixel lines)
MIN_CONNECTIVITY = 3                  # Minimum connected pixels for line detection (optimized for thin lines)
MIN_SURROUNDING_SIDES = 3             # Minimum sides for white pixel filling (3 or 4)

# 🎛️ STEP ORDER EXAMPLES:
# 
# Normal order (default):
# STEP_ORDER = [1, 2, 3, 4, 5, 6]     # isolated pixels → outside boxes → color analysis → neighbor replacement → line detection → white pixel filling
# 
# Line detection FIRST (detect lines on raw image):
# STEP_ORDER = [5, 1, 2, 3, 4, 6]     # line detection → isolated pixels → outside boxes → color analysis → neighbor replacement → white pixel filling
# 
# Isolated pixels LAST:
# STEP_ORDER = [2, 3, 4, 5, 6, 1]     # outside boxes → color analysis → neighbor replacement → line detection → white pixel filling → isolated pixels
# 
# White pixel filling FIRST (after color analysis):
# STEP_ORDER = [1, 2, 3, 6, 4, 5]     # isolated pixels → outside boxes → color analysis → white pixel filling → neighbor replacement → line detection
# 
# Only line detection + color processing:
# STEP_ORDER = [5, 3, 4]              # line detection → color analysis → neighbor replacement
# 
# Skip line detection:
# STEP_ORDER = [1, 2, 3, 4]           # isolated pixels → outside boxes → color analysis → neighbor replacement
# 
# Only preprocessing (no color analysis):
# STEP_ORDER = [5, 1, 2]              # line detection → isolated pixels → outside boxes

# 📋 OTHER USEFUL ENABLE/DISABLE CONFIGURATIONS:
# 
# Only line detection:
# ENABLE_STEP1_ISOLATED_PIXELS = False
# ENABLE_STEP2_OUTSIDE_BOXES = False
# ENABLE_STEP3_COLOR_ANALYSIS = False
# ENABLE_STEP4_NEIGHBOR_REPLACEMENT = False
# ENABLE_STEP5_LINE_DETECTION = True
# STEP_ORDER = [5]
# 
# Line detection + color analysis only:
# ENABLE_STEP1_ISOLATED_PIXELS = False
# ENABLE_STEP2_OUTSIDE_BOXES = False
# ENABLE_STEP3_COLOR_ANALYSIS = True
# ENABLE_STEP4_NEIGHBOR_REPLACEMENT = True
# ENABLE_STEP5_LINE_DETECTION = True
# STEP_ORDER = [5, 3, 4]
# 
# All steps except line detection:
# ENABLE_STEP5_LINE_DETECTION = False
# STEP_ORDER = [1, 2, 3, 4]

image_processed, target_colors, box_images, roi_positions, cleaned_image, denoised_image, line_removed_image = color_based_preprocessing_ordered(
    image_bgr, 
    step_order=STEP_ORDER,
    step1_isolated_pixels=ENABLE_STEP1_ISOLATED_PIXELS,
    step2_outside_boxes=ENABLE_STEP2_OUTSIDE_BOXES,
    step3_color_analysis=ENABLE_STEP3_COLOR_ANALYSIS,
    step4_neighbor_replacement=ENABLE_STEP4_NEIGHBOR_REPLACEMENT,
    step5_line_detection=ENABLE_STEP5_LINE_DETECTION,
    step6_white_pixel_filling=ENABLE_STEP6_WHITE_PIXEL_FILLING,
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
print(f"Configuration: Step1={ENABLE_STEP1_ISOLATED_PIXELS}, Step2={ENABLE_STEP2_OUTSIDE_BOXES}, Step3={ENABLE_STEP3_COLOR_ANALYSIS}, Step4={ENABLE_STEP4_NEIGHBOR_REPLACEMENT}, Step5={ENABLE_STEP5_LINE_DETECTION}, Step6={ENABLE_STEP6_WHITE_PIXEL_FILLING}")
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
step1_status = "✅ ON" if ENABLE_STEP1_ISOLATED_PIXELS else "⏭️ OFF"
plt.title(f"2. Denoised ({step1_status})\n(Isolated Pixels Removed)", fontsize=10, fontweight='bold')
plt.axis('off')

# Show cleaned image (outside boxes removed)
plt.subplot(3, 8, 3)
plt.imshow(cv2.cvtColor(cleaned_image, cv2.COLOR_BGR2RGB))
step2_status = "✅ ON" if ENABLE_STEP2_OUTSIDE_BOXES else "⏭️ OFF"
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
step3_status = "✅ ON" if ENABLE_STEP3_COLOR_ANALYSIS else "⏭️ OFF"
step4_status = "✅ ON" if ENABLE_STEP4_NEIGHBOR_REPLACEMENT else "⏭️ OFF"
step6_status = "✅ ON" if ENABLE_STEP6_WHITE_PIXEL_FILLING else "⏭️ OFF"
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
config_str = f"Order:{STEP_ORDER} S1:{ENABLE_STEP1_ISOLATED_PIXELS} S2:{ENABLE_STEP2_OUTSIDE_BOXES} S3:{ENABLE_STEP3_COLOR_ANALYSIS} S4:{ENABLE_STEP4_NEIGHBOR_REPLACEMENT} S5:{ENABLE_STEP5_LINE_DETECTION} S6:{ENABLE_STEP6_WHITE_PIXEL_FILLING}"
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
print("✅ 5-Step dynamic ordered color-based prediction completed!")
print(f"🔧 Used step order: {STEP_ORDER}")
print(f"🔧 Used configuration: Step1={ENABLE_STEP1_ISOLATED_PIXELS}, Step2={ENABLE_STEP2_OUTSIDE_BOXES}, Step3={ENABLE_STEP3_COLOR_ANALYSIS}, Step4={ENABLE_STEP4_NEIGHBOR_REPLACEMENT}, Step5={ENABLE_STEP5_LINE_DETECTION}")
print(f"📊 To test different orders, modify STEP_ORDER around line 686!")
print(f"📊 To test different combinations, modify the ENABLE_* variables around line 691!")