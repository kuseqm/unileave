@echo off
echo Creating CSS folder structure for UniLeave...
echo.

cd C:\wamp64\www\unileave\public\css

REM Create folders
mkdir core 2>nul
mkdir components 2>nul
mkdir layouts 2>nul
mkdir pages 2>nul
mkdir utils 2>nul
mkdir vendor 2>nul

REM Create core files
type nul > core\variables.css
type nul > core\reset.css
type nul > core\typography.css

REM Create components files
type nul > components\buttons.css
type nul > components\forms.css
type nul > components\cards.css
type nul > components\tables.css
type nul > components\alerts.css
type nul > components\badges.css

REM Create layouts files
type nul > layouts\navbar.css
type nul > layouts\sidebar.css
type nul > layouts\footer.css
type nul > layouts\grid.css

REM Create pages files
type nul > pages\landing.css
type nul > pages\login.css
type nul > pages\dashboard.css
type nul > pages\leave.css
type nul > pages\admin.css

REM Create utils files
type nul > utils\helpers.css
type nul > utils\responsive.css

REM Create vendor files
type nul > vendor\overrides.css

REM Create main file
type nul > unileave.css

echo.
echo All CSS folders and files created successfully!
echo.
echo Location: C:\wamp64\www\unileave\public\css\
echo.
pause