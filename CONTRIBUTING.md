
## Convention de nommage des commits (Conventional Commits)

Format : `<type>(<scope>): <description>`

### Types autorisés
- feat     → nouvelle fonctionnalité
- fix      → correction de bug
- chore    → tâche technique (config, dépendances)
- docs     → documentation
- ci       → pipeline CI/CD
- test     → ajout/modification de tests
- refactor → refactoring sans changement fonctionnel
- style    → formatage, espaces

### Exemples
- feat(api): ajout endpoint livraison
- fix(auth): correction token JWT expiré
- ci(github-actions): ajout stage SonarQube
- chore(docker): optimisation Dockerfile multi-stage