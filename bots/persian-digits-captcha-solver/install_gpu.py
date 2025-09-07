import subprocess
import sys
import os

def run_command(command, description):
    """Run a command and handle errors"""
    print(f"\n🔄 {description}")
    print(f"Running: {command}")
    
    try:
        result = subprocess.run(command, shell=True, capture_output=True, text=True)
        if result.returncode == 0:
            print(f"✅ {description} - Success!")
            if result.stdout:
                print(f"Output: {result.stdout.strip()}")
            return True
        else:
            print(f"❌ {description} - Failed!")
            if result.stderr:
                print(f"Error: {result.stderr.strip()}")
            return False
    except Exception as e:
        print(f"❌ {description} - Exception: {e}")
        return False

def check_nvidia_gpu():
    """Check if NVIDIA GPU is available"""
    print("🔍 Checking for NVIDIA GPU...")
    return run_command("nvidia-smi", "NVIDIA GPU check")

def install_tensorflow_gpu():
    """Install TensorFlow with GPU support"""
    print("\n📦 Installing TensorFlow with GPU support...")
    
    # Uninstall existing TensorFlow
    run_command("pip uninstall tensorflow tensorflow-gpu -y", "Uninstalling existing TensorFlow")
    
    # Install TensorFlow with GPU support
    success = run_command("pip install tensorflow[gpu]==2.19.0", "Installing TensorFlow GPU")
    
    if success:
        print("✅ TensorFlow GPU installation completed!")
    else:
        print("❌ TensorFlow GPU installation failed!")
        print("💡 Try installing manually: pip install tensorflow[gpu]==2.19.0")
    
    return success

def install_dependencies():
    """Install other required dependencies"""
    print("\n📦 Installing other dependencies...")
    
    dependencies = [
        "numpy==2.1.3",
        "opencv-python==4.11.0.86", 
        "matplotlib==3.10.1",
        "keras==3.9.2",
        "pillow==11.2.1",
        "scipy==1.15.2"
    ]
    
    for dep in dependencies:
        run_command(f"pip install {dep}", f"Installing {dep}")

def main():
    print("=" * 60)
    print("🚀 TensorFlow GPU Setup Assistant")
    print("=" * 60)
    
    print("\nThis script will help you set up TensorFlow with GPU support.")
    print("Make sure you have:")
    print("1. NVIDIA GPU")
    print("2. NVIDIA drivers installed")
    print("3. CUDA Toolkit installed")
    print("4. cuDNN installed")
    
    # Check GPU
    gpu_available = check_nvidia_gpu()
    
    if not gpu_available:
        print("\n⚠️  No NVIDIA GPU detected!")
        print("Please install NVIDIA drivers first.")
        print("Visit: https://www.nvidia.com/Download/index.aspx")
        return
    
    # Install TensorFlow GPU
    tf_success = install_tensorflow_gpu()
    
    # Install other dependencies
    install_dependencies()
    
    # Test the installation
    print("\n🧪 Testing GPU setup...")
    test_success = run_command("python test_gpu.py", "GPU setup test")
    
    print("\n" + "=" * 60)
    if tf_success and test_success:
        print("🎉 Setup completed successfully!")
        print("You can now train with GPU acceleration:")
        print("  python train_gpu.py")
    else:
        print("❌ Setup encountered issues.")
        print("Please check the GPU_SETUP_GUIDE.md for manual instructions.")
    print("=" * 60)

if __name__ == "__main__":
    main() 