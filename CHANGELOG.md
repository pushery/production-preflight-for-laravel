# Changelog

All notable changes to `pushery/production-preflight-for-laravel` are documented here. The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and the project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

A release carries only the sections that have an entry, in this order: `### ⚠️ Breaking`, `### ✨ Added`, `### 🔧 Changed`, `### ⚡ Performance`, `### 🐛 Fixed`, `### 🔒 Security`, `### 🧹 Deprecated`, `### 🗑️ Removed` and `### 📚 Documentation`.

## [0.1.0] - 2026-10-30

Initial release. The package stops a Laravel deploy on a production misconfiguration and keeps checking once the application runs. `php artisan preflight --deploy` runs the checks a deploy can answer, `php artisan env:lint` lints a `.env` file as text, and twenty-seven checks run as laravel-health checks over the `.env` file, the configuration and the installation. No finding carries a value of the application.

[Unreleased]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/pushery/production-preflight-for-laravel/releases/tag/v0.1.0
