@echo off
title UniLeave Backup System
color 0A

echo ============================================
echo        UNILEAVE COMPLETE BACKUP
echo ============================================
echo.

REM Get current date and time
set YEAR=%date:~10,4%
set MONTH=%date:~4,2%
set DAY=%date:~7,2%
set HOUR=%time:~0,2%
set MINUTE=%time:~3,2%
set BACKUP_DATE=%YEAR%-%MONTH%-%DAY%_%HOUR%-%MINUTE%

REM Set backup directory
set BACKUP_DIR=C:\wamp64\www\unileave-backups\backup-%BACKUP_DATE%

echo [INFO] Creating backup directory...
mkdir "%BACKUP_DIR%" 2>nul

if not exist "%BACKUP_DIR%" (
    echo [ERROR] Cannot create backup directory!
    pause
    exit /b 1
)

echo [SUCCESS] Backup folder created: %BACKUP_DIR%
echo.

REM ============================================
REM 1. BACKUP DATABASE
REM ============================================
echo [1/7] Backing up database...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysqldump" -u root --no-tablespaces unileave > "%BACKUP_DIR%\database.sql"

if %errorlevel% equ 0 (
    echo [SUCCESS] Database backed up successfully
) else (
    echo [WARNING] Database backup failed
)
echo.

REM ============================================
REM 2. BACKUP APP CONTROLLERS
REM ============================================
echo [2/7] Backing up app/Controllers...
xcopy /E /I /Y /Q "C:\wamp64\www\unileave\app\Http\Controllers" "%BACKUP_DIR%\app\Http\Controllers" >nul
echo [SUCCESS] Controllers backed up
echo.

REM ============================================
REM 3. BACKUP MODELS
REM ============================================
echo [3/7] Backing up app/Models...
xcopy /E /I /Y /Q "C:\wamp64\www\unileave\app\Models" "%BACKUP_DIR%\app\Models" >nul
echo [SUCCESS] Models backed up
echo.

REM ============================================
REM 4. BACKUP TRAITS
REM ============================================
echo [4/7] Backing up app/Traits...
if exist "C:\wamp64\www\unileave\app\Traits" (
    xcopy /E /I /Y /Q "C:\wamp64\www\unileave\app\Traits" "%BACKUP_DIR%\app\Traits" >nul
    echo [SUCCESS] Traits backed up
) else (
    echo [INFO] No Traits folder found
)
echo.

REM ============================================
REM 5. BACKUP VIEWS
REM ============================================
echo [5/7] Backing up resources/views...
xcopy /E /I /Y /Q "C:\wamp64\www\unileave\resources\views" "%BACKUP_DIR%\resources\views" >nul
echo [SUCCESS] Views backed up
echo.

REM ============================================
REM 6. BACKUP ROUTES
REM ============================================
echo [6/7] Backing up routes...
xcopy /E /I /Y /Q "C:\wamp64\www\unileave\routes" "%BACKUP_DIR%\routes" >nul
echo [SUCCESS] Routes backed up
echo.

REM ============================================
REM 7. BACKUP MIGRATIONS
REM ============================================
echo [7/7] Backing up database/migrations...
xcopy /E /I /Y /Q "C:\wamp64\www\unileave\database\migrations" "%BACKUP_DIR%\database\migrations" >nul
echo [SUCCESS] Migrations backed up
echo.

REM ============================================
REM CREATE RESTORE SCRIPT
REM ============================================
echo Creating restore script...
(
echo @echo off
echo echo ============================================
echo echo        UNILEAVE RESTORE SCRIPT
echo echo ============================================
echo echo.
echo echo [1/3] Restoring database...
echo "C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql" -u root unileave ^< "%%~dp0database.sql"
echo echo.
echo echo [2/3] Restoring files...
echo echo Copy backup files to C:\wamp64\www\unileave
echo echo.
echo echo [3/3] Run: php artisan cache:clear
echo echo php artisan view:clear
echo echo.
echo echo Restore complete!
echo pause
) > "%BACKUP_DIR%\restore.bat"

echo [SUCCESS] Restore script created
echo.

REM ============================================
REM CREATE BACKUP INFO FILE
REM ============================================
echo Creating backup information file...
(
echo BACKUP INFORMATION
echo =================
echo Date: %BACKUP_DATE%
echo Location: %BACKUP_DIR%
echo.
echo INCLUDED:
echo - Database (unileave.sql)
echo - Controllers
echo - Models
echo - Traits
echo - Views
echo - Routes
echo - Migrations
echo.
echo TO RESTORE:
echo 1. Run restore.bat from backup folder
echo 2. Or manually copy files
) > "%BACKUP_DIR%\readme.txt"

echo [SUCCESS] Backup info created
echo.

REM ============================================
REM SUMMARY
REM ============================================
echo ============================================
echo           BACKUP COMPLETED!
echo ============================================
echo.
echo Backup Location: %BACKUP_DIR%
echo Total Size: 
dir "%BACKUP_DIR%" /s /-c | find "File(s)"
echo.
echo To restore, run: %BACKUP_DIR%\restore.bat
echo.
pause