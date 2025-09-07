@echo off
REM NICS24 Provider Test Batch Script
REM For Windows users to easily run the test

echo.
echo ======================================
echo   NICS24 Provider Test Launcher
echo ======================================
echo.

REM Check if PHP is available
php --version >nul 2>&1
if errorlevel 1 (
    echo ❌ PHP is not installed or not in PATH
    echo 💡 Please install PHP and add it to your system PATH
    pause
    exit /b 1
)

echo ✅ PHP is available
echo.

:menu
echo Select test type:
echo 1. Quick connectivity test
echo 2. Full test with OTP flow
echo 3. Debug mode test
echo 4. Custom test (enter parameters)
echo 5. Show usage help
echo 6. Exit
echo.

set /p choice="Enter your choice (1-6): "

if "%choice%"=="1" goto quick_test
if "%choice%"=="2" goto full_test
if "%choice%"=="3" goto debug_test
if "%choice%"=="4" goto custom_test
if "%choice%"=="5" goto show_help
if "%choice%"=="6" goto exit

echo Invalid choice. Please try again.
echo.
goto menu

:quick_test
echo.
echo 🔍 Running Quick Connectivity Test...
echo ====================================
php quick-test-nics24.php
goto end

:full_test
echo.
echo 🚀 Running Full Test with OTP Flow...
echo ====================================
echo.
set /p mobile="Enter mobile number (or press Enter for default 09123456789): "
set /p national_code="Enter national code (or press Enter for default 1234567890): "

if "%mobile%"=="" set mobile=09123456789
if "%national_code%"=="" set national_code=1234567890

echo.
echo Running test with:
echo Mobile: %mobile%
echo National Code: %national_code%
echo.

php test-nics24-provider.php --mobile=%mobile% --national_code=%national_code%
goto end

:debug_test
echo.
echo 🐛 Running Debug Mode Test...
echo =============================
php test-nics24-provider.php --debug
goto end

:custom_test
echo.
echo ⚙️ Custom Test Configuration
echo ============================
set /p mobile="Mobile number (default: 09123456789): "
set /p national_code="National code (default: 1234567890): "
set /p provider="Provider (default: nics24): "
set /p timeout="Timeout in seconds (default: 300): "

if "%mobile%"=="" set mobile=09123456789
if "%national_code%"=="" set national_code=1234567890
if "%provider%"=="" set provider=nics24
if "%timeout%"=="" set timeout=300

echo.
echo Running custom test with:
echo Mobile: %mobile%
echo National Code: %national_code%
echo Provider: %provider%
echo Timeout: %timeout% seconds
echo.

php test-nics24-provider.php --mobile=%mobile% --national_code=%national_code% --provider=%provider% --timeout=%timeout% --debug
goto end

:show_help
echo.
echo 📖 NICS24 Test Help
echo ===================
echo.
echo Available test scripts:
echo.
echo 1. quick-test-nics24.php
echo    - Quick connectivity and configuration check
echo    - No OTP required
echo    - Usage: php quick-test-nics24.php
echo.
echo 2. test-nics24-provider.php
echo    - Full credit score test with OTP flow
echo    - Supports various options
echo    - Usage: php test-nics24-provider.php [options]
echo.
echo Available options:
echo   --mobile=09123456789       Mobile number for OTP
echo   --national_code=1234567890 National code
echo   --provider=nics24          Provider name
echo   --timeout=300              Timeout in seconds
echo   --debug                    Enable debug output
echo.
echo Prerequisites:
echo   - Node.js server running on port 9999
echo   - Redis server running
echo   - NICS24 credentials configured in config.js
echo   - Captcha API running on localhost:9090
echo.
echo Examples:
echo   php test-nics24-provider.php
echo   php test-nics24-provider.php --debug
echo   php test-nics24-provider.php --mobile=09121234567 --debug
echo.
pause
goto menu

:end
echo.
echo Test completed. Check the log files for detailed information.

:end_wait
echo.
set /p restart="Run another test? (y/n): "
if /i "%restart%"=="y" goto menu
if /i "%restart%"=="yes" goto menu

:exit
echo.
echo 👋 Goodbye!
pause