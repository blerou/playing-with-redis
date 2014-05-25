<?php

/*
 * TODOs:
 *  + extract "run command and give back the result"
 *  - do something with error case (with return values in general)
 *  - extract general patterns/constants?
 *  - handle different response types based on first byte
 */

class RedisTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function setSomethingAndGetItBack()
    {
        $sock = $this->open();
        $this->assertThat($sock, $this->isType("resource"));

        $res = $this->writeCmd($sock, ["flushdb"]);
        $okResponse = "+OK\r\n";
        $this->assertEquals($okResponse, $res);

        $res = $this->writeCmd($sock, ["set", "foo", "bar"]);
        $this->assertEquals($okResponse, $res);

        $res = $this->writeCmd($sock, ["get", "foo"]);
        $this->assertEquals($this->bulkString("bar"), $res, "$res something different");
    }

    /**
     * @test
     */
    public function getSomethingThatIsNotSetYet()
    {
        $sock = $this->open();

        $this->writeCmd($sock, ["flushdb"]);

        $res = $this->writeCmd($sock, ["get", "undefined"]);
        $this->assertEquals("$-1\r\n", $res);
    }

    private function writeCmd($sock, $args)
    {
        fwrite($sock, "*".count($args)."\r\n");
        foreach ($args as $arg) $this->writeBulkString($sock, $arg);
        return fread($sock, 1000);
    }

    private function writeBulkString($sock, $str)
    {
        fwrite($sock, $this->bulkString($str));
    }

    private function bulkString($str)
    {
        return "$".strlen($str)."\r\n$str\r\n";
    }

    /**
     * @return resource
     */
    private function open()
    {
        return fsockopen("127.0.0.1", 6379);
    }
}
