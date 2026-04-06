# BiblioConnect — Projet Symfony

Plateforme de gestion de bibliothèque développée avec Symfony 8 et PostgreSQL (Neon).

## Stack

- PHP 8.4 / Symfony 8.0
- Doctrine ORM + PostgreSQL (Neon serverless)
- Twig + Bootstrap 5
- KnpPaginatorBundle, VichUploaderBundle
- PHPUnit 12

## Installation

```bash
git clone <url-du-depot>
cd biblioconnect
composer install
cp .env.example .env.local
# Renseigner DATABASE_URL dans .env.local
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
symfony server:start
```

## Comptes de test (après fixtures)

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@biblioconnect.fr | Admin123! |
| Bibliothécaire | librarian@biblioconnect.fr | Libra123! |
| Usager | user@biblioconnect.fr | User1234! |

## Tests

```bash
php bin/phpunit
```

## Fonctionnalités

**Usagers** : inscription/connexion, recherche d'ouvrages, réservation, favoris, commentaires et notes.

**Bibliothécaires** : gestion du catalogue (livres, auteurs, catégories, langues, images), consultation des réservations.

**Admins** : tout ce que fait le bibliothécaire + modération des commentaires, gestion des utilisateurs et des rôles.

**Challenge** : redirection automatique vers le bon espace selon le rôle après connexion.
