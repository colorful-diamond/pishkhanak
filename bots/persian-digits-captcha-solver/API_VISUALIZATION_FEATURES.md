# Enhanced Captcha API - Visualization Features

## 🎯 Overview

The Captcha API has been enhanced to provide comprehensive visualization and detailed processing information, similar to the standalone predict script. Now when you make an API request, you get not just the predicted text, but also detailed analysis, ROI information, and visualization images.

## 🖼️ New Response Structure

### Basic Response (unchanged)
```json
{
  "success": true,
  "predicted_text": "۱۲۳۴۵",
  "confidence_scores": [0.98, 0.95, 0.97, 0.92, 0.89],
  "prediction_time": 0.234,
  "timestamp": "2024-01-15T10:30:45Z"
}
```

### Enhanced Response (NEW!)
```json
{
  "success": true,
  "predicted_text": "۱۲۳۴۵",
  "confidence_scores": [0.98, 0.95, 0.97, 0.92, 0.89],
  "individual_results": [
    {
      "position": 1,
      "roi_coordinates": {"x": 5, "y": 4, "width": 25, "height": 32},
      "target_color": [45, 78, 123],
      "predicted_digit": "۱",
      "confidence": 0.98,
      "roi_image_base64": "iVBORw0KGgoAAAANSUhEUgAA..."
    }
    // ... 4 more digits
  ],
  "target_colors": [[45, 78, 123], [67, 89, 134], null, [89, 123, 156], [34, 67, 98]],
  "roi_positions": [
    {"x": 5, "y": 4, "width": 25, "height": 32},
    {"x": 37, "y": 4, "width": 25, "height": 32}
    // ... 3 more boxes
  ],
  "images": {
    "original_image": "data:image/png;base64,iVBORw0KGgoAAAANS...",
    "roi_visualization": "data:image/png;base64,iVBORw0KGgoAAAANS...",
    "processing_steps": "data:image/png;base64,iVBORw0KGgoAAAANS..."
  },
  "processing_info": {
    "total_boxes_processed": 5,
    "successful_predictions": 5,
    "average_confidence": 0.942
  },
  "prediction_time": 0.456,
  "timestamp": "2024-01-15T10:30:45Z"
}
```

## 📊 New Features

### 1. Individual Digit Analysis
Each digit now has detailed information:
- **Position**: Digit number (1-5)
- **ROI Coordinates**: Exact pixel coordinates of the digit box
- **Target Color**: The dominant color detected in that box (BGR format)
- **Predicted Digit**: The predicted Persian digit
- **Confidence**: Model confidence for this specific digit
- **ROI Image**: Base64 encoded image of the processed digit ROI

### 2. Target Color Detection
The API now analyzes each digit box individually to find:
- The most common color in each box (excluding background)
- Filters out very light colors (background noise)
- Returns BGR color values for further analysis

### 3. Visualization Images

#### Original Image (`original_image`)
- The input image exactly as received by the API

#### ROI Visualization (`roi_visualization`)
- Original image with green bounding boxes around each digit
- Predicted digit displayed above each box
- Confidence percentage shown below each box

#### Processing Steps (`processing_steps`)
- Comprehensive visualization showing:
  - Original image
  - ROI boxes highlighted
  - Target colors for each box
  - Individual processed digit ROIs
  - Final result with predictions

### 4. Processing Statistics
- Total boxes processed
- Number of successful predictions
- Average confidence across all digits
- Detailed timing information

## 🚀 Usage Examples

### Python Example
```python
import requests
import base64
import json
from PIL import Image
import io

# Prepare image
with open("captcha.png", "rb") as f:
    image_data = f.read()
    image_base64 = base64.b64encode(image_data).decode('utf-8')

# Make request
response = requests.post("http://127.0.0.1:9090/predict", json={"image": image_base64})
result = response.json()

if result['success']:
    print(f"Predicted: {result['predicted_text']}")
    
    # Save visualization images
    for image_type, image_b64 in result['images'].items():
        if image_b64:
            image_data = base64.b64decode(image_b64)
            with open(f"result_{image_type}.png", "wb") as f:
                f.write(image_data)
    
    # Analyze individual digits
    for digit_result in result['individual_results']:
        print(f"Digit {digit_result['position']}: {digit_result['predicted_digit']} "
              f"(confidence: {digit_result['confidence']:.3f})")
        print(f"  Target color: BGR{digit_result['target_color']}")
        print(f"  ROI: {digit_result['roi_coordinates']}")
```

### JavaScript Example
```javascript
// Send image to API
const response = await fetch('http://127.0.0.1:9090/predict', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ image: imageBase64 })
});

const result = await response.json();

if (result.success) {
    console.log('Predicted:', result.predicted_text);
    
    // Display visualization images
    document.getElementById('original').src = `data:image/png;base64,${result.images.original_image}`;
    document.getElementById('roi_viz').src = `data:image/png;base64,${result.images.roi_visualization}`;
    document.getElementById('steps').src = `data:image/png;base64,${result.images.processing_steps}`;
    
    // Show individual results
    result.individual_results.forEach(digit => {
        console.log(`Digit ${digit.position}: ${digit.predicted_digit} (${(digit.confidence * 100).toFixed(1)}%)`);
    });
}
```

## 🔧 API Endpoints

### POST `/predict`
Enhanced prediction endpoint with visualization

**Request:**
- File upload: `multipart/form-data` with `image` field
- JSON: `{"image": "base64_encoded_image"}`

**Response:** Enhanced JSON with visualization data (see structure above)

### GET `/health`
Server health check (unchanged)

### GET `/stats`
Server statistics (unchanged)

## 📈 Performance Impact

The new visualization features add minimal overhead:
- **Processing time**: +0.1-0.2 seconds for visualization generation
- **Response size**: Larger due to base64 images (~500KB-2MB depending on image complexity)
- **Memory usage**: Temporary increase during matplotlib processing

## 🎨 Visualization Details

### ROI Boxes
- **Color**: Green (#00FF00)
- **Thickness**: 2 pixels
- **Text**: Predicted digit above box, confidence below

### Processing Steps Visualization
- **Format**: Multi-panel figure (2x6 grid)
- **Panels**: Original, ROI boxes, target colors, individual digits, final result
- **Resolution**: 100 DPI for balance between quality and size

### Target Colors
- **Display**: Vertical color patches showing each box's dominant color
- **Size**: 20x50 pixels per color patch
- **Order**: Box 1 to Box 5, top to bottom

## 🐛 Error Handling

Individual digit processing errors are captured and reported:
```json
{
  "position": 3,
  "roi_coordinates": {"x": 69, "y": 4, "width": 25, "height": 32},
  "target_color": null,
  "predicted_digit": "?",
  "confidence": 0.0,
  "error": "Empty ROI"
}
```

Common error types:
- "Empty ROI" - ROI extraction failed
- "Model prediction failed" - CNN model error
- "Color detection failed" - Target color analysis error

## 🔧 Testing

Use the provided test script:
```bash
python test_api_visualization.py
```

This script demonstrates all new features and saves visualization images for inspection.

## 📝 Backward Compatibility

✅ **Fully backward compatible** - existing clients will continue to work
✅ **Optional features** - new data is additional, core response unchanged
✅ **Same endpoints** - no API endpoint changes required

The enhanced API maintains full compatibility with existing clients while providing rich visualization capabilities for clients that can utilize them.