@echo off
title UniLeave File Backup
color 0A

echo ============================================
echo     UNILEAVE FILE BACKUP (No Database)
echo ============================================
echo.

set YEAR=%date:~10,4%
set MONTH=%date:~4,2%
set DAY=%date:~7,2%
set BACKUP_DATE=%YEAR%-%MONTH%-%DAY%

set BACKUP_DIR=C:\wamp64\www\unileave-backups\files-%BACKUP_DATE%

echo [INFO] Creating backup folder...
mkdir "%BACKUP_DIR%" 2>nul
echo.

echo [1/6] Backing up Controllers...
xcopy /E /I /Y /Q "C:\wamp64\www\unileave\app\Http\Controllers" "%BACKUP_DIR%\app\Http\Controllers" >nul
echo [OK] Controllers done

echo [2/6] Backing up Models...
xcopy /E /I /Y /Q "C:\wamp64\www\unileave\app\Models" "%BACKUP_DIR%\app\Models" >nul
echo [OK] Models done

echo [3/6] Backing up Traits...
if exist "C:\wamp64\www\unileave\app\Traits" (
    xcopy /E /I /Y /Q "C:\wamp64\www\unileave\app\Traits" "%BACKUP_DIR%\app\Traits" >nul
    echo [OK] Traits done
)

echo [4/6] Backing up Views...
xcopy /E /I /Y /Q "C:\wamp64\www\unileave\resources\views" "%BACKUP_DIR%\resources\views" >nul
echo [OK] Views done

echo [5/6] Backing up Routes...
xcopy /E /I /Y /Q "C:\wamp64\www\unileave\routes" "%BACKUP_DIR%\routes" >nul
echo [OK] Routes done

echo [6/6] Backing up Migrations...
xcopy /E /I /Y /Q "C:\wamp64\www\unileave\database\migrations" "%BACKUP_DIR%\database\migrations" >nul
echo [OK] Migrations done

echo.
echo ============================================
echo        BACKUP COMPLETED!
echo ============================================
echo.
echo Location: %BACKUP_DIR%
echo.
echo To restore, copy files back to your project
echo then run: php artisan migrate
echo.
pause