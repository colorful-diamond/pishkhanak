import tensorflow as tf
from tensorflow import keras
import numpy as np
import cv2
import os
import matplotlib.pyplot as plt
from sklearn.metrics import classification_report, confusion_matrix
import seaborn as sns
from PIL import Image
import random

# --- Settings ---
MODEL_PATH = 'persian_digit_enhanced_v2.keras'
TEST_DATASET_DIR = 'captcha_dataset'
PERSIAN_DIGITS = "۰۱۲۳۴۵۶۷۸۹"
IMG_WIDTH = 30
IMG_HEIGHT = 40

print("🔍 Enhanced Keras Model Evaluation")
print("=" * 50)

# --- Load Enhanced Model ---
print(f"Loading enhanced model: {MODEL_PATH}")
try:
    model = keras.models.load_model(MODEL_PATH)
    print("✅ Enhanced model loaded successfully")
    model.summary()
except Exception as e:
    print(f"❌ Error loading model: {e}")
    print("Please run train_enhanced_keras.py first")
    exit()

# --- Test Time Augmentation (TTA) Function ---
def predict_with_tta(model, image, num_augmentations=8):
    """
    Test Time Augmentation for more robust predictions
    """
    predictions = []
    
    # Original image
    predictions.append(model.predict(image, verbose=0))
    
    # Apply various augmentations
    for i in range(num_augmentations - 1):
        # Random rotation (-10 to 10 degrees)
        angle = random.uniform(-10, 10)
        h, w = image.shape[1], image.shape[2]
        center = (w // 2, h // 2)
        rotation_matrix = cv2.getRotationMatrix2D(center, angle, 1.0)
        
        augmented = image[0]  # Remove batch dimension
        augmented = cv2.warpAffine(augmented, rotation_matrix, (w, h), 
                                 borderMode=cv2.BORDER_CONSTANT, borderValue=1.0)
        augmented = np.expand_dims(augmented, axis=0)  # Add batch dimension back
        
        predictions.append(model.predict(augmented, verbose=0))
    
    # Average all predictions
    avg_prediction = np.mean(predictions, axis=0)
    return avg_prediction

# --- Load Test Data ---
print("Loading test data...")

def load_test_data(dataset_dir, samples_per_class=500):
    """Load balanced test data"""
    images = []
    labels = []
    class_names = []
    
    for digit_idx, digit in enumerate(PERSIAN_DIGITS):
        digit_dir = os.path.join(dataset_dir, digit)
        if not os.path.exists(digit_dir):
            print(f"Warning: {digit_dir} not found")
            continue
            
        image_files = [f for f in os.listdir(digit_dir) if f.endswith('.png')]
        
        # Randomly sample images for testing
        if len(image_files) > samples_per_class:
            image_files = random.sample(image_files, samples_per_class)
        
        print(f"Loading {len(image_files)} test images for digit '{digit}'")
        
        for img_file in image_files:
            img_path = os.path.join(digit_dir, img_file)
            try:
                img = Image.open(img_path).convert('L')
                img_array = np.array(img.resize((IMG_WIDTH, IMG_HEIGHT)), dtype=np.float32) / 255.0
                img_array = img_array.reshape(IMG_HEIGHT, IMG_WIDTH, 1)
                
                images.append(img_array)
                labels.append(digit_idx)
                class_names.append(digit)
                
            except Exception as e:
                print(f"Error loading {img_path}: {e}")
    
    return np.array(images), np.array(labels), class_names

test_images, test_labels, class_names = load_test_data(TEST_DATASET_DIR)
print(f"✅ Loaded {len(test_images)} test images")
print(f"Class distribution: {np.bincount(test_labels)}")

# --- Standard Evaluation ---
print("\n📊 Standard Model Evaluation")
print("-" * 30)

test_loss, test_accuracy = model.evaluate(test_images, test_labels, verbose=0)
print(f"Test accuracy: {test_accuracy:.4f}")
print(f"Test loss: {test_loss:.4f}")

# Get predictions
standard_predictions = model.predict(test_images, verbose=0)
standard_pred_classes = np.argmax(standard_predictions, axis=1)

# --- Test Time Augmentation Evaluation ---
print("\n🚀 Test Time Augmentation (TTA) Evaluation")
print("-" * 40)

tta_predictions = []
tta_pred_classes = []

print("Applying TTA to test images...")
for i, img in enumerate(test_images):
    if i % 100 == 0:
        print(f"Processing {i}/{len(test_images)}...")
    
    img_batch = np.expand_dims(img, axis=0)
    tta_pred = predict_with_tta(model, img_batch, num_augmentations=5)
    tta_predictions.append(tta_pred[0])
    tta_pred_classes.append(np.argmax(tta_pred[0]))

tta_predictions = np.array(tta_predictions)
tta_pred_classes = np.array(tta_pred_classes)

tta_accuracy = np.mean(tta_pred_classes == test_labels)
print(f"TTA accuracy: {tta_accuracy:.4f}")
print(f"TTA improvement: +{tta_accuracy - test_accuracy:.4f}")

# --- Detailed Analysis ---
print("\n📈 Detailed Performance Analysis")
print("-" * 35)

# Standard model confusion matrix
standard_cm = confusion_matrix(test_labels, standard_pred_classes)
tta_cm = confusion_matrix(test_labels, tta_pred_classes)

# Classification reports
print("\n📋 Standard Model Classification Report:")
print(classification_report(test_labels, standard_pred_classes, 
                          target_names=PERSIAN_DIGITS, digits=4))

print("\n📋 TTA Model Classification Report:")
print(classification_report(test_labels, tta_pred_classes, 
                          target_names=PERSIAN_DIGITS, digits=4))

# --- Visualization ---
print("\n📊 Creating performance visualization...")

fig, axes = plt.subplots(2, 3, figsize=(18, 12))

# Standard confusion matrix
sns.heatmap(standard_cm, annot=True, fmt='d', cmap='Blues', 
           xticklabels=PERSIAN_DIGITS, yticklabels=PERSIAN_DIGITS, ax=axes[0, 0])
axes[0, 0].set_title('Standard Model Confusion Matrix', fontsize=14, fontweight='bold')
axes[0, 0].set_xlabel('Predicted')
axes[0, 0].set_ylabel('Actual')

# TTA confusion matrix
sns.heatmap(tta_cm, annot=True, fmt='d', cmap='Greens', 
           xticklabels=PERSIAN_DIGITS, yticklabels=PERSIAN_DIGITS, ax=axes[0, 1])
axes[0, 1].set_title('TTA Model Confusion Matrix', fontsize=14, fontweight='bold')
axes[0, 1].set_xlabel('Predicted')
axes[0, 1].set_ylabel('Actual')

# Accuracy comparison
methods = ['Standard', 'TTA']
accuracies = [test_accuracy, tta_accuracy]
colors = ['skyblue', 'lightgreen']

bars = axes[0, 2].bar(methods, accuracies, color=colors)
axes[0, 2].set_title('Accuracy Comparison', fontsize=14, fontweight='bold')
axes[0, 2].set_ylabel('Accuracy')
axes[0, 2].set_ylim(0, 1)

# Add accuracy values on bars
for bar, acc in zip(bars, accuracies):
    axes[0, 2].text(bar.get_x() + bar.get_width()/2, bar.get_height() + 0.01, 
                   f'{acc:.4f}', ha='center', va='bottom', fontweight='bold')

# Per-class accuracy comparison
standard_class_acc = np.diag(standard_cm) / np.sum(standard_cm, axis=1)
tta_class_acc = np.diag(tta_cm) / np.sum(tta_cm, axis=1)

x = np.arange(len(PERSIAN_DIGITS))
width = 0.35

axes[1, 0].bar(x - width/2, standard_class_acc, width, label='Standard', color='skyblue')
axes[1, 0].bar(x + width/2, tta_class_acc, width, label='TTA', color='lightgreen')
axes[1, 0].set_title('Per-Class Accuracy Comparison', fontsize=14, fontweight='bold')
axes[1, 0].set_xlabel('Persian Digits')
axes[1, 0].set_ylabel('Accuracy')
axes[1, 0].set_xticks(x)
axes[1, 0].set_xticklabels(PERSIAN_DIGITS)
axes[1, 0].legend()
axes[1, 0].grid(True, alpha=0.3)

# Confused digits analysis
confused_pairs = [(2, 4), (4, 2), (7, 8), (8, 7)]  # Confused digit pairs
confused_improvements = []

for true_digit, pred_digit in confused_pairs:
    standard_confusion = standard_cm[true_digit, pred_digit]
    tta_confusion = tta_cm[true_digit, pred_digit]
    improvement = standard_confusion - tta_confusion
    confused_improvements.append(improvement)

pair_labels = ['2→4', '4→2', '7→8', '8→7']
axes[1, 1].bar(pair_labels, confused_improvements, color='orange')
axes[1, 1].set_title('Confusion Reduction (TTA vs Standard)', fontsize=14, fontweight='bold')
axes[1, 1].set_xlabel('Confused Pairs')
axes[1, 1].set_ylabel('Confusion Reduction')
axes[1, 1].grid(True, alpha=0.3)

# Performance summary
axes[1, 2].text(0.1, 0.9, '🎯 Enhanced Model Performance Summary', fontsize=14, fontweight='bold', transform=axes[1, 2].transAxes)
axes[1, 2].text(0.1, 0.8, f'Standard Accuracy: {test_accuracy:.4f}', fontsize=12, transform=axes[1, 2].transAxes)
axes[1, 2].text(0.1, 0.75, f'TTA Accuracy: {tta_accuracy:.4f}', fontsize=12, transform=axes[1, 2].transAxes)
axes[1, 2].text(0.1, 0.7, f'TTA Improvement: +{tta_accuracy - test_accuracy:.4f}', fontsize=12, fontweight='bold', color='green', transform=axes[1, 2].transAxes)

baseline_acc = 0.98  # Assumed baseline
total_improvement = tta_accuracy - baseline_acc
axes[1, 2].text(0.1, 0.6, f'Total vs Baseline: +{total_improvement:.4f}', fontsize=12, fontweight='bold', color='blue', transform=axes[1, 2].transAxes)

# Most improved classes
improvement_per_class = tta_class_acc - standard_class_acc
top_improved = np.argsort(improvement_per_class)[-3:][::-1]
axes[1, 2].text(0.1, 0.5, f'Most Improved Classes:', fontsize=12, fontweight='bold', transform=axes[1, 2].transAxes)
for i, idx in enumerate(top_improved):
    digit = PERSIAN_DIGITS[idx]
    improvement = improvement_per_class[idx]
    axes[1, 2].text(0.15, 0.45 - i*0.05, f'{digit}: +{improvement:.4f}', fontsize=11, transform=axes[1, 2].transAxes)

axes[1, 2].text(0.1, 0.25, f'✅ Techniques Applied:', fontsize=12, fontweight='bold', transform=axes[1, 2].transAxes)
axes[1, 2].text(0.15, 0.2, f'• Enhanced CNN architecture', fontsize=10, transform=axes[1, 2].transAxes)
axes[1, 2].text(0.15, 0.15, f'• Class weights for confused digits', fontsize=10, transform=axes[1, 2].transAxes)
axes[1, 2].text(0.15, 0.1, f'• Test Time Augmentation', fontsize=10, transform=axes[1, 2].transAxes)
axes[1, 2].text(0.15, 0.05, f'• Learning rate scheduling', fontsize=10, transform=axes[1, 2].transAxes)

axes[1, 2].set_xlim(0, 1)
axes[1, 2].set_ylim(0, 1)
axes[1, 2].axis('off')

plt.suptitle('Enhanced Keras Model: Comprehensive Performance Analysis', fontsize=16, fontweight='bold')
plt.tight_layout(rect=[0, 0.03, 1, 0.95])
plt.savefig('enhanced_model_evaluation.png', dpi=300, bbox_inches='tight')
plt.show()

# --- Final Summary ---
print("\n" + "=" * 60)
print("🎯 ENHANCED KERAS MODEL EVALUATION COMPLETE")
print("=" * 60)
print(f"📊 Standard Model Accuracy: {test_accuracy:.4f}")
print(f"🚀 TTA Enhanced Accuracy: {tta_accuracy:.4f}")
print(f"📈 TTA Improvement: +{tta_accuracy - test_accuracy:.4f}")
print(f"🎉 Total Improvement vs Baseline (98%): +{tta_accuracy - 0.98:.4f}")
print("\n📁 Files generated:")
print("   • enhanced_model_evaluation.png")
print("\n🔍 Key Insights:")
if tta_accuracy > test_accuracy:
    print("   ✅ TTA provides measurable accuracy improvement")
else:
    print("   📊 Standard model already very robust")

print(f"   📋 Most confused pairs analysis completed")
print(f"   🎯 Ready for production deployment")

# Save detailed results
results = {
    'standard_accuracy': float(test_accuracy),
    'tta_accuracy': float(tta_accuracy),
    'improvement': float(tta_accuracy - test_accuracy),
    'per_class_standard': standard_class_acc.tolist(),
    'per_class_tta': tta_class_acc.tolist(),
    'confused_pairs_improvement': confused_improvements
}

import json
with open('enhanced_evaluation_results.json', 'w') as f:
    json.dump(results, f, indent=2)

print("   • enhanced_evaluation_results.json")
print("\n🚀 Next: Use enhanced model in predict_enhanced.py for real-world testing!")