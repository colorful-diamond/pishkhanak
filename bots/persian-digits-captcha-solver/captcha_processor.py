#!/usr/bin/env python3
"""
Persian Digit Captcha Processor - Production Module
==================================================

Clean, production-ready module for processing Persian digit captchas.
Contains core image processing and prediction functions.
"""

import cv2
import numpy as np
from collections import Counter

# Configuration constants
DEFAULT_COLOR_TOLERANCE = 30
DEFAULT_ROI_POSITIONS = [
    (5, 6, 25, 32),    # Digit 1
    (37, 6, 25, 32),   # Digit 2
    (69, 6, 25, 32),   # Digit 3
    (101, 6, 25, 32),  # Digit 4
    (133, 6, 25, 32),  # Digit 5
]

def find_most_repeated_color_in_box(box_colors, exclude_background=True):
    """
    Find the most repeated color in a box, optionally excluding background colors.
    
    Args:
        box_colors: List of RGB tuples from the box
        exclude_background: Whether to exclude light colors (background)
    
    Returns:
        tuple: (most_common_color, count)
    """
    if not box_colors:
        return None, 0
    
    # Count color occurrences
    color_counts = Counter(box_colors)
    
    if exclude_background:
        # Filter out very light colors (background)
        filtered_counts = {}
        for color, count in color_counts.items():
            r, g, b = color
            if r < 240 or g < 240 or b < 240:  # Not very light
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
    Detect if a pixel is a warm shadow (left side pattern) based on lighting model.
    
    Args:
        pixel_bgr: BGR pixel values
        target_color_bgr: Target color BGR values  
        tolerance: Color distance tolerance
    
    Returns:
        bool: True if pixel is a warm shadow
    """
    # Convert to float to prevent overflow
    pixel_rgb = np.array([pixel_bgr[2], pixel_bgr[1], pixel_bgr[0]], dtype=np.float32)
    target_rgb = np.array([target_color_bgr[2], target_color_bgr[1], target_color_bgr[0]], dtype=np.float32)
    
    pixel_brightness = np.mean(pixel_rgb)
    target_brightness = np.mean(target_rgb)
    
    red_increase = pixel_rgb[0] - target_rgb[0]
    blue_change = pixel_rgb[2] - target_rgb[2]
    
    color_distance = np.sqrt(np.sum((pixel_rgb - target_rgb)**2))
    
    is_brighter = pixel_brightness > target_brightness - 10
    has_warm_shift = red_increase > blue_change
    within_tolerance = color_distance <= tolerance
    
    return is_brighter and has_warm_shift and within_tolerance

def is_cool_shadow(pixel_bgr, target_color_bgr, tolerance=50):
    """
    Detect if a pixel is a cool shadow (right side pattern) based on lighting model.
    
    Args:
        pixel_bgr: BGR pixel values
        target_color_bgr: Target color BGR values
        tolerance: Color distance tolerance
        
    Returns:
        bool: True if pixel is a cool shadow
    """
    # Convert to float to prevent overflow
    pixel_rgb = np.array([pixel_bgr[2], pixel_bgr[1], pixel_bgr[0]], dtype=np.float32)
    target_rgb = np.array([target_color_bgr[2], target_color_bgr[1], target_color_bgr[0]], dtype=np.float32)
    
    pixel_brightness = np.mean(pixel_rgb)
    target_brightness = np.mean(target_rgb)
    
    red_change = pixel_rgb[0] - target_rgb[0]
    blue_increase = pixel_rgb[2] - target_rgb[2]
    
    color_distance = np.sqrt(np.sum((pixel_rgb - target_rgb)**2))
    
    is_brighter_or_blue_shift = pixel_brightness > target_brightness - 15 or blue_increase > 20
    has_cool_shift = blue_increase > red_change
    within_tolerance = color_distance <= tolerance
    
    return is_brighter_or_blue_shift and has_cool_shift and within_tolerance

def create_color_based_binary_image_for_box(box_image, target_color, tolerance=20, use_pattern_shadow_detection=True):
    """
    Create binary image for a single box with intelligent shadow detection.
    
    Args:
        box_image: Input box image (BGR)
        target_color: Target digit color (BGR)
        tolerance: Color matching tolerance
        use_pattern_shadow_detection: Use intelligent shadow detection
    
    Returns:
        numpy.ndarray: Binary image (grayscale)
    """
    if target_color is None:
        return np.ones_like(box_image[:,:,0]) * 255
    
    target_color = np.array(target_color)
    height, width = box_image.shape[:2]
    
    # Create mask for exact target color
    target_mask = np.all(np.abs(box_image.astype(int) - target_color.astype(int)) <= tolerance, axis=2)
    
    # Neighbor offsets (4-connected)
    neighbor_offsets = [(-1, 0), (1, 0), (0, -1), (0, 1)]
    
    enhanced_mask = target_mask.copy()
    
    if use_pattern_shadow_detection:
        # Use intelligent shadow detection
        for y in range(height):
            for x in range(width):
                if not target_mask[y, x]:  # Not exact target color
                    pixel_bgr = box_image[y, x]
                    
                    # Check shadow patterns
                    is_warm_shadow_pixel = is_warm_shadow(pixel_bgr, target_color)
                    is_cool_shadow_pixel = is_cool_shadow(pixel_bgr, target_color)
                    
                    if not (is_warm_shadow_pixel or is_cool_shadow_pixel):
                        # Not a shadow - check if it neighbors digit pixels
                        has_target_neighbor = False
                        
                        for dy, dx in neighbor_offsets:
                            ny, nx = y + dy, x + dx
                            if (0 <= ny < height and 0 <= nx < width and target_mask[ny, nx]):
                                has_target_neighbor = True
                                break
                        
                        # Convert line pixel to digit color (prevent gaps)
                        if has_target_neighbor:
                            enhanced_mask[y, x] = True
    
    # Create binary image
    binary_image = np.ones_like(box_image[:,:,0]) * 255
    binary_image[enhanced_mask] = 0
    
    return binary_image

def extract_boxes_rgb_values(image_bgr, roi_positions=None):
    """
    Extract RGB values from predefined ROI boxes.
    
    Args:
        image_bgr: Input image (BGR)
        roi_positions: ROI positions [(x, y, w, h), ...] or None for default
    
    Returns:
        tuple: (box_colors, box_images, roi_positions)
    """
    if roi_positions is None:
        roi_positions = DEFAULT_ROI_POSITIONS
    
    box_colors = []
    box_images = []
    
    for x, y, w, h in roi_positions:
        # Extract the box region
        x1, y1 = max(0, x), max(0, y)
        x2, y2 = min(image_bgr.shape[1], x + w), min(image_bgr.shape[0], y + h)
        
        if x2 > x1 and y2 > y1:
            box_image = image_bgr[y1:y2, x1:x2]
            box_images.append(box_image)
            
            # Extract colors from this box
            box_color_list = []
            for row in box_image:
                for pixel in row:
                    box_color_list.append(tuple(pixel))
            
            box_colors.append(box_color_list)
        else:
            box_images.append(np.zeros((h, w, 3), dtype=np.uint8))
            box_colors.append([])
    
    return box_colors, box_images, roi_positions

def process_captcha_image(image_bgr, color_tolerance=DEFAULT_COLOR_TOLERANCE, roi_positions=None, use_pattern_shadow_detection=True):
    """
    Main function to process captcha image using color analysis with intelligent shadow detection.
    
    Args:
        image_bgr: Input captcha image (BGR)
        color_tolerance: Color matching tolerance
        roi_positions: ROI positions or None for default
        use_pattern_shadow_detection: Use intelligent shadow detection
    
    Returns:
        tuple: (binary_image, target_colors, confidence_info)
    """
    if roi_positions is None:
        roi_positions = DEFAULT_ROI_POSITIONS
    
    # Extract colors from boxes
    box_colors, box_images, roi_positions = extract_boxes_rgb_values(image_bgr, roi_positions)
    
    # Process each box
    target_colors = []
    box_binary_images = []
    confidence_info = []
    
    for i, (box_color_list, box_image) in enumerate(zip(box_colors, box_images)):
        if len(box_color_list) > 0:
            # Find target color
            target_color, count = find_most_repeated_color_in_box(box_color_list)
            target_colors.append(target_color)
            
            # Calculate confidence based on color frequency
            total_pixels = len(box_color_list)
            confidence = count / total_pixels if total_pixels > 0 else 0
            confidence_info.append({
                'box_index': i,
                'target_color': target_color,
                'color_frequency': count,
                'total_pixels': total_pixels,
                'confidence': confidence
            })
            
            # Create binary image
            box_binary = create_color_based_binary_image_for_box(
                box_image, target_color, color_tolerance, use_pattern_shadow_detection
            )
            box_binary_images.append(box_binary)
        else:
            target_colors.append(None)
            confidence_info.append({
                'box_index': i,
                'target_color': None,
                'confidence': 0
            })
            box_binary_images.append(np.ones((roi_positions[i][3], roi_positions[i][2]), dtype=np.uint8) * 255)
    
    # Combine binary images
    combined_binary = np.ones((image_bgr.shape[0], image_bgr.shape[1]), dtype=np.uint8) * 255
    
    for i, (box_binary, (x, y, w, h)) in enumerate(zip(box_binary_images, roi_positions)):
        x1, y1 = max(0, x), max(0, y)
        x2, y2 = min(combined_binary.shape[1], x + w), min(combined_binary.shape[0], y + h)
        
        if x2 > x1 and y2 > y1:
            # Resize box_binary to fit the exact region
            target_h, target_w = y2 - y1, x2 - x1
            if box_binary.shape != (target_h, target_w):
                box_binary = cv2.resize(box_binary, (target_w, target_h))
            
            combined_binary[y1:y2, x1:x2] = box_binary
    
    return combined_binary, target_colors, confidence_info

def extract_digit_rois_for_prediction(binary_image, roi_positions=None, target_size=(30, 40)):
    """
    Extract and prepare digit ROIs for model prediction.
    
    Args:
        binary_image: Processed binary image
        roi_positions: ROI positions or None for default
        target_size: Target size for model input
    
    Returns:
        numpy.ndarray: Array of prepared digit images for prediction
    """
    if roi_positions is None:
        roi_positions = DEFAULT_ROI_POSITIONS
    
    digit_images = []
    
    for x, y, w, h in roi_positions:
        x1, y1 = max(0, x), max(0, y)
        x2, y2 = min(binary_image.shape[1], x + w), min(binary_image.shape[0], y + h)
        
        if x2 > x1 and y2 > y1:
            digit_roi = binary_image[y1:y2, x1:x2]
            digit_roi_resized = cv2.resize(digit_roi, target_size)
            digit_roi_normalized = digit_roi_resized.astype('float32') / 255.0
            digit_images.append(digit_roi_normalized)
        else:
            # Create empty image if ROI is invalid
            # Note: cv2.resize takes (width, height) but creates array with (height, width)
            # So we need to create array with shape (target_size[1], target_size[0])
            empty_roi = np.ones((target_size[1], target_size[0]), dtype=np.float32)
            digit_images.append(empty_roi)
    
    # Prepare for model prediction
    digit_images_array = np.array(digit_images)
    # Note: cv2.resize takes (width, height) but creates array with (height, width)
    # target_size is (width, height) = (30, 40), so resized array is (40, 30)
    digit_images_array = digit_images_array.reshape(-1, target_size[1], target_size[0], 1)
    
    return digit_images_array