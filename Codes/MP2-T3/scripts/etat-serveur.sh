#!/bin/bash
# Affiche l'état du serveur du MP2
# Utilisation : sudo etat-serveur

echo "========== ÉTAT DU SERVEUR MP2 =========="
echo "Date : $(date '+%d/%m/%Y %H:%M:%S')"
echo "Allumé depuis : $(uptime -p)"
echo

echo "---------- Services ----------"
for service in apache2 mariadb ufw; do
    if systemctl is-active --quiet "$service"; then
        echo "$service : OK"
    else
        echo "$service : ARRÊTÉ"
    fi
done
echo

echo "---------- Ressources ----------"
echo "Charge du processeur : $(cut -d ' ' -f1-3 /proc/loadavg)"
echo "Température : $(( $(cat /sys/class/thermal/thermal_zone0/temp) / 1000 )) °C"
free -h | awk '/Mem/ {print "Mémoire : " $3 " utilisés sur " $2}'
df -h / | awk 'NR==2 {print "Disque : " $3 " utilisés sur " $2 " (" $5 ")"}'
echo

echo "---------- 5 derniers accès ----------"
mariadb mp2_salle215 -e "SELECT a.date_heure, a.valeur_lue, u.prenom, a.resultat FROM acces a LEFT JOIN utilisateurs u ON u.id = a.id_utilisateur ORDER BY a.id DESC LIMIT 5;"
echo

echo "---------- 3 dernières erreurs de l'API ----------"
grep "\.php :" /var/log/apache2/error.log | tail -n 3
