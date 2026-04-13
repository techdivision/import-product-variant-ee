# AGENTS.md - import-product-variant-ee

## Zweck & Verantwortung

Das `import-product-variant-ee` Modul bietet **EE-spezifische Configurable Product (Variant) Import-Funktionalität**. Es ist ein **Tier 6 Modul** und erweitert `import-product-variant`.

**Hauptverantwortung:**
- EE Configurable Product Staging Support
- EE Sequence Actions für Variants
- Observer Pattern für EE Variant-Hooks

## Architektur & Design Patterns

### Kern-Klassen
- **EeVariantObserver**: Observer für EE-Hooks

### Verwendete Patterns
- **Observer Pattern**: Für EE-Hooks

## Abhängigkeiten

### Externe Pakete
- **Keine**

### TechDivision Dependencies
- **import-product-ee** ^27.0.0 - EE Product Importer
- **import-product-variant** ^26.0.0 - Configurable Product Importer

### Abhängig von diesem Modul (1 Reverse Dependency)
- **import-cli-simple** - Master CLI

## Wichtige Entry Points

### Observer Klassen
```php
// EE Variant Observer
EeVariantObserver::handle($row): void
```

## Events & Extension Points

**Keine Events** - Tier 6 EE-Modul

## Hints für KI-Agenten

### Wichtig zu verstehen
1. **Tier 6 Modul**: Erweitert Configurable Product Importer mit EE-Features
2. **EE-fokussiert**: Spezialisiert auf EE Staging
3. **Observer Pattern**: Für EE-Hooks

## Bekannte Einschränkungen

- **EE-Only**: Nur für Magento EE Deployments
- **Variant-EE-Only**: Nur für EE Configurable Products

## Zusammenfassung

`import-product-variant-ee` ist ein **Tier 6 Modul**, das EE-spezifische Configurable Product Import-Funktionalität bietet. Es erweitert den Configurable Product Importer mit EE-Features.

**Für Agenten:** Verstehe dieses Modul als **EE Configurable Product Importer** mit Observer Pattern.
