#!/usr/bin/env python3
"""
Persian Digit Captcha API - Production Test Client
=================================================

Simple test client for the production PM2-managed API server.
"""

import requests
import sys
import time
import glob
import os

API_URL = "http://localhost:9090"

def test_health():
    """Test the health endpoint"""
    print("🔍 Testing health endpoint...")
    try:
        response = requests.get(f"{API_URL}/health", timeout=5)
        if response.status_code == 200:
            data = response.json()
            print(f"✅ Server is healthy!")
            print(f"   Model loaded: {data['model_loaded']}")
            print(f"   Uptime: {data['uptime_seconds']} seconds")
            print(f"   Predictions made: {data['prediction_count']}")
            return True
        else:
            print(f"❌ Health check failed: {response.status_code}")
            return False
    except Exception as e:
        print(f"❌ Cannot connect to server: {e}")
        print("   Make sure the server is running: ./start_production.sh start")
        return False

def test_prediction(image_path):
    """Test prediction with an image file"""
    if not os.path.exists(image_path):
        print(f"❌ Image file not found: {image_path}")
        return None
    
    print(f"🖼️ Testing prediction with: {image_path}")
    
    try:
        with open(image_path, 'rb') as f:
            files = {'image': f}
            start_time = time.time()
            
            response = requests.post(f"{API_URL}/predict", files=files, timeout=30)
            
            request_time = time.time() - start_time
            
        if response.status_code == 200:
            data = response.json()
            if data['success']:
                print(f"✅ Prediction successful!")
                print(f"   Predicted text: {data['predicted_text']}")
                print(f"   Predicted digits: {data['predicted_digits']}")
                print(f"   Prediction time: {data['prediction_time']}s")
                print(f"   Total request time: {request_time:.3f}s")
                print(f"   Confidence scores: {[f'{score:.3f}' for score in data['confidence_scores']]}")
                return data
            else:
                print(f"❌ Prediction failed: {data['error']}")
        else:
            print(f"❌ Request failed: {response.status_code}")
            if response.text:
                print(f"   Response: {response.text}")
                
    except Exception as e:
        print(f"❌ Error during prediction: {e}")
    
    return None

def test_stats():
    """Test the stats endpoint"""
    print("📊 Getting server statistics...")
    try:
        response = requests.get(f"{API_URL}/stats", timeout=5)
        if response.status_code == 200:
            data = response.json()
            print(f"   Total predictions: {data['total_predictions']}")
            print(f"   Average prediction time: {data['average_prediction_time']}s")
            print(f"   Error count: {data['error_count']}")
            print(f"   Configuration: {data['configuration']}")
            return True
        else:
            print(f"❌ Stats request failed: {response.status_code}")
    except Exception as e:
        print(f"❌ Error getting stats: {e}")
    return False

def benchmark_predictions(image_path, num_requests=5):
    """Run a simple benchmark"""
    if not os.path.exists(image_path):
        print(f"❌ Image file not found: {image_path}")
        return
    
    print(f"⚡ Running benchmark with {num_requests} requests...")
    
    times = []
    successes = 0
    
    for i in range(num_requests):
        print(f"   Request {i+1}/{num_requests}...", end=" ")
        
        try:
            with open(image_path, 'rb') as f:
                files = {'image': f}
                start_time = time.time()
                response = requests.post(f"{API_URL}/predict", files=files, timeout=30)
                request_time = time.time() - start_time
                times.append(request_time)
                
            if response.status_code == 200:
                data = response.json()
                if data['success']:
                    print(f"✅ {request_time:.3f}s - {data['predicted_text']}")
                    successes += 1
                else:
                    print(f"❌ Failed: {data['error']}")
            else:
                print(f"❌ HTTP {response.status_code}")
                
        except Exception as e:
            print(f"❌ Error: {e}")
    
    if times:
        avg_time = sum(times) / len(times)
        min_time = min(times)
        max_time = max(times)
        
        print(f"\n📈 Benchmark Results:")
        print(f"   Successful predictions: {successes}/{num_requests}")
        print(f"   Average time: {avg_time:.3f}s")
        print(f"   Min time: {min_time:.3f}s")
        print(f"   Max time: {max_time:.3f}s")
        if avg_time > 0:
            print(f"   Predictions per second: {1/avg_time:.1f}")

def main():
    """Main test function"""
    print("🧪 Persian Digit Captcha API - Production Test")
    print("=" * 50)
    
    # Test health first
    if not test_health():
        return 1
    
    print()
    
    # Get image path from command line or find one
    image_path = None
    if len(sys.argv) > 1:
        image_path = sys.argv[1]
    else:
        # Try to find a test image
        test_images = glob.glob("test/*.png")
        if test_images:
            image_path = test_images[0]
            print(f"💡 Using test image: {image_path}")
        else:
            print("❌ No test image provided and no images found in test/ directory")
            print("Usage: python test_production_api.py [image_path]")
            print("\nServer health check completed. API is ready for use.")
            test_stats()
            return 0
    
    print()
    
    # Test prediction
    result = test_prediction(image_path)
    
    if result:
        print()
        # Run benchmark if prediction was successful
        benchmark_predictions(image_path, 3)
    
    print()
    
    # Get final stats
    test_stats()
    
    print()
    print("=" * 50)
    print("🎉 Production API test completed!")
    
    return 0

if __name__ == '__main__':
    exit(main())