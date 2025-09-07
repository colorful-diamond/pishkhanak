import os
import random
import cv2
import numpy as np
from PIL import Image, ImageDraw, ImageFont

# Enhanced Configuration
DIGITS = "۰۱۲۳۴۵۶۷۸۹"
FONT_PATHS = [
    "fonts/ARIALBD.TTF",
]
FONT_SIZE = 36
IMAGE_SIZE = (30, 40)
SAMPLES_PER_CLASS = 12000  # Increased for better diversity
OUTPUT_DIR = "captcha_dataset"

# Enhanced dataset will be saved to separate directory
# to preserve your original dataset for comparison

# check if the output directory exists, if not create it
os.makedirs(OUTPUT_DIR, exist_ok=True)
for digit in DIGITS:
    os.makedirs(os.path.join(OUTPUT_DIR, digit), exist_ok=True)

print("🚀 Generating Hey Linux Processed Dataset")
print("=" * 50)
print(f"📁 Output: {OUTPUT_DIR}")
print(f"🔧 Preprocessing: Hey Linux (user's manual adjustments)")
print(f"   - First threshold: 225")  
print(f"   - Blur intensity: 1/10")
print(f"   - Final threshold: 210")
print("=" * 50)

# Load available fonts
available_fonts = []
for font_path in FONT_PATHS:
    if os.path.exists(font_path):
        available_fonts.append(font_path)
    else:
        print(f"Failded to load font: {font_path}")

if not available_fonts:
    print("No fonts available. Please check the font paths.")
    exit()

def noise_removal_preprocessing(image_bgr):
    
    # Convert to grayscale first
    gray = cv2.cvtColor(image_bgr, cv2.COLOR_BGR2GRAY)
    
    # ONLY Step 1: Check 3+ sides surrounded by white (3x3 neighborhood)
    cleaned_result = gray.copy()
    height, width = gray.shape
    
    for y in range(1, height-1):
        for x in range(1, width-1):
            current_pixel = gray[y, x]
            
            # Check 8-connected neighbors
            neighbors = [
                gray[y-1, x-1], gray[y-1, x], gray[y-1, x+1],  # Top row
                gray[y, x-1],                  gray[y, x+1],    # Middle row (skip center)
                gray[y+1, x-1], gray[y+1, x], gray[y+1, x+1]   # Bottom row
            ]
            
            # Count white neighbors (background)
            white_neighbors = sum(1 for n in neighbors if n > 200)
            
            # If surrounded by white on 3+ sides (out of 8), make it white
            if white_neighbors >= 3:
                cleaned_result[y, x] = 255  # Make it white
    
    # Convert back to BGR for compatibility with Hey Linux preprocessing
    cleaned_bgr = cv2.cvtColor(cleaned_result, cv2.COLOR_GRAY2BGR)
    
    return Image.fromarray(cleaned_bgr)

def simple_grayscale_conversion(pil_image):
    """
    Simple grayscale conversion - no additional preprocessing
    Just convert to grayscale for training
    """
    # Convert to grayscale
    if pil_image.mode != 'L':
        pil_image = pil_image.convert('L')
    
    return pil_image




# Simple dataset generation with neighbor color checking algorithm
print("Starting simple dataset generation...")
print(f"Generating {SAMPLES_PER_CLASS} samples per digit...")

for digit_idx, digit in enumerate(DIGITS):
    print(f"Processing digit '{digit}' ({digit_idx + 1}/{len(DIGITS)})...")
    
    for i in range(SAMPLES_PER_CLASS):
        # Create background with slight variation
        bg_color = random.randint(250, 255)  # Slight background variation
        bg = Image.new("RGB", IMAGE_SIZE, color=(bg_color, bg_color, bg_color))

        random_font_path = random.choice(available_fonts)
        try:
            # Slight font size variation
            font_size = random.randint(FONT_SIZE - 3, FONT_SIZE + 3)
            font = ImageFont.truetype(random_font_path, font_size)
        except IOError:
            font = ImageFont.truetype(available_fonts[0], FONT_SIZE)

        draw = ImageDraw.Draw(bg)
        # Enhanced text color and thickness variation for better digit distinction
        text_color = random.randint(0, 40)  # Slightly more variation
        text_color = (text_color, text_color, text_color)
        
        # Special handling for confused digits (4, 2, 7, 8) - add stroke variations
        stroke_width = 0
        if digit in ['۴', '۲', '۷', '۸']:  # Confused digits get stroke variation
            if random.random() < 0.4:  # 40% chance of stroke
                stroke_width = random.randint(1, 2)
                stroke_color = random.randint(0, 60)
                stroke_color = (stroke_color, stroke_color, stroke_color)

        # Center the text with slight random offset
        try:
            bbox = draw.textbbox((0, 0), digit, font=font)
            text_w, text_h = bbox[2] - bbox[0], bbox[3] - bbox[1]
            
            # Add small random offset to center position
            offset_x = random.randint(-2, 2)
            offset_y = random.randint(-2, 2)
            
            text_x = (IMAGE_SIZE[0] - text_w) // 2 - bbox[0] + offset_x
            text_y = (IMAGE_SIZE[1] - text_h) // 2 - bbox[1] + offset_y
        except AttributeError:
            continue

        # Draw text with enhanced features for confused digits
        if stroke_width > 0 and digit in ['۴', '۲', '۷', '۸']:
            draw.text((text_x, text_y), digit, font=font, fill=text_color, 
                     stroke_width=stroke_width, stroke_fill=stroke_color)
        else:
            draw.text((text_x, text_y), digit, font=font, fill=text_color)

        # Enhanced rotation for confused digits to create more distinguishing features
        if digit in ['۴', '۲', '۷', '۸']:
            # More varied rotation for confused digits
            angle = random.uniform(-30, 30)  # Extra rotation range
        else:
            # Standard rotation for other digits
            angle = random.uniform(-25, 25)
        bg = bg.rotate(
            angle,
            resample=Image.BICUBIC,
            expand=False,
            fillcolor=(bg_color, bg_color, bg_color))

        # Convert to grayscale and apply simple noise removal algorithm
        bg = bg.convert("L")
        
        # Convert back to BGR format for noise removal algorithm
        np_image = np.array(bg)
        bgr_image = cv2.cvtColor(np_image, cv2.COLOR_GRAY2BGR)
        
        # Apply simple noise removal algorithm (neighbor color checking)
        processed_bg = noise_removal_preprocessing(bgr_image)

        # Save the processed image
        filename = os.path.join(OUTPUT_DIR, digit, f"{digit}_{i:04}.png")
        processed_bg.save(filename)
        
        # Progress indicator
        if (i + 1) % 1000 == 0:
            print(f"  Generated {i + 1}/{SAMPLES_PER_CLASS} samples for '{digit}'")

print(f"Simple dataset generation complete!")
print(f"Features applied:")
print("✅ Simple noise removal algorithm (neighbor color checking)")
print("✅ Font size and color variations")
print("✅ Realistic rotation range")
print("✅ No artificial noise - clean training data")

print(
    f"Total samples: {SAMPLES_PER_CLASS * len(DIGITS)} in '{OUTPUT_DIR}' generated."
)
