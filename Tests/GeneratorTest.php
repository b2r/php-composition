<?php

namespace b2r\Component\Composition\Tests;

require_once __DIR__ . '/boot.php';

use b2r\Component\Composition\Tests\TestCase;
use b2r\Component\Composition\Generator;

class GeneratorTest extends TestCase
{
    public function testPDOWrapper()
    {
        $gen = new Generator();
        $gen->name('PDOWrapper')   # Set composition class|tarit name
            ->namespace('b2r\PDO') # Set namespace
            ->target(\PDO::class)  # Set composition target class
            ->property('pdo')      # set composition property name
            ->excludes(['quote', 'query', 'exec']); # Exclude methods
        
        $code = $gen->asClass()->generate();
        $this->is(strpos($code, 'class PDOWrapper') !== false);
        $this->is(strpos($code, 'trait PDOWrapper') === false);

        $gen->asTrait()
            ->aliases([            # Define aliases
                'lastId' => 'lastInsertId',
                'begin'  => 'beginTransaction',
            ]);
        $code = (string)$gen; // Invoke `__toString()`
        $this->is(strpos($code, 'class PDOWrapper') === false);
        $this->is(strpos($code, 'trait PDOWrapper') !== false);

        $filename = __DIR__ . '/PDOWrapper.php';
        $gen->save($filename);
        $this->is(is_readable($filename));
        unlink($filename);
    }

    public function testCall()
    {
        $gen = new Generator();
        $this->is(null, $gen->hoge());
    }
}
