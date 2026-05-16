# AGENTS.md - import-product-variant-ee

## Zweck & Verantwortung

Das `import-product-variant-ee` Modul bietet **EE-spezifische Configurable Product (Variant) Import-Funktionalität** mit Staging und Sequence-Management. Es ist ein **Tier 6 Modul** in der EE-Import-Hierarchie und erweitert das `import-product-variant` Modul mit Enterprise Edition Features.

**Hauptverantwortung:**
- EE Configurable Product Staging Support (zukünftige Variant-Updates)
- EE Sequence Actions für Audit-Trail Variant-Imports
- Observer Pattern Integration mit EE Hooks
- Staging-Table Management für Super Attributes
- Version und Timeline Management für Variants
- Variant Association Staging Koordination

**Modul-Kategorie:** EE Extension Module  
**Komplexität:** ⭐⭐⭐⭐ (Hoch - komplexe Staging-Logik)  
**Abhängig von:** Magento EE Enterprise Edition

## Architektur & Design Patterns

### Kern-Klassen
- **EeVariantRepository**: EE Configurable-spezifische Persistierung mit Staging
- **StagingVariantRepository**: Staging-Table Management für Super Attributes
- **SequenceActionRepository**: Audit-Trail für Variant-Imports
- **EeVariantProcessor**: Service Layer für EE Configurable-Verarbeitung
- **EeVariantObserver**: Observer für EE Lifecycle Hooks
- **VariantStagingManager**: Koordiniert Staging für Super Attribute und Varianten

### Verwendete Patterns
- **Observer Pattern**: Integration mit Parent Variant Import Hooks
- **Repository Pattern**: Abstraktion der Staging-Datenschicht
- **Service Layer Pattern**: EE-spezifische Business Logic
- **Staging Pattern**: Zeitgesteuerte Attribute/Variant-Updates
- **Decorator Pattern**: Erweiterung der Base Variant Repositories

### Staging-Datenfluss
```
Configurable CSV (mit Datum/Zeit)
    ↓
Parser + Converter
    ↓
EE Variant Processor
    ├─→ StagingVariantRepository (in *_staging Tabellen)
    ├─→ SequenceActionRepository (Audit-Trail)
    └─→ VariantStagingManager (Timing-Verwaltung)
    ↓
Magento Database (catalog_product_super_*_staging)
    ↓
Scheduler aktualisiert Produktivtabellen zum Zeitstempel
```

## Abhängigkeiten

### Externe Pakete
- **Keine direkten PHP-Pakete**

### TechDivision Dependencies
- **import-product-ee** ^27.0.0 - EE Product Importer (Base)
- **import-product-variant** ^26.0.0 - Configurable Product Importer (Parent)
- **import-attribute-ee** - EE Attribute Staging Framework
- **import-converter-ee** - EE Conversion Framework

### Abhängig von diesem Modul (1 Reverse Dependency)
- **import-cli-simple** - Master CLI für alle Importer

### Magento EE Dependencies
- **Magento_Staging** - Core Staging Framework
- **Magento_Enterprise** - EE License Check

## Wichtige Entry Points

### Repository Klassen
```php
// EE Variant Repository - mit Staging-Support
EeVariantRepository::create($row): void
EeVariantRepository::findByProductIdAndStaging($productId, $stagingId): VariantStaging

// Staging Variant Repository - Staging-Tabellen-Verwaltung
StagingVariantRepository::create($row, $stagingData): void
StagingVariantRepository::findByStagingId($stagingId): array

// Sequence Action Repository - Audit-Trail
SequenceActionRepository::createAction($variantId, $action): void
```

### Observer Methods
- `EeVariantObserver::handle()` - Haupteingangspunkt für EE-Integration
- `EeVariantObserver::handleVariantStaging()` - Staging-spezifische Logik
- `EeVariantObserver::createSequenceAction()` - Audit-Trail Record
- `EeVariantObserver::manageSuperAttributeStaging()` - Attribut-Staging

## Events & Extension Points

**Erbt Parent Events** aus import-product-variant, erweitert um EE-spezifische

### Observer Hooks
- `product.import.variant.staging.validate.pre` - Vor Staging-Validierung
- `product.import.variant.attribute.staging.process.post` - Nach Super Attribute Staging
- `product.import.variant.link.staging.process.post` - Nach Link-Staging
- `product.import.variant.sequence.action.create` - Audit-Trail Record
- `product.import.variant.staging.schedule.post` - Nach Scheduling

## Database Schema

### EE-Staging-Tabellen
- **catalog_product_super_attribute_staging** - Super Attributes Staging
  - `product_super_attribute_id`, `product_id`, `attribute_id`
  - `created_in`, `updated_in` - Staging Timeline
  
- **catalog_product_super_attribute_label_staging** - Attribute Label Staging
  - `product_super_attribute_label_id`
  - `created_in`, `updated_in` - Staging Timeline

- **catalog_product_super_link_staging** - Variant Link Staging
  - `product_id` (Simple Variant)
  - `parent_id` (Configurable)
  - `created_in`, `updated_in` - Staging Timeline

### Audit-Trail Tabellen
- **sequence_product_ee** - Sequence für Variant Imports
  - `sequence_id`, `variant_id`, `action_type`
  - `created_at`, `import_batch_id`

## Common Use Cases

### Use Case 1: Zukünftige Configurable Attribut-Änderungen
```php
// CSV mit Staging-Datum:
// sku,super_attribute_color,super_attribute_size,staging_from_date

// SHIRT-PARENT,color,size,2026-04-20 09:00:00
// Importer erstellt:
// 1. Super Attributes in catalog_product_super_attribute_staging
// 2. created_in/updated_in = 2026-04-20 09:00:00
// 3. Scheduler aktiviert zum Zeitstempel
```

### Use Case 2: Variant-Versioning mit Audit
```php
// Nach Variant Import wird Versioning erstellt
// sequence_product_ee Eintrag:
// - variant_id: 789
// - action_type: 'UPDATE'
// - import_batch_id: 'VARIANT_2026_04_20'
```

## Performance Considerations

### Wichtige Performance-Aspekte
1. **Staging-Overhead**: Schreib in 5+ Tabellen (Attribute + Label + Link + Staging)
2. **Timeline Indizes**: created_in/updated_in MUSS auf ALLEN staging-Tabellen indexiert sein
3. **Sequence-Lookups**: Audit-Trail Inserts sollten batched werden
4. **Label-Staging**: Separate Inserts für jedes Store-Label

### Optimierungen
- Batch Staging-Inserts (max 500 pro Batch)
- Nutze Transaktionen für Consistency zwischen live/staging
- Cleanup alte Staging-Daten nach Schedule-Execution
- Pre-cache Attribute-IDs und Store-Informationen

### Speicher-Optimierung
- Streame große Staging-Operationen
- Garbage Collection für Audit-Trail Records
- Archiviere alte Sequence-Actions

## Hints für KI-Agenten

### Kritisches Verständnis
1. **Tier 6 Modul**: EE-spezifische Extension des Variant Importers
2. **Staging-fokussiert**: Arbeitet mit zukünftigen Timelines
3. **Attribut-Staging**: Super Attributes haben eigene Staging-Tabellen
4. **Observer Pattern**: Integration in Parent Variant Import
5. **Audit-Trail**: Sequencing für Compliance/Audit

### Häufige Fehler
- ❌ Staging-Tabellen ignorieren
- ❌ Label-Staging nicht für alle Stores erstellen
- ❌ Timeline-Indizes nicht beachten
- ❌ Sequence-Actions nicht erstellen
- ❌ Transaktionen nicht nutzen
- ❌ Alte Staging-Daten nicht cleanup

### Best Practices
- ✅ Nutze Staging-Repositories statt direkter DB-Zugriffe
- ✅ Erstelle Sequence-Actions für Audit-Trails
- ✅ Nutze Transaktionen für Multi-Table Updates
- ✅ Validiere Staging-Termine VOR Persistierung
- ✅ Implementiere Cleanup für alte Staging-Daten
- ✅ Teste mit echten mehrstufigen CSV-Dateien

## Known Limitations

- **EE-Only**: Funktioniert nur auf Magento EE Deployments
- **Staging-Abhängig**: Erfordert dass Magento_Staging aktiviert ist
- **Timeline-Restriktionen**: created_in muss größer als updated_in sein
- **Performance-Overhead**: 5x+ Speicherplatz für Staging-Duplikate
- **Keine Rollback**: Staging nur über Scheduler zurückgängig machbar
- **Label-Komplexität**: Separate Staging für Label pro Store

## Related Modules

### Direct Dependencies
- **import-product-variant** - Base Configurable Product Importer
- **import-product-ee** - EE Product Import Framework
- **import-attribute-ee** - EE Attribute Staging

### Related/Companion Modules
- **import-product-bundle-ee** - EE Bundle Product Importer
- **import-product-grouped-ee** - EE Grouped Product Importer
- **import-product-ee** - Base EE Product Importer

## Troubleshooting

### Problem: Super Attributes werden nicht in Staging aktualisiert
**Mögliche Ursachen:**
1. Magento_Staging nicht aktiviert
2. Staging-Tabellen nicht existieren
3. Timeline falsch konfiguriert

**Lösung:**
- Prüfe dass Magento_Staging aktiviert ist
- Validiere dass Staging-Tabellen erstellt wurden
- Stelle sicher dass created_in in Zukunft liegt

### Problem: Variant-Links werden nicht übernommen
**Mögliche Ursachen:**
1. catalog_product_super_link_staging falsch gefüllt
2. Scheduler läuft nicht
3. Simple Products nicht vorhanden

**Lösung:**
- Validiere dass Links im Staging gespeichert werden
- Prüfe dass Cron-Job läuft
- Stelle sicher dass Simple Products existieren

### Problem: Attribute Labels fehlen
**Mögliche Ursachen:**
1. Label-Staging nicht erstellt
2. Store-IDs nicht korrekt
3. Cleanup zu aggressiv

**Lösung:**
- Validiere dass Labels für ALLE Stores im Staging sind
- Prüfe dass store_id korrekt ist
- Reduziere Cleanup-Aggressivität

## Zusammenfassung

`import-product-variant-ee` ist ein **Tier 6 EE-Modul**, das Enterprise Edition Features für Configurable Product Import mit komplexer Staging und Audit-Trail bietet. Es verwaltet die Staging von Super Attributes, Labels und Variant-Links mit Versioning.

**Für KI-Agenten:** Verstehe dieses Modul als:
- **EE Configurable Product Importer** mit Staging Support
- **Tier 6 Extension** mit Timeline-Management
- **Attribut-Staging-fokussiert** mit Label und Link Staging
- **Audit-Trail Integration** für Tracking und Compliance
