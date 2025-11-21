@echo off
echo ========================================
echo Database Migration Runner
echo ========================================
echo.

REM Set MySQL path
set MYSQL_PATH=C:\xampp\mysql\bin\mysql.exe
set DB_NAME=db_upholcare
set DB_USER=root
set DB_PASS=

REM Check if MySQL exists
if not exist "%MYSQL_PATH%" (
    echo ERROR: MySQL not found at %MYSQL_PATH%
    echo Please check your XAMPP installation.
    pause
    exit /b 1
)

echo Step 1: Adding verification code columns to admin_registrations...
echo.

"%MYSQL_PATH%" -u %DB_USER% %DB_PASS% %DB_NAME% < "%~dp0add_verification_code_to_admin_registrations.sql"

if %errorlevel% equ 0 (
    echo.
    echo ✓ Migration 1 completed successfully!
) else (
    echo.
    echo ✗ Migration 1 failed. Please check the errors above.
    pause
    exit /b 1
)

echo.
echo Step 2: Creating verification codes table and populating codes...
echo.

"%MYSQL_PATH%" -u %DB_USER% %DB_PASS% %DB_NAME% < "%~dp0setup_verification_codes_complete.sql"

if %errorlevel% equ 0 (
    echo.
    echo ✓ Migration 2 completed successfully!
) else (
    echo.
    echo ✗ Migration 2 failed. Please check the errors above.
    pause
    exit /b 1
)

echo.
echo ========================================
echo All migrations completed successfully!
echo ========================================
echo.
echo Verification:
echo - admin_registrations table now has verification_code columns
echo - admin_verification_codes table created with 9000 codes (1000-9999)
echo.
pause

