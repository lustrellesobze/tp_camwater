# Politique de Sécurité — Kaba-Delivery

## Absence de données sensibles hardcodées

Conformément aux bonnes pratiques DevSecOps, aucune donnée sensible
n'est hardcodée dans les fichiers du projet.

### Preuves

| Donnée sensible | Méthode sécurisée utilisée |
|----------------|---------------------------|
| DOCKERHUB_USERNAME | GitHub Actions Secret |
| DOCKERHUB_TOKEN | GitHub Actions Secret |
| SONAR_TOKEN | GitHub Actions Secret |
| SONAR_HOST_URL | GitHub Actions Secret |
| O2SWITCH_HOST | GitHub Actions Secret |
| O2SWITCH_USER | GitHub Actions Secret |
| O2SWITCH_SSH_KEY | GitHub Actions Secret |
| DB_PASSWORD | Fichier .env (exclu du repo via .gitignore) |
| APP_KEY Laravel | Fichier .env (exclu du repo via .gitignore) |

### Vérification dans le code

- Le fichier `.env` est listé dans `.gitignore` → jamais pushé sur GitHub
- Le `Dockerfile` ne contient aucune variable d'environnement en dur
- Le pipeline `main.yml` référence uniquement `${{ secrets.NOM_SECRET }}`
- Aucun token, mot de passe ou clé SSH n'apparaît dans l'historique Git

### Commande de vérification
```bash
# Vérifier qu'aucun secret n'est présent dans le code
grep -r "password\|token\|secret\|key" --include="*.yml" .github/
```