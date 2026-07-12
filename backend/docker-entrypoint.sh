#!/bin/sh
# Script de démarrage en production (Railway).
#
# 1. Met en cache la config/routes (perf : évite de reparser les fichiers PHP
#    à chaque requête — gain notable avec beaucoup de routes/traductions).
# 2. Lance les migrations en attente (idempotent : ne rejoue pas celles déjà
#    appliquées). --force nécessaire car APP_ENV=production bloque sinon
#    l'exécution interactive.
# 3. Démarre le serveur PHP sur le port fourni par Railway (variable $PORT,
#    différente à chaque déploiement).
set -e

php artisan config:cache
php artisan route:cache
php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
