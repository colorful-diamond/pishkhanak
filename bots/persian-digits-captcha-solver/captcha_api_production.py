#!/usr/bin/env python3
"""
Persian Digit Captcha Solver - Production API Server
===================================================

Production-ready web API for solving Persian digit captchas.
Optimized for performance, security, and reliability.

UPDATES:
- Fixed 3-level binary image processing (black/gray/white)
- Shadow detection only for neighbor pixels (prevents gray background bug)
- Returns English digits (0-9) instead of Persian digits
- Preserves digit continuity when lines cross digits
"""

import os
import io
import base64
import time
import logging
from datetime import datetime, timezone

import cv2
import keras
import numpy as np
from flask import Flask, request, jsonify, g
from werkzeug.utils import secure_filename
from PIL import Image
import tensorflow as tf
import matplotlib
matplotlib.use('Agg')  # Use Agg backend for server compatibility
import matplotlib.pyplot as plt
from collections import Counter

# We'll use the predict.py logic directly instead of duplicating code

# Suppress TensorFlow warnings
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '2'
tf.get_logger().setLevel('ERROR')

# Configuration
class Config:
    MAX_CONTENT_LENGTH = 16 * 1024 * 1024  # 16MB
    MODEL_PATH = './persian_digit.keras'
    COLOR_TOLERANCE = int(os.environ.get('COLOR_TOLERANCE', 30))
    USE_PATTERN_SHADOW_DETECTION = os.environ.get('USE_PATTERN_SHADOW_DETECTION', 'true').lower() == 'true'
    SIMPLE_MODE = os.environ.get('SIMPLE_MODE', 'false').lower() == 'true'  # For testing
    LOG_LEVEL = os.environ.get('LOG_LEVEL', 'INFO')
    HOST = '127.0.0.1'  # Localhost only for security
    PORT = int(os.environ.get('PORT', 9090))

# Flask app setup
app = Flask(__name__)
app.config['MAX_CONTENT_LENGTH'] = Config.MAX_CONTENT_LENGTH

# Logging setup
logging.basicConfig(
    level=getattr(logging, Config.LOG_LEVEL),
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[
        logging.StreamHandler(),
        logging.FileHandler('captcha_api.log')
    ]
)
logger = logging.getLogger(__name__)

# Global variables
MODEL = None
SERVER_STATS = {
    'start_time': None,
    'prediction_count': 0,
    'total_prediction_time': 0.0,
    'error_count': 0,
    'last_prediction_time': None
}

# English digits mapping (changed from Persian to English as requested)
ENGLISH_DIGITS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']

def convert_numpy_types(obj):
    """Convert numpy types to Python native types for JSON serialization."""
    if isinstance(obj, np.integer):
        return int(obj)
    elif isinstance(obj, np.floating):
        return float(obj)
    elif isinstance(obj, np.ndarray):
        return obj.tolist()
    elif isinstance(obj, (list, tuple)):
        return [convert_numpy_types(item) for item in obj]
    elif isinstance(obj, dict):
        return {key: convert_numpy_types(value) for key, value in obj.items()}
    else:
        return obj

def image_to_base64(image):
    """Convert OpenCV image to base64 string."""
    if len(image.shape) == 3:
        # Color image
        _, buffer = cv2.imencode('.png', image)
    else:
        # Grayscale image
        _, buffer = cv2.imencode('.png', image)
    
    img_base64 = base64.b64encode(buffer).decode('utf-8')
    return img_base64

def create_roi_visualization(image_bgr, roi_positions, predicted_text, confidence_scores):
    """Create visualization image with ROI boxes and predictions."""
    output_image = image_bgr.copy()
    
    for i, ((x, y, w, h), char, confidence) in enumerate(zip(roi_positions, predicted_text, confidence_scores)):
        # Draw bounding box
        cv2.rectangle(output_image, (x, y), (x + w, y + h), (0, 255, 0), 2)
        
        # Add prediction text
        cv2.putText(output_image, char, (x, y-5), 
                   cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)
        
        # Add confidence text
        conf_text = f"{confidence*100:.1f}%"
        cv2.putText(output_image, conf_text, (x, y+h+15), 
                   cv2.FONT_HERSHEY_SIMPLEX, 0.4, (255, 0, 0), 1)
    
    return output_image

def create_processing_steps_visualization(image_bgr, roi_positions, predicted_text, target_colors, digit_images, processed_binary=None):
    """Create a comprehensive visualization showing all processing steps."""
    fig = None
    try:
        fig = plt.figure(figsize=(24, 10))
        
        # Original image
        plt.subplot(2, 7, 1)
        plt.imshow(cv2.cvtColor(image_bgr, cv2.COLOR_BGR2RGB))
        plt.title("1. Original Image", fontsize=10, fontweight='bold')
        plt.axis('off')
        
        # ROI boxes highlighted
        roi_image = image_bgr.copy()
        for i, (x, y, w, h) in enumerate(roi_positions):
            cv2.rectangle(roi_image, (x, y), (x + w, y + h), (0, 255, 0), 2)
            cv2.putText(roi_image, f"Box {i+1}", (x, y-5), 
                       cv2.FONT_HERSHEY_SIMPLEX, 0.4, (0, 255, 0), 1)
        
        plt.subplot(2, 7, 2)
        plt.imshow(cv2.cvtColor(roi_image, cv2.COLOR_BGR2RGB))
        plt.title("2. ROI Boxes", fontsize=10, fontweight='bold')
        plt.axis('off')
        
        # Target colors
        plt.subplot(2, 7, 3)
        color_patches = []
        for i, target_color in enumerate(target_colors):
            if target_color is not None:
                # BGR to RGB conversion
                color_rgb = target_color[::-1]
                patch = np.full((20, 50, 3), color_rgb, dtype=np.uint8)
                color_patches.append(patch)
            else:
                # White patch for empty boxes
                patch = np.full((20, 50, 3), [255, 255, 255], dtype=np.uint8)
                color_patches.append(patch)
        
        if color_patches:
            combined_patch = np.vstack(color_patches)
            plt.imshow(combined_patch)
        plt.title("3. Target Colors", fontsize=10, fontweight='bold')
        plt.axis('off')
        
        # Preprocessed binary image (NEW!)
        if processed_binary is not None:
            plt.subplot(2, 7, 4)
            plt.imshow(processed_binary, cmap='gray')
            plt.title("4. Preprocessed Binary\n(4-Step Pipeline)", fontsize=10, fontweight='bold')
            plt.axis('off')
        
        # Individual processed digits (first 3)
        for i in range(min(3, len(digit_images))):
            plt.subplot(2, 7, 5 + i)
            if digit_images[i].size > 0:
                plt.imshow(digit_images[i], cmap='gray')
                char_prediction = predicted_text[i] if i < len(predicted_text) else '?'
                plt.title(f"Digit {i+1}: '{char_prediction}'", fontsize=9, fontweight='bold')
            plt.axis('off')
        
        # Final result visualization
        result_image = create_roi_visualization(image_bgr, roi_positions, predicted_text, [0.95]*len(predicted_text))
        plt.subplot(2, 7, (8, 14))
        plt.imshow(cv2.cvtColor(result_image, cv2.COLOR_BGR2RGB))
        plt.title(f"Final Result: {''.join(predicted_text)}\n(Complete Preprocessing Pipeline)", fontsize=12, fontweight='bold')
        plt.axis('off')
        
        # Save to bytes buffer
        buf = io.BytesIO()
        plt.tight_layout()
        plt.savefig(buf, format='png', dpi=100, bbox_inches='tight')
        buf.seek(0)
        
        # Convert to base64
        img_base64 = base64.b64encode(buf.getvalue()).decode('utf-8')
        
        return img_base64
        
    except Exception as e:
        logger.error(f"Error creating visualization: {e}")
        return None
    finally:
        # Always close the figure to prevent memory leaks
        if fig is not None:
            plt.close(fig)
        else:
            plt.close('all')  # Fallback to close all figures

def extract_boxes_rgb_values(image_bgr, roi_positions):
    """Extract RGB values from each of the 5 digit boxes individually."""
    height, width = image_bgr.shape[:2]
    
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
    
    return box_colors, box_images

def find_most_repeated_color_in_box(box_colors, exclude_background=True):
    """Find the most repeated color in a single box."""
    
    if not box_colors:
        return None, 0
        
    color_counts = Counter(box_colors)
    
    if exclude_background:
        # Filter out very light colors (likely background)
        filtered_counts = {}
        for color, count in color_counts.items():
            r, g, b = color
            if not (r > 200 and g > 200 and b > 200):  # Not very light
                filtered_counts[color] = count
        
        if filtered_counts:
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
    
    # Warm shadow characteristics
    pixel_brightness = sum(pixel_rgb) / 3
    target_brightness = sum(target_rgb) / 3
    
    # Check for warm tone shift (red/orange increase)
    red_increase = pixel_rgb[0] - target_rgb[0]
    blue_change = pixel_rgb[2] - target_rgb[2]
    
    # Color distance
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
    
    # Cool shadow characteristics
    pixel_brightness = sum(pixel_rgb) / 3
    target_brightness = sum(target_rgb) / 3
    
    # Check for cool tone shift (blue/purple increase)
    red_change = pixel_rgb[0] - target_rgb[0]
    blue_increase = pixel_rgb[2] - target_rgb[2]
    
    # Color distance
    color_distance = np.sqrt(sum((np.array(pixel_rgb) - np.array(target_rgb))**2))
    
    is_brighter_or_blue_shift = pixel_brightness > target_brightness - 15 or blue_increase > 20
    has_cool_shift = blue_increase > red_change  # Blue increases more than red
    within_tolerance = color_distance <= tolerance
    
    return is_brighter_or_blue_shift and has_cool_shift and within_tolerance

def create_color_based_binary_image_for_box(box_image, target_color, tolerance=20, preserve_digit_continuity=True, use_pattern_shadow_detection=True):
    """
    Create a 3-level binary image for a single box - PRODUCTION API VERSION:
    
    FIXED BEHAVIOR:
    - Keep exact target color pixels as BLACK (0) - digit pixels
    - Keep neighbor pixels as BLACK (0) - to preserve digit continuity when lines cross
    - Keep shadow neighbors as GRAY (128) - detected warm/cool shadows preserve line structure
    - Keep other pixels as WHITE (255) - background
    
    Parameters:
    - tolerance: Strictness for exact target color matching
    - preserve_digit_continuity: Enable neighbor preservation for digit continuity
    - use_pattern_shadow_detection: Use intelligent shadow detection
    
    Returns:
    - 3-level grayscale image: 0=target+neighbors, 128=shadow neighbors, 255=background
    """
    if target_color is None:
        # If no target color found, return a white image
        return np.ones_like(box_image[:,:,0]) * 255
    
    # Convert to numpy array for easier processing
    target_color = np.array(target_color)
    height, width = box_image.shape[:2]
    
    # Create a mask for pixels similar to target color (ONLY exact matches)
    target_mask = np.all(np.abs(box_image.astype(int) - target_color.astype(int)) <= tolerance, axis=2)
    
    logger.info(f"API DEBUG: Box shape {box_image.shape}, target color {target_color}, tolerance {tolerance}")
    
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
                        logger.debug(f"API: Warm shadow neighbor at ({x},{y})")
                    elif is_cool_shadow_pixel:
                        # Neighbor + cool shadow = make gray
                        cool_shadows_detected += 1
                        cool_shadow_mask[y, x] = True
                        logger.debug(f"API: Cool shadow neighbor at ({x},{y})")
                    else:
                        # Neighbor but not shadow = make black for continuity
                        enhanced_mask[y, x] = True
                        neighbor_pixels_preserved += 1
                        logger.debug(f"API: Neighbor preserved at ({x},{y})")
                else:
                    # Not a neighbor to any target pixel = make white (ignore shadow patterns)
                    other_pixels += 1
    
    logger.info(f"API Results: Target={target_pixels}, Neighbors={neighbor_pixels_preserved}, Warm shadows={warm_shadows_detected}, Cool shadows={cool_shadows_detected}, Other={other_pixels}")
    
    # Use enhanced mask that includes both target pixels and neighbors
    final_mask = enhanced_mask
    
    # Create 3-level binary image: black (0), gray (128), white (255)
    binary_image = np.ones_like(box_image[:,:,0]) * 255  # Start with white (255)
    
    # Apply masks in order: shadows first, then target+neighbors (to prioritize digits)
    combined_shadow_mask = warm_shadow_mask | cool_shadow_mask
    binary_image[combined_shadow_mask] = 128  # Make shadow neighbors gray (128)
    binary_image[final_mask] = 0  # Make target pixels AND neighbors black (0) - this overrides shadows
    
    total_black_pixels = np.sum(binary_image == 0)
    total_gray_pixels = np.sum(binary_image == 128)
    total_white_pixels = np.sum(binary_image == 255)
    
    logger.info(f"API Final binary: {total_black_pixels} black, {total_gray_pixels} gray, {total_white_pixels} white")
    
    return binary_image

def remove_isolated_pixels(image_bgr):
    """Remove pixels that are surrounded by white on 3+ sides (noise removal)"""
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
    
    # Convert back to BGR for compatibility
    cleaned_bgr = cv2.cvtColor(cleaned_result, cv2.COLOR_GRAY2BGR)
    return cleaned_bgr

def remove_pixels_outside_boxes(image_bgr, roi_positions):
    """Remove all pixels outside the 5 digit boxes by setting them to white."""
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

def apply_neighbor_replacement(binary_image, roi_positions, target_colors):
    """Apply neighbor replacement to improve digit continuity - DISABLED to prevent neighbor conversion"""
    logger.info("Neighbor replacement is DISABLED - returning original binary image")
    return binary_image  # Return original without any neighbor conversion
    
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
    
    return enhanced_binary

def preprocess_image_for_ocr(image_bgr, roi_positions, tolerance=25, use_pattern_shadow_detection=True):
    """
    Complete image preprocessing pipeline matching predict.py exactly:
    1. Remove isolated pixels (noise removal)
    2. Remove pixels outside boxes
    3. Advanced color analysis with shadow detection
    4. Neighbor replacement for digit continuity
    """
    try:
        logger.info("Starting complete image preprocessing pipeline...")
        start_time = time.time()
        
        # Validate inputs
        if image_bgr is None or image_bgr.size == 0:
            raise ValueError("Invalid input image")
        if not roi_positions or len(roi_positions) == 0:
            raise ValueError("Invalid ROI positions")
        
        logger.info(f"Input image shape: {image_bgr.shape}")
        logger.info(f"ROI positions: {roi_positions}")
        
        # Step 1: Remove isolated pixels
        logger.info("Step 1: Removing isolated pixels...")
        step_start = time.time()
        denoised_image = remove_isolated_pixels(image_bgr)
        logger.info(f"Step 1 completed in {time.time() - step_start:.3f}s")
        
        # Step 2: Remove pixels outside boxes
        logger.info("Step 2: Removing pixels outside boxes...")
        step_start = time.time()
        cleaned_image = remove_pixels_outside_boxes(denoised_image, roi_positions)
        logger.info(f"Step 2 completed in {time.time() - step_start:.3f}s")
        
        # Step 3: Extract colors and apply color analysis
        logger.info("Step 3: Advanced color analysis with shadow detection...")
        step_start = time.time()
        
        try:
            box_colors, box_images = extract_boxes_rgb_values(cleaned_image, roi_positions)
            logger.info(f"Extracted {len(box_colors)} box color lists")
        except Exception as e:
            logger.error(f"Error in extract_boxes_rgb_values: {e}")
            raise
        
        target_colors = []
        
        for i, box_color_list in enumerate(box_colors):
            try:
                if len(box_color_list) > 0:
                    target_color, count = find_most_repeated_color_in_box(box_color_list)
                    # Convert numpy uint8 values to Python int for JSON serialization
                    target_color_serializable = [int(c) for c in target_color] if target_color else None
                    target_colors.append(target_color_serializable)
                    logger.info(f"Box {i+1}: Target color BGR{target_color_serializable} (appeared {count} times)")
                else:
                    target_colors.append(None)
                    logger.warning(f"Box {i+1}: No colors found")
            except Exception as e:
                logger.error(f"Error processing box {i+1}: {e}")
                target_colors.append(None)
        
        # Process each box individually with color analysis
        box_binary_images = []
        
        for i, (box_color_list, box_image) in enumerate(zip(box_colors, box_images)):
            try:
                if len(box_color_list) > 0:
                    # Find most repeated color in this specific box
                    target_color, count = find_most_repeated_color_in_box(box_color_list)
                    
                    # Create binary image for this box WITH digit continuity preservation
                    box_binary = create_color_based_binary_image_for_box(
                        box_image, target_color, tolerance, 
                        preserve_digit_continuity=True, 
                        use_pattern_shadow_detection=use_pattern_shadow_detection
                    )
                    box_binary_images.append(box_binary)
                    logger.info(f"Box {i+1} binary processing completed")
                else:
                    box_binary_images.append(np.ones((32, 25), dtype=np.uint8) * 255)  # white box
                    logger.info(f"Box {i+1} - empty, using white box")
            except Exception as e:
                logger.error(f"Error creating binary for box {i+1}: {e}")
                box_binary_images.append(np.ones((32, 25), dtype=np.uint8) * 255)  # fallback white box
        
        logger.info(f"Step 3 completed in {time.time() - step_start:.3f}s")
        
        # Create combined binary image by placing processed boxes back in original positions
        logger.info("Creating combined binary image...")
        step_start = time.time()
        
        try:
            height, width = cleaned_image.shape[:2]
            combined_binary = np.ones((height, width), dtype=np.uint8) * 255  # Start with white
            
            for i, (box_binary, (x, y, w, h)) in enumerate(zip(box_binary_images, roi_positions)):
                try:
                    if box_binary.size > 0:
                        # Place the processed box back in the combined image
                        x1, y1 = max(0, x), max(0, y)
                        x2, y2 = min(width, x + w), min(height, y + h)
                        
                        if x2 > x1 and y2 > y1:
                            # Resize box_binary to match the exact ROI size
                            roi_h, roi_w = y2 - y1, x2 - x1
                            box_binary_resized = cv2.resize(box_binary, (roi_w, roi_h))
                            combined_binary[y1:y2, x1:x2] = box_binary_resized
                            logger.info(f"Placed box {i+1} back in combined image")
                except Exception as e:
                    logger.error(f"Error placing box {i+1} in combined image: {e}")
                    continue
        except Exception as e:
            logger.error(f"Error creating combined binary: {e}")
            # Fallback: create simple binary
            gray = cv2.cvtColor(cleaned_image, cv2.COLOR_BGR2GRAY)
            _, combined_binary = cv2.threshold(gray, 127, 255, cv2.THRESH_BINARY)
        
        logger.info(f"Combined binary creation completed in {time.time() - step_start:.3f}s")
        
        # Step 4: Apply neighbor replacement
        logger.info("Step 4: Applying neighbor replacement...")
        step_start = time.time()
        
        try:
            final_binary = apply_neighbor_replacement(combined_binary, roi_positions, target_colors)
            logger.info(f"Step 4 completed in {time.time() - step_start:.3f}s")
        except Exception as e:
            logger.error(f"Error in neighbor replacement: {e}")
            # Fallback: use combined_binary without neighbor replacement
            final_binary = combined_binary
        
        total_time = time.time() - start_time
        logger.info(f"Complete preprocessing pipeline finished in {total_time:.3f}s")
        
        return final_binary, target_colors
        
    except Exception as e:
        logger.error(f"Critical error in preprocessing pipeline: {e}")
        logger.error(f"Traceback: {e.__traceback__}")
        
        # Emergency fallback: return simple binary conversion
        try:
            gray = cv2.cvtColor(image_bgr, cv2.COLOR_BGR2GRAY)
            _, simple_binary = cv2.threshold(gray, 127, 255, cv2.THRESH_BINARY)
            fallback_colors = [None] * len(roi_positions)
            logger.warning("Using emergency fallback - simple binary conversion")
            return simple_binary, fallback_colors
        except Exception as fallback_error:
            logger.error(f"Even fallback failed: {fallback_error}")
            raise RuntimeError(f"Complete preprocessing failure: {e}")

def load_model():
    """Load the trained model at startup."""
    global MODEL
    
    logger.info("Loading trained model...")
    
    try:
        if not os.path.exists(Config.MODEL_PATH):
            raise FileNotFoundError(f"Model file not found: {Config.MODEL_PATH}")
        
        MODEL = keras.models.load_model(Config.MODEL_PATH)
        logger.info("Model loaded successfully")
        return True
        
    except Exception as e:
        logger.error(f"Failed to load model: {e}")
        return False

def check_local_access():
    """Security check: Only allow local requests."""
    client_ip = request.environ.get('REMOTE_ADDR', '')
    if client_ip not in ['127.0.0.1', '::1', 'localhost']:
        logger.warning(f"Blocked external request from {client_ip}")
        return jsonify({
            'success': False,
            'error': 'Access denied: Server only accepts local requests'
        }), 403
    return None

@app.before_request
def before_request():
    """Log request details and check access."""
    g.start_time = time.time()
    
    # Security check for all endpoints
    security_error = check_local_access()
    if security_error:
        return security_error

@app.after_request
def after_request(response):
    """Log response details."""
    if hasattr(g, 'start_time'):
        duration = time.time() - g.start_time
        logger.info(f"{request.method} {request.path} - {response.status_code} - {duration:.3f}s")
    return response

@app.errorhandler(413)
def request_entity_too_large(error):
    """Handle file too large error."""
    logger.warning("File upload too large")
    return jsonify({
        'success': False,
        'error': 'File too large. Maximum size: 16MB'
    }), 413

@app.errorhandler(500)
def internal_server_error(error):
    """Handle internal server errors."""
    logger.error(f"Internal server error: {error}")
    SERVER_STATS['error_count'] += 1
    return jsonify({
        'success': False,
        'error': 'Internal server error'
    }), 500

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint."""
    uptime = int(time.time() - SERVER_STATS['start_time']) if SERVER_STATS['start_time'] else 0
    
    return jsonify({
        'status': 'healthy',
        'model_loaded': MODEL is not None,
        'uptime_seconds': uptime,
        'prediction_count': SERVER_STATS['prediction_count'],
        'error_count': SERVER_STATS['error_count'],
        'timestamp': datetime.now(timezone.utc).isoformat()
    })

@app.route('/stats', methods=['GET'])
def get_stats():
    """Get detailed server statistics."""
    uptime = int(time.time() - SERVER_STATS['start_time']) if SERVER_STATS['start_time'] else 0
    avg_time = (SERVER_STATS['total_prediction_time'] / SERVER_STATS['prediction_count'] 
                if SERVER_STATS['prediction_count'] > 0 else 0)
    
    return jsonify({
        'total_predictions': SERVER_STATS['prediction_count'],
        'total_prediction_time': round(SERVER_STATS['total_prediction_time'], 3),
        'average_prediction_time': round(avg_time, 3),
        'error_count': SERVER_STATS['error_count'],
        'uptime_seconds': uptime,
        'last_prediction_time': SERVER_STATS['last_prediction_time'],
        'model_loaded': MODEL is not None,
        'configuration': {
            'color_tolerance': Config.COLOR_TOLERANCE,
            'pattern_shadow_detection': Config.USE_PATTERN_SHADOW_DETECTION,
            'max_file_size_mb': Config.MAX_CONTENT_LENGTH // (1024 * 1024)
        },
        'timestamp': datetime.now(timezone.utc).isoformat()
    })

@app.route('/predict', methods=['POST'])
def predict_endpoint():
    """Main prediction endpoint with detailed visualization."""
    if MODEL is None:
        logger.error("Prediction attempted but model not loaded")
        return jsonify({
            'success': False,
            'error': 'Model not loaded'
        }), 500
    
    start_time = time.time()
    
    try:
        # Get image from request
        image_bgr = None
        
        if 'image' in request.files:
            # File upload method
            file = request.files['image']
            if file.filename == '':
                return jsonify({
                    'success': False,
                    'error': 'No image file selected'
                }), 400
            
            # Convert to OpenCV format
            image = Image.open(io.BytesIO(file.read()))
            image_bgr = cv2.cvtColor(np.array(image), cv2.COLOR_RGB2BGR)
            
        elif request.is_json:
            # JSON base64 method
            data = request.get_json()
            if 'image' not in data:
                return jsonify({
                    'success': False,
                    'error': 'No image provided in JSON'
                }), 400
            
            # Decode base64 image
            image_data = base64.b64decode(data['image'])
            image = Image.open(io.BytesIO(image_data))
            image_bgr = cv2.cvtColor(np.array(image), cv2.COLOR_RGB2BGR)
            
        else:
            return jsonify({
                'success': False,
                'error': 'No image provided'
            }), 400
        
        # Fixed ROI positions for 5 Persian digits
        roi_positions = [
            (5, 4, 27, 32),    # Digit 1
            (37, 4, 27, 32),   # Digit 2
            (69, 4, 27, 32),   # Digit 3
            (101, 4, 27, 32),  # Digit 4
            (133, 4, 27, 32),  # Digit 5
        ]
        
        # Check if simple mode is enabled for testing
        if Config.SIMPLE_MODE:
            logger.info("SIMPLE MODE: Using basic grayscale processing only")
            gray = cv2.cvtColor(image_bgr, cv2.COLOR_BGR2GRAY)
            _, processed_binary = cv2.threshold(gray, 127, 255, cv2.THRESH_BINARY)
            target_colors = [None] * len(roi_positions)
        else:
            # Apply complete preprocessing pipeline (matching predict.py exactly)
            logger.info("Applying complete image preprocessing pipeline...")
            tolerance = Config.COLOR_TOLERANCE
            use_pattern_shadow_detection = Config.USE_PATTERN_SHADOW_DETECTION
            
            try:
                # Get preprocessed binary image and target colors
                processed_binary, target_colors = preprocess_image_for_ocr(
                    image_bgr, roi_positions, tolerance, use_pattern_shadow_detection
                )
                logger.info("Preprocessing pipeline completed successfully")
            except Exception as preprocessing_error:
                logger.error(f"Preprocessing pipeline failed: {preprocessing_error}")
                # Emergency fallback: simple grayscale processing
                logger.warning("Using emergency fallback - simple grayscale processing")
                gray = cv2.cvtColor(image_bgr, cv2.COLOR_BGR2GRAY)
                _, processed_binary = cv2.threshold(gray, 127, 255, cv2.THRESH_BINARY)
                target_colors = [None] * len(roi_positions)
        
        # Process each digit and make predictions
        predicted_text = ""
        confidence_scores = []
        digit_images = []
        individual_results = []
        
        for i, (x, y, w, h) in enumerate(roi_positions):
            try:
                # Extract ROI from the preprocessed binary image
                digit_roi = processed_binary[y:y+h, x:x+w]
                
                if digit_roi.size == 0:
                    logger.warning(f"Empty ROI for digit {i+1}")
                    predicted_text += "?"
                    confidence_scores.append(0.0)
                    digit_images.append(np.zeros((40, 30), dtype=np.uint8))
                    individual_results.append({
                        'position': i+1,
                        'roi_coordinates': {'x': x, 'y': y, 'width': w, 'height': h},
                        'target_color': target_colors[i] if i < len(target_colors) else None,
                        'predicted_digit': "?",
                        'confidence': 0.0,
                        'error': "Empty ROI"
                    })
                    continue
                
                # The binary image is already grayscale, so no need to convert
                # Resize to model input size (30x40)
                digit_roi_resized = cv2.resize(digit_roi, (30, 40))
                digit_images.append(digit_roi_resized)
                
                # Prepare for model input (normalize, add batch and channel dimensions)
                processed_digit = digit_roi_resized.astype('float32') / 255.0
                processed_digit = np.expand_dims(processed_digit, axis=-1)  # Add channel dimension
                processed_digit = np.expand_dims(processed_digit, axis=0)   # Add batch dimension
                
                # Make prediction using CNN model
                prediction = MODEL.predict(processed_digit, verbose=0)
                predicted_class_index = np.argmax(prediction, axis=1)[0]
                confidence = np.max(prediction)
                predicted_digit_char = ENGLISH_DIGITS[predicted_class_index]
                
                predicted_text += predicted_digit_char
                confidence_scores.append(float(confidence))
                
                # Store individual result
                individual_results.append({
                    'position': i+1,
                    'roi_coordinates': {'x': x, 'y': y, 'width': w, 'height': h},
                    'target_color': target_colors[i] if i < len(target_colors) else None,
                    'predicted_digit': predicted_digit_char,
                    'confidence': float(confidence),
                    'roi_image_base64': image_to_base64(digit_roi_resized)
                })
                
                logger.info(f"Digit {i+1}: '{predicted_digit_char}' (confidence: {confidence:.3f})")
                
            except Exception as e:
                logger.error(f"Error processing digit {i+1}: {e}")
                predicted_text += "?"
                confidence_scores.append(0.0)
                digit_images.append(np.zeros((40, 30), dtype=np.uint8))
                individual_results.append({
                    'position': i+1,
                    'roi_coordinates': {'x': x, 'y': y, 'width': w, 'height': h},
                    'target_color': target_colors[i] if i < len(target_colors) else None,
                    'predicted_digit': "?",
                    'confidence': 0.0,
                    'error': str(e)
                })
        
        # Create visualizations
        logger.info("Creating visualization images...")
        
        # Original image with ROI boxes
        roi_visualization = create_roi_visualization(image_bgr, roi_positions, predicted_text, confidence_scores)
        roi_image_base64 = image_to_base64(roi_visualization)
        
        # Processing steps visualization (now includes the preprocessed binary)
        processing_steps_base64 = create_processing_steps_visualization(
            image_bgr, roi_positions, predicted_text, target_colors, digit_images, processed_binary
        )
        
        # Original image base64
        original_image_base64 = image_to_base64(image_bgr)
        
        # Preprocessed binary image base64
        preprocessed_binary_base64 = image_to_base64(processed_binary)
        
        # Update statistics
        prediction_time = time.time() - start_time
        SERVER_STATS['prediction_count'] += 1
        SERVER_STATS['total_prediction_time'] += prediction_time
        SERVER_STATS['last_prediction_time'] = datetime.now(timezone.utc).isoformat()
        
        logger.info(f"Prediction successful: {predicted_text} (time: {prediction_time:.3f}s)")
        
        # Prepare comprehensive response
        response_data = {
            'success': True,
            'predicted_text': predicted_text,
            'confidence_scores': confidence_scores,
            'individual_results': individual_results,
            'target_colors': target_colors,
            'roi_positions': [{'x': x, 'y': y, 'width': w, 'height': h} for x, y, w, h in roi_positions],
            'images': {
                'original_image': original_image_base64,
                'preprocessed_binary': preprocessed_binary_base64,
                'roi_visualization': roi_image_base64,
                'processing_steps': processing_steps_base64
            },
            'processing_info': {
                'total_boxes_processed': len(roi_positions),
                'successful_predictions': len([c for c in confidence_scores if c > 0]),
                'average_confidence': sum(confidence_scores) / len(confidence_scores) if confidence_scores else 0
            },
            'prediction_time': round(prediction_time, 3),
            'timestamp': datetime.now(timezone.utc).isoformat()
        }
        
        # Ensure all numpy types are converted to JSON-serializable types
        response_data = convert_numpy_types(response_data)
        
        return jsonify(response_data)
        
    except Exception as e:
        prediction_time = time.time() - start_time
        SERVER_STATS['error_count'] += 1
        
        logger.error(f"Prediction failed: {str(e)}")
        
        return jsonify({
            'success': False,
            'error': str(e),
            'prediction_time': round(prediction_time, 3),
            'timestamp': datetime.now(timezone.utc).isoformat()
        }), 500

def create_app():
    """Application factory function."""
    if not load_model():
        logger.critical("Failed to load model. Cannot start server.")
        raise RuntimeError("Model loading failed")
    
    SERVER_STATS['start_time'] = time.time()
    
    logger.info("Persian Digit Captcha API Server initialized")
    logger.info(f"Configuration: color_tolerance={Config.COLOR_TOLERANCE}, "
                f"pattern_shadows={Config.USE_PATTERN_SHADOW_DETECTION}")
    
    return app

def main():
    """Main function for development server."""
    print("🚀 Persian Digit Captcha Solver - Production API Server")
    print("=" * 55)
    
    # Load model
    if not load_model():
        print("❌ Failed to load model. Exiting...")
        return 1
    
    SERVER_STATS['start_time'] = time.time()
    
    print("✅ Server ready!")
    print(f"🌐 Listening on: http://{Config.HOST}:{Config.PORT} (localhost only)")
    print(f"🔒 Security: Only accepts local requests")
    print(f"📝 API Endpoints:")
    print(f"   POST /predict - Upload image for prediction")
    print(f"   GET /health  - Check server health")
    print(f"   GET /stats   - Get prediction statistics")
    print()
    print("🔧 Configuration:")
    print(f"   Color Tolerance: {Config.COLOR_TOLERANCE}")
    print(f"   Pattern Shadow Detection: {Config.USE_PATTERN_SHADOW_DETECTION}")
    print(f"   Log Level: {Config.LOG_LEVEL}")
    print()
    print("💡 For production, use: gunicorn -c gunicorn.conf.py captcha_api_production:app")
    print("=" * 55)
    
    # Start development server
    app.run(host=Config.HOST, port=Config.PORT, debug=False, threaded=True)
    
    return 0

# For WSGI servers (gunicorn, uWSGI, etc.)
application = create_app()

if __name__ == '__main__':
    exit(main())