<?php

namespace tests;

use PHPUnit\Framework\TestCase;

class AnalyzerTest extends TestCase
{
    //Проверяем на возвращение с нулевым порогом
    public function testSuccessfulRequest()
    {
        $testInput = "192.168.32.181 - - [14/06/2017:13:32:26 +1000] \"PUT /rest/v1.4/documents?zone=default&_rid=123456 HTTP/1.1\" 200 2 15.712345 \"-\" \"@list-item-updater\" prio:0\n";
        $testFile = tempnam(sys_get_temp_dir(), 'test_input');
        file_put_contents($testFile, $testInput);

        $expectedOutput = "13:32:26\t13:32:26\t0.0\n";

        $testDirectory = dirname(__FILE__);
        $analyzePath = realpath($testDirectory . '/../analyze.php');

        $command = "type {$testFile} | php {$analyzePath} -u 51 -t 1";
        $output = shell_exec($command);

        unlink($testFile);

        $this->assertEquals(trim($expectedOutput), trim($output));
    }
//Проверяем на пустой результат
    public function testNoResult()
    {

        $testInput = "192.168.32.181 - - [14/06/2017:13:32:26 +1000] \"PUT /rest/v1.4/documents?zone=default&_rid=123456 HTTP/1.1\" 200 2 15.712345 \"-\" \"@list-item-updater\" prio:0\n" .
            "192.168.32.181 - - [14/06/2017:13:33:15 +1000] \"PUT /rest/v1.4/documents?zone=default&_rid=7ae28555 HTTP/1.1\" 200 2 25.251219 \"-\" \"@list-item-updater\" prio:0\n";
        $testFile = tempnam(sys_get_temp_dir(), 'test_input');
        file_put_contents($testFile, $testInput);

        $expectedOutput = "";

        $testDirectory = dirname(__FILE__);
        $analyzePath = realpath($testDirectory . '/../analyze.php');

        $command = "type {$testFile} | php {$analyzePath} -u 51 -t 100";
        $output = shell_exec($command);

        unlink($testFile);

        $this->assertEquals(trim($expectedOutput), trim($output));
    }
//Проверяем на получение положительного результата
    public function testOneSuccessRequests()
    {

        $testInput = "192.168.32.181 - - [14/06/2017:13:32:26 +1000] \"PUT /rest/v1.4/documents?zone=default&_rid=123456 HTTP/1.1\" 500 2 15.712345 \"-\" \"@list-item-updater\" prio:0\n" .
            "192.168.32.181 - - [14/06/2017:13:33:15 +1000] \"PUT /rest/v1.4/documents?zone=default&_rid=7ae28555 HTTP/1.1\" 200 2 25.251219 \"-\" \"@list-item-updater\" prio:0\n";
        $testFile = tempnam(sys_get_temp_dir(), 'test_input');
        file_put_contents($testFile, $testInput);


        $expectedOutput = "13:32:26\t13:33:15\t50.0";


        $testDirectory = dirname(__FILE__);
        $analyzePath = realpath($testDirectory . '/../analyze.php');


        $command = "type {$testFile} | php {$analyzePath} -u 100 -t 30"; // Настройки, чтобы пороги не превышены
        $output = shell_exec($command);


        unlink($testFile);

        $this->assertEquals(trim($expectedOutput), trim($output));
    }





}
