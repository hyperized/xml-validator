# [hyperized/xml-validator](https://packagist.org/packages/hyperized/xml-validator)

[![Buy us a tree](https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen)](https://plant.treeware.earth/hyperized/xml-validator) ![Run tests](https://github.com/hyperized/xml-validator/workflows/Run%20tests/badge.svg) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hyperized/xml-validator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/hyperized/xml-validator/?branch=master) [![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator?ref=badge_shield) [![License](https://poser.pugx.org/hyperized/xml-validator/license)](https://packagist.org/packages/hyperized/xml-validator)

A simple PHP XML validator.

## Installation

```shell
composer require hyperized/xml-validator
```

| Package | PHP | Notes |
| --- | --- | --- |
| 2.x | ^8.3 | Current. Requires `ext-dom` and `ext-libxml` |
| 1.x | >=7.2 | Unmaintained. See [Migrating from 1.x](#migrating-from-1x) |

## Usage

Validate a document and report why it failed:

```php
use Hyperized\Xml\Exceptions\InvalidXml;
use Hyperized\Xml\Exceptions\XmlValidatorException;
use Hyperized\Xml\Validator;

$validator = new Validator();

try {
    $validator->validateXMLFile('/path/to/document.xml', '/path/to/schema.xsd');
} catch (InvalidXml $exception) {
    // Malformed, or rejected by the schema. Line and column survive.
    foreach ($exception->getErrors() as $error) {
        printf("line %d column %d: %s\n", $error->line, $error->column, trim($error->message));
    }
} catch (XmlValidatorException $exception) {
    // Missing, unreadable or empty file.
    printf("%s: %s\n", $exception::class, $exception->getMessage());
}
```

The schema is optional, and a document you already hold in memory goes through
`validateXMLString()` instead. Both return `void` and throw on failure:

```php
$validator->validateXMLFile('/path/to/document.xml');
$validator->validateXMLString($xml);
$validator->validateXMLString($xml, '/path/to/schema.xsd');
```

### Exceptions

Every exception this package throws implements `XmlValidatorException`, so one
catch block covers all of them and keeps working when a new one is added.

| Exception | Thrown when |
| --- | --- |
| `FileDoesNotExist` | No file at the given XML or XSD path |
| `FileCouldNotBeOpenedException` | The path exists but reading it failed |
| `EmptyFile` | The XML file holds nothing |
| `InvalidXml` | Malformed, empty once trimmed, or rejected by the schema |

All four extend `RuntimeException`.

`InvalidXml::getErrors()` returns libxml's own `LibXMLError` objects, so line,
column and level survive. `getMessage()` is a newline-joined summary of the same
errors, which is usually what you want in a log line.

### When you only need yes or no

The `is*Valid()` predicates run the same check, catch the exceptions above and
return `false` instead. They never throw:

```php
$validator->isXMLFileValid('/path/to/document.xml');
$validator->isXMLFileValid('/path/to/document.xml', '/path/to/schema.xsd');

$validator->isXMLStringValid($xml);
$validator->isXMLStringValid($xml, '/path/to/schema.xsd');
```

Because they are implemented on top of the throwing methods, the two forms can
never disagree. Reach for these when a failure needs no explanation; reach for
`validate*()` when it does.

### Dependency injection

`Validator` implements `ValidatorInterface`, which declares all eight public
methods. Type-hint against the interface where you inject or mock it:

```php
use Hyperized\Xml\ValidatorInterface;

public function __construct(private readonly ValidatorInterface $validator) {}
```

No error state is kept between calls, so an instance is safe to reuse and to
share, including as a container singleton. The only mutable state is the
document version and encoding covered below.

### Document version and encoding

`DOMDocument` is constructed with an XML version of `1.0` and an encoding of
`utf-8`. Prefer setting them once, at construction:

```php
$validator = new Validator(version: '1.1', encoding: 'iso-8859-1');

$validator->getVersion();   // '1.1'
$validator->getEncoding();  // 'iso-8859-1'
```

`setVersion()` and `setEncoding()` exist too, but they mutate the instance. On a
validator you have shared, that change is visible to every other caller, so
reach for the constructor instead.

### Untrusted input

libxml does not resolve external entities and caps internal entity expansion, so
`SYSTEM` references are not fetched and expansion bombs are rejected rather than
exhausting memory. That protection comes from libxml's defaults rather than from
this package, and this package does not weaken them: `LIBXML_NOENT` is never
passed.

Schema paths are a different matter. `validateXMLFile()` and
`validateXMLString()` both read the XSD from the path you give them, so that
path should come from your own configuration and never from a request.

## Migrating from 1.x

2.0 replaces the error-reporting API. The old `throwError()` could not report
failures from `isXMLStringValid()` at all, and raised a fatal
`Error: Typed property ... must not be accessed before initialization` when
nothing had failed yet.

| 1.x | 2.0 |
| --- | --- |
| `$validator->throwError()` | Catch from `validateXMLFile()` / `validateXMLString()` |
| `catch (EmptyFile \| FileDoesNotExist \| ...)` | `catch (XmlValidatorException)` |
| Exceptions extend `Exception` | Exceptions extend `RuntimeException` |
| Error message string only | `InvalidXml::getErrors()` with line and column |

Also in 2.0:

- PHP 8.3 is the minimum, replacing `^8.1|^8.2|^8.3`.
- Nullable parameters are declared `?string`, so PHP 8.4 and newer no longer
  emit implicit-nullable deprecations.
- `libxml_use_internal_errors()` is restored to its previous value instead of
  being left switched on for the rest of the process.

## Development

```shell
make          # install dependencies and run every quality gate
make help     # list the available targets
```

## Treeware

You're free to use this package, but if it makes it to your production environment you are required to buy the world a tree.

It's now common knowledge that one of the best tools to tackle the climate crisis and keep our temperatures from rising above 1.5C is to <a href="https://www.bbc.co.uk/news/science-environment-48870920">plant trees</a>. If you support this package and contribute to the Treeware forest you'll be creating employment for local families and restoring wildlife habitats.

You can buy trees here [offset.earth/treeware](https://plant.treeware.earth/hyperized/xml-validator)

Read more about Treeware at [treeware.earth](http://treeware.earth)

## License
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator?ref=badge_large)
