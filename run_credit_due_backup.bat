@echo off
cd /d "%~dp0"
php artisan backup:credit-due-list
