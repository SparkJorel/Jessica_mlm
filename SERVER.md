# Documentation Serveur - Jessica MLM

## Informations du serveur

| Élément       | Valeur                |
|---------------|-----------------------|
| **IP**        | 83.228.193.57         |
| **OS**        | Ubuntu                |
| **Utilisateur** | ubuntu              |
| **Authentification** | Clé SSH (RSA) |

---

## Connexion SSH

### Prérequis

- Disposer de la clé privée SSH (`id_rsa`) fournie lors de la création du serveur
- Avoir un client SSH installé (natif sur Linux/macOS, OpenSSH sur Windows 10+)

### Configuration

1. Placer la clé privée dans le dossier SSH :

   ```bash
   # Linux / macOS
   cp /chemin/vers/id_rsa ~/.ssh/id_rsa
   chmod 600 ~/.ssh/id_rsa

   # Windows (PowerShell)
   copy C:\chemin\vers\id_rsa C:\Users\VOTRE_USER\.ssh\id_rsa
   ```

2. (Optionnel) Créer un raccourci dans `~/.ssh/config` :

   ```
   Host jessica
       HostName 83.228.193.57
       User ubuntu
       IdentityFile ~/.ssh/id_rsa
   ```

### Connexion

```bash
# Avec le raccourci configuré :
ssh jessica

# Sans raccourci :
ssh -i ~/.ssh/id_rsa ubuntu@83.228.193.57
```

---

## Ajouter une nouvelle machine

Pour autoriser une nouvelle machine à se connecter au serveur **sans copier la clé privée existante** (méthode recommandée) :

### 1. Générer une clé SSH sur la nouvelle machine

```bash
ssh-keygen -t ed25519 -C "description-de-la-machine"
```

Cela crée deux fichiers :
- `~/.ssh/id_ed25519` (clé privée - ne jamais partager)
- `~/.ssh/id_ed25519.pub` (clé publique - à ajouter au serveur)

### 2. Récupérer la clé publique

```bash
cat ~/.ssh/id_ed25519.pub
```

Copier le résultat (une ligne commençant par `ssh-ed25519 ...`).

### 3. Ajouter la clé publique au serveur

Depuis une machine déjà autorisée, se connecter au serveur et ajouter la clé :

```bash
ssh jessica
echo "ssh-ed25519 AAAA... description-de-la-machine" >> ~/.ssh/authorized_keys
```

### 4. Tester la connexion depuis la nouvelle machine

```bash
ssh -i ~/.ssh/id_ed25519 ubuntu@83.228.193.57
```

---

## Révoquer l'accès d'une machine

Si une machine est perdue ou compromise :

```bash
ssh jessica
nano ~/.ssh/authorized_keys
```

Supprimer la ligne correspondant à la clé de la machine à révoquer, puis sauvegarder.

---

## Bonnes pratiques de sécurité

- **Ne jamais** stocker la clé privée dans un dépôt Git (même privé)
- **Ne jamais** partager la clé privée par email ou messagerie non chiffrée
- Utiliser une clé différente par machine (permet de révoquer individuellement)
- Protéger la clé privée avec une passphrase lors de la génération
- Vérifier régulièrement les clés autorisées dans `~/.ssh/authorized_keys` sur le serveur

---

## Transfert de fichiers

### Envoyer un fichier vers le serveur

```bash
scp -i ~/.ssh/id_rsa fichier.txt ubuntu@83.228.193.57:/chemin/destination/

# Avec le raccourci :
scp fichier.txt jessica:/chemin/destination/
```

### Envoyer un dossier vers le serveur

```bash
scp -r mon_dossier/ jessica:/chemin/destination/
```

### Télécharger un fichier depuis le serveur

```bash
scp jessica:/chemin/fichier.txt ./
```

---

## Commandes utiles une fois connecté

```bash
# Voir l'espace disque
df -h

# Voir la mémoire
free -h

# Voir les processus
htop

# Voir les logs système
sudo journalctl -f

# Redémarrer le serveur
sudo reboot
```
