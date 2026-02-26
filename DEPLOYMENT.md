# Guide de Déploiement - Jessica MLM (JTWC)

Documentation complète du déploiement du projet Jessica MLM sur un serveur de production.

---

## Table des matières

1. [Infrastructure](#infrastructure)
2. [Architecture Docker](#architecture-docker)
3. [Prérequis serveur](#prérequis-serveur)
4. [Installation initiale du serveur](#installation-initiale-du-serveur)
5. [Configuration du projet](#configuration-du-projet)
6. [Déploiement initial](#déploiement-initial)
7. [CI/CD avec GitHub Actions](#cicd-avec-github-actions)
8. [SSL/HTTPS avec Certbot](#sslhttps-avec-certbot)
9. [Commandes utiles](#commandes-utiles)
10. [Dépannage](#dépannage)

---

## Infrastructure

| Élément            | Détail                                      |
|--------------------|---------------------------------------------|
| **Serveur**        | Ubuntu 22.04 LTS (2 Go RAM, 20 Go disque)  |
| **IP**             | 83.228.193.57                               |
| **Domaine**        | jessica-mlm.duckdns.org                     |
| **Hébergement**    | VPS                                         |
| **Repo GitHub**    | https://github.com/SparkJorel/Jessica_mlm   |
| **Branche prod**   | `main`                                      |
| **Accès SSH**      | `ssh -i ~/.ssh/id_rsa ubuntu@83.228.193.57` |

---

## Architecture Docker

Le projet utilise 3 containers Docker orchestrés via Docker Compose :

```
┌─────────────────────────────────────────────────────┐
│                    Serveur Ubuntu                     │
│                                                       │
│  ┌──────────────┐  ┌──────────────┐  ┌─────────────┐│
│  │  jessica_web  │  │  jessica_db  │  │jessica_redis││
│  │              │  │              │  │             ││
│  │  PHP 7.4     │  │  MySQL 5.7   │  │ Redis 7     ││
│  │  Apache 2.4  │  │              │  │ (Alpine)    ││
│  │              │  │              │  │             ││
│  │  Port: 80    │  │  Port: 3306  │  │ Port: 6379  ││
│  │  (public)    │  │  (interne)   │  │ (interne)   ││
│  └──────────────┘  └──────────────┘  └─────────────┘│
│         │                 │                │          │
│         └─────────────────┴────────────────┘          │
│                   Réseau Docker interne               │
└─────────────────────────────────────────────────────┘
```

### Container `jessica_web`
- **Image** : PHP 7.4 + Apache (Debian Bullseye)
- **Extensions PHP** : pdo_mysql, mysqli, intl, gd, opcache, zip, mbstring, sodium, bcmath, redis
- **Rôle** : Serveur web Symfony + PHP
- **Port exposé** : 80 (HTTP)
- **Volume** : `uploads` (fichiers uploadés persistants)

### Container `jessica_db`
- **Image** : MySQL 5.7
- **Rôle** : Base de données
- **Port** : 3306 (interne uniquement)
- **Volume** : `db_data` (données persistantes)
- **Healthcheck** : `mysqladmin ping` toutes les 10s
- **Init** : Le dump SQL dans `docker/init-db/` est importé automatiquement au premier lancement

### Container `jessica_redis`
- **Image** : Redis 7 Alpine
- **Rôle** : Cache applicatif (sessions, données)
- **Port** : 6379 (interne uniquement)
- **Volume** : `redis_data` (données persistantes)

---

## Prérequis serveur

- Ubuntu 20.04+ (testé sur 22.04 LTS)
- Minimum 2 Go RAM
- Minimum 10 Go disque
- Accès root ou sudo
- Port 80 ouvert (HTTP)
- Port 443 ouvert (HTTPS, pour SSL)
- Port 22 ouvert (SSH)

---

## Installation initiale du serveur

### 1. Connexion SSH

```bash
ssh -i ~/.ssh/id_rsa ubuntu@83.228.193.57
```

### 2. Installer Docker, Docker Compose et Git

```bash
# Mettre à jour les paquets
sudo apt-get update
sudo apt-get install -y ca-certificates curl gnupg git

# Ajouter le dépôt Docker officiel
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo $VERSION_CODENAME) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Installer Docker
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Ajouter l'utilisateur au groupe docker (évite sudo pour les commandes docker)
sudo usermod -aG docker ubuntu

# Vérifier l'installation
docker --version          # Docker version 29.x
docker compose version    # Docker Compose version v5.x
git --version             # git version 2.x
```

### 3. Configurer le Deploy Key GitHub

Le repo étant privé, le serveur a besoin d'une clé SSH pour cloner :

```bash
# Générer un deploy key sur le serveur
ssh-keygen -t ed25519 -f ~/.ssh/deploy_key -N "" -C "jessica-server-deploy"

# Afficher la clé publique
cat ~/.ssh/deploy_key.pub
```

Ajouter cette clé publique dans les **Deploy Keys** du repo GitHub :
- Aller sur https://github.com/SparkJorel/Jessica_mlm/settings/keys
- Cliquer "Add deploy key"
- Coller la clé publique

Ou via `gh` CLI depuis votre machine locale :
```bash
gh repo deploy-key add /chemin/vers/deploy_key.pub --title "jessica-server"
```

Configurer SSH sur le serveur pour utiliser cette clé avec GitHub :

```bash
cat > ~/.ssh/config << 'EOF'
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/deploy_key
    StrictHostKeyChecking no
EOF
chmod 600 ~/.ssh/config
```

---

## Configuration du projet

### 1. Cloner le dépôt

```bash
cd /home/ubuntu
git clone git@github.com:SparkJorel/Jessica_mlm.git jessica_mlm
cd jessica_mlm
```

### 2. Créer le fichier `.env` de production

Le fichier `.env` contient les variables d'environnement sensibles. Il n'est **jamais** versionné dans Git.

```bash
cat > .env << 'EOF'
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=CHANGER_PAR_UNE_CLE_ALEATOIRE_DE_32_CARACTERES
DATABASE_URL=mysql://jessica_user:MOT_DE_PASSE_DB@db:3306/db_jessica_mlm?serverVersion=5.7
REDIS_URL=redis://redis:6379
MAILER_URL=null://localhost
CORS_ALLOW_ORIGIN=^https?://.*$
API_KEY_DOHONE=UF216Z28675032288026408
API_URL_PAY_IN=https://www.my-dohone.com/dohone/pay
HASH_CODE_DOHONE=7C41503CF78C2A292EB5CFD906CE99
DB_PASSWORD=MOT_DE_PASSE_DB
DB_ROOT_PASSWORD=MOT_DE_PASSE_ROOT_DB
API_KEY_DOHONE=UF216Z28675032288026408
HASH_CODE_DOHONE=7C41503CF78C2A292EB5CFD906CE99
EOF
```

> **Important** : Remplacer `CHANGER_PAR_UNE_CLE_ALEATOIRE_DE_32_CARACTERES`, `MOT_DE_PASSE_DB` et `MOT_DE_PASSE_ROOT_DB` par des valeurs sécurisées.

### 3. Importer le dump SQL

Placer le fichier SQL dans le dossier d'initialisation :

```bash
mkdir -p docker/init-db
cp /chemin/vers/5b9fju_mlm_jessicatwc.sql docker/init-db/
```

Depuis votre machine locale (si le dump est sur votre PC) :

```bash
scp -i ~/.ssh/id_rsa "chemin/vers/5b9fju_mlm_jessicatwc.sql" ubuntu@83.228.193.57:/home/ubuntu/jessica_mlm/docker/init-db/
```

> **Note** : MySQL n'exécute les scripts d'initialisation que lors de la **première** création du volume. Pour réimporter, supprimer le volume : `docker compose down -v` puis relancer.

---

## Déploiement initial

### 1. Builder et lancer les containers

```bash
cd /home/ubuntu/jessica_mlm
docker compose up -d --build
```

Ce que cette commande fait :
1. **Build le container web** :
   - Installe PHP 7.4 + Apache + toutes les extensions
   - Installe Composer 2.2 LTS
   - Exécute `composer install` (installe les dépendances PHP)
   - Copie les fichiers du projet
   - Configure Apache (DocumentRoot, mod_rewrite, .htaccess)
2. **Démarre MySQL 5.7** :
   - Crée la base `db_jessica_mlm`
   - Crée l'utilisateur `jessica_user`
   - Importe le dump SQL automatiquement
3. **Démarre Redis 7**
4. **Lance le container web** qui exécute l'entrypoint :
   - Clear le cache Symfony
   - Démarre Apache

### 2. Vérifier le déploiement

```bash
# Vérifier que les 3 containers tournent
docker ps

# Tester la réponse HTTP
curl -s -o /dev/null -w "HTTP %{http_code}" http://localhost/login
# Doit retourner : HTTP 200

# Vérifier la base de données
docker exec jessica_db mysql -u jessica_user -p'MOT_DE_PASSE' db_jessica_mlm -e "SELECT COUNT(*) FROM user;"

# Voir les logs web
docker logs jessica_web

# Voir les logs Symfony
docker exec jessica_web cat var/log/prod.log | tail -20
```

### 3. Corrections post-déploiement

#### Corriger la table sessions (si erreur `sess_lifetime out of range`)

```bash
docker exec jessica_db mysql -u jessica_user -p'MOT_DE_PASSE' db_jessica_mlm -e "ALTER TABLE sessions MODIFY sess_lifetime INT NOT NULL;"
```

#### Mettre à jour le mot de passe d'un utilisateur

```bash
# Générer un hash bcrypt (coût 15) pour le mot de passe souhaité
docker exec jessica_web php -r "echo password_hash('nouveau_mot_de_passe', PASSWORD_BCRYPT, ['cost' => 15]);"

# Mettre à jour en base
docker exec jessica_db mysql -u jessica_user -p'MOT_DE_PASSE' db_jessica_mlm -e "UPDATE user SET password='HASH_BCRYPT' WHERE username='email@exemple.com';"
```

---

## CI/CD avec GitHub Actions

### Fonctionnement

Le pipeline CI/CD se déclenche automatiquement à chaque **push sur la branche `main`**. Il se connecte au serveur via SSH et exécute le déploiement.

### Fichier workflow

Le fichier se trouve dans `.github/workflows/deploy.yml` :

```yaml
name: Deploy to Production

on:
  push:
    branches:
      - main

jobs:
  deploy:
    name: Deploy Jessica MLM
    runs-on: ubuntu-latest
    timeout-minutes: 10

    steps:
      - name: Deploy to server via SSH
        uses: appleboy/ssh-action@v1.2.0
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /home/ubuntu/jessica_mlm
            git pull origin main
            docker compose build
            docker compose up -d
            sleep 5
            docker exec jessica_web php bin/console cache:clear --env=prod --no-debug || true
            echo "Deployment completed successfully"
```

### Secrets GitHub requis

Configurer ces secrets dans **Settings > Secrets and variables > Actions** du repo GitHub :

| Secret             | Valeur                                          |
|--------------------|------------------------------------------------|
| `SERVER_HOST`      | `83.228.193.57`                                |
| `SERVER_USER`      | `ubuntu`                                       |
| `SSH_PRIVATE_KEY`  | Contenu complet du fichier `~/.ssh/id_rsa`     |

Configuration via `gh` CLI :

```bash
gh secret set SERVER_HOST --body "83.228.193.57"
gh secret set SERVER_USER --body "ubuntu"
gh secret set SSH_PRIVATE_KEY < ~/.ssh/id_rsa
```

### Flux de déploiement

```
Developer push sur main
        │
        ▼
GitHub Actions déclenché
        │
        ▼
SSH vers le serveur (83.228.193.57)
        │
        ▼
git pull origin main
        │
        ▼
docker compose build
        │
        ▼
docker compose up -d
        │
        ▼
cache:clear Symfony
        │
        ▼
Site mis à jour ✓
```

---

## SSL/HTTPS avec Certbot

### Installation de Certbot et Nginx

```bash
sudo apt-get update
sudo apt-get install -y certbot nginx
```

### Obtenir un certificat SSL

```bash
# Arrêter le container web pour libérer le port 80
docker compose -f /home/ubuntu/jessica_mlm/docker-compose.yml stop web

# Obtenir le certificat
sudo certbot certonly --standalone -d jessica-mlm.duckdns.org \
    --non-interactive --agree-tos --email tiomelajorel@gmail.com

# Redémarrer le container web
docker compose -f /home/ubuntu/jessica_mlm/docker-compose.yml start web
```

### Configuration Nginx comme reverse proxy HTTPS

Une fois le certificat obtenu, configurer Nginx :

```bash
sudo tee /etc/nginx/sites-available/jessica-mlm << 'EOF'
server {
    listen 80;
    server_name jessica-mlm.duckdns.org;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name jessica-mlm.duckdns.org;

    ssl_certificate /etc/letsencrypt/live/jessica-mlm.duckdns.org/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/jessica-mlm.duckdns.org/privkey.pem;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
EOF

sudo ln -sf /etc/nginx/sites-available/jessica-mlm /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx
```

> **Note** : Avec Nginx en reverse proxy, modifier le port Docker de `80:80` à `8080:80` dans `docker-compose.yml`.

### Renouvellement automatique

Certbot installe automatiquement un timer systemd pour le renouvellement. Vérifier :

```bash
sudo systemctl status certbot.timer
```

### Problèmes connus avec DuckDNS

Les nameservers de DuckDNS peuvent retourner des erreurs `SERVFAIL` aux serveurs de Let's Encrypt, empêchant la validation HTTP. Solutions :
- **Attendre** : le problème est souvent temporaire
- **Utiliser un domaine avec des nameservers fiables** (Cloudflare, OVH, etc.)
- **Utiliser le challenge DNS** avec un plugin Certbot compatible DuckDNS

---

## Commandes utiles

### Gestion des containers

```bash
# Voir l'état des containers
docker ps

# Arrêter tous les containers
docker compose down

# Redémarrer tous les containers
docker compose restart

# Redémarrer uniquement le web
docker compose restart web

# Rebuilder et relancer
docker compose up -d --build

# Rebuilder sans cache
docker compose build --no-cache
docker compose up -d
```

### Logs

```bash
# Logs Apache + PHP
docker logs jessica_web
docker logs jessica_web --tail 50 -f    # suivre en temps réel

# Logs Symfony
docker exec jessica_web cat var/log/prod.log | tail -50

# Logs MySQL
docker logs jessica_db

# Logs Redis
docker logs jessica_redis
```

### Base de données

```bash
# Accéder au shell MySQL
docker exec -it jessica_db mysql -u jessica_user -p db_jessica_mlm

# Exécuter une requête
docker exec jessica_db mysql -u jessica_user -p'MOT_DE_PASSE' db_jessica_mlm -e "SELECT COUNT(*) FROM user;"

# Faire un dump de la base
docker exec jessica_db mysqldump -u root -p'MOT_DE_PASSE_ROOT' db_jessica_mlm > backup_$(date +%Y%m%d).sql

# Restaurer un dump
docker exec -i jessica_db mysql -u root -p'MOT_DE_PASSE_ROOT' db_jessica_mlm < backup.sql
```

### Symfony

```bash
# Vider le cache
docker exec jessica_web php bin/console cache:clear --env=prod --no-debug

# Vérifier la connexion à la base
docker exec jessica_web php bin/console doctrine:schema:validate

# Lancer une migration
docker exec jessica_web php bin/console doctrine:migrations:migrate --no-interaction

# Shell dans le container web
docker exec -it jessica_web bash
```

### Volumes et données

```bash
# Lister les volumes
docker volume ls

# Supprimer les volumes (ATTENTION : perte de données)
docker compose down -v

# Sauvegarder le volume de la base
docker run --rm -v jessica_mlm_db_data:/data -v $(pwd):/backup ubuntu tar czf /backup/db_backup.tar.gz /data
```

---

## Dépannage

### Erreur 500

1. Vérifier les logs Symfony :
   ```bash
   docker exec jessica_web cat var/log/prod.log | tail -30
   ```

2. Vérifier les logs Apache :
   ```bash
   docker logs jessica_web | tail -20
   ```

3. Vider le cache :
   ```bash
   docker exec jessica_web php bin/console cache:clear --env=prod --no-debug
   ```

### Container qui ne démarre pas

```bash
# Voir les logs du container en échec
docker logs jessica_web

# Vérifier le status
docker ps -a

# Recréer le container
docker compose up -d --force-recreate web
```

### Base de données inaccessible

```bash
# Vérifier que MySQL est healthy
docker ps   # colonne STATUS doit afficher (healthy)

# Tester la connexion
docker exec jessica_db mysql -u jessica_user -p'MOT_DE_PASSE' -e "SELECT 1;"
```

### Le dump SQL n'est pas importé

MySQL n'exécute les scripts `docker-entrypoint-initdb.d/` que lors de la **première** initialisation du volume. Pour réimporter :

```bash
# Supprimer le volume et recréer
docker compose down -v
docker compose up -d --build
```

### Problème de permissions sur `var/`

```bash
docker exec jessica_web chown -R www-data:www-data var/
docker exec jessica_web chmod -R 775 var/
```

### Erreur `sess_lifetime out of range`

```bash
docker exec jessica_db mysql -u jessica_user -p'MOT_DE_PASSE' db_jessica_mlm \
    -e "ALTER TABLE sessions MODIFY sess_lifetime INT NOT NULL;"
```

### Espace disque plein

```bash
# Nettoyer les images Docker non utilisées
docker system prune -a

# Vérifier l'espace
df -h
```

---

## Structure des fichiers de déploiement

```
Jessica_mlm/
├── .github/
│   └── workflows/
│       └── deploy.yml          # Pipeline CI/CD GitHub Actions
├── docker/
│   ├── entrypoint.sh           # Script de démarrage du container web
│   ├── init-db/                # Dump SQL (non versionné, à placer sur le serveur)
│   │   └── *.sql
│   └── php.ini                 # Configuration PHP personnalisée
├── Dockerfile                  # Image Docker PHP 7.4 + Apache
├── docker-compose.yml          # Orchestration des 3 containers
├── .dockerignore               # Fichiers exclus du build Docker
├── .env                        # Variables d'environnement (non versionné en prod)
├── .env.prod                   # Template des variables de production (non versionné)
└── ...
```

---

## Historique des versions déployées

| Date       | Description                                              |
|------------|----------------------------------------------------------|
| 2026-02-26 | Déploiement initial - Docker (PHP 7.4 + MySQL 5.7 + Redis) |
