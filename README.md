b2rPHP: ComppositionGenerator
=============================

Easy to generate composition php source

- [CHANGELOG](CHANGELOG.md)

### Usage

```php
use b2r\Component\Composition\Generator;

$gen = new Generator();
$gen->name('PDOWrapper')   # Set composition class|tarit name
    ->namespace('b2r\PDO') # Set namespace
    // ->asTrait()         # Output as trait
    ->target(PDO::class)   # Set composition target class
    ->property('pdo')      # set composition property name
    ->aliases([            # Define aliases
        'lastId' => 'lastInsertId',
        'begin'  => 'beginTransaction',
    ])
    ->excludes(['quote', 'query', 'exec']); # Exclude methods

echo $gen;
// $gen->save('PDOWrapper.php'); // Save to file
```
