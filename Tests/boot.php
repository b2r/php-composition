<?php

namespace b2r\Component\Composition\Tests;

require_once __DIR__ . '/../vendor/autoload.php';

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function is()
    {
        $args = func_get_args();
        if (count($args) === 2) {
            $this->assertEquals($args[0], $args[1]);
        } else {
            foreach ($args as $value) {
                $this->assertTrue($value);
            }
        }
    }

    /**
     * Dump values
     */
    public function dump()
    {
        ob_start();
        var_dump(...func_get_args());
        $result = ob_get_contents();
        ob_end_clean();

        // Minify
        $result = preg_replace("/=>\n\s+/", '=> ', $result);
        $result = preg_replace("/{\s+}/", '{}', $result);

        // Display
        $sep = "\n" . str_repeat('-', 60). "\n";
        echo $sep;
        echo sprintf("Dump [%s]\n", get_called_class());
        echo $result;
        echo $sep;
    }

    public function testIs()
    {
        $this->is(true);
        $this->is(1, 1);
    }
}
