# Supervision du serveur — MP2

## 1. Ce qui est surveillé

| Élément                      | Où le voir                                   |
|:----------------------------:|:--------------------------------------------:|
| Chaque passage de badge      | Table `acces` de la base de données          |
| Erreurs de l'API             | `/var/log/apache2/error.log`                 |
| Connexions bloquées          | Journaux du système (`[UFW BLOCK]`)          |
| État des services            | Script `etat-serveur`                        |
| Processeur, mémoire, disque  | Script `etat-serveur`                        |

On a choisi un script simple plutôt qu'un outil de supervision complet : un Raspberry Pi a peu de ressources, et un script suffit pour un seul serveur.

## 2. Script d'état du serveur

Le script est dans `Codes/MP2-T3/scripts/etat-serveur.sh`.

### Installer le script.

```bash
sudo curl -o /usr/local/bin/etat-serveur https://raw.githubusercontent.com/youssefsaidi-ops/Controle-d-acces-Authentification-Interface-Web/t3-supervision/Codes/MP2-T3/scripts/etat-serveur.sh
sudo chmod +x /usr/local/bin/etat-serveur
```

### Lancer le script.

```bash
sudo etat-serveur
```

Résultat obtenu : 

| Partie     | Résultat                                       |
|:----------:|:----------------------------------------------:|
| apache2    | OK                                             |
| mariadb    | OK                                             |
| pare-feu   | OK                                             |
| Processeur | 0.00 0.00 0.00                                 |
| Température | 40 °C                                         |
| Mémoire    | 271Mi utilisés sur 905Mi                       |
| Disque     | 3.2G utilisés sur 29G (12%)                    |

Le script affiche aussi les 5 derniers accès et les 3 dernières erreurs de l'API.

## 3. Voir les accès en temps réel

```bash
sudo watch -n 2 'mariadb mp2_salle215 -e "SELECT a.date_heure, a.valeur_lue, u.prenom, a.resultat FROM acces a LEFT JOIN utilisateurs u ON u.id = a.id_utilisateur ORDER BY a.id DESC LIMIT 10;"'
```

L'affichage se met à jour toutes les 2 secondes. `Ctrl + C` pour arrêter.

## 4. Anomalie rencontrée

| Problème                              | Cause                                                                 | Correction                                  |
|:-------------------------------------:|:---------------------------------------------------------------------:|:-------------------------------------------:|
| Le script affichait `ufw : ARRÊTÉ`    | `systemctl` vérifie le service de démarrage d'UFW, pas le pare-feu lui-même | Vérification avec `ufw status` à la place |
