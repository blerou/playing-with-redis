<?php

class RedisTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function setSomethingAndGetItBack()
    {
        $sock = fsockopen("127.0.0.1", 6379);
        $this->assertThat($sock, $this->isType("resource"));

        $this->writeCmd($sock, ["flushdb"]);
        $res = fread($sock, 1000);
        $this->assertEquals("+OK\r\n", $res);

        $this->writeCmd($sock, ["set", "foo", "bar"]);
        $res = fread($sock, 1000);
        $this->assertEquals("+OK\r\n", $res);

        $this->writeCmd($sock, ["get", "foo"]);
        $res = fread($sock, 1000);
        $this->assertEquals($this->bulkString("bar"), $res, "$res something different");
    }

    /**
     * @test
     */
    public function getSomethingThatIsNotSetYet()
    {
        $sock = fsockopen("127.0.0.1", 6379);

        $this->writeCmd($sock, ["flushdb"]);
        $res = fread($sock, 1000);

        $this->writeCmd($sock, ["get", "undefined"]);
        $res = fread($sock, 1000);
        $this->assertEquals("$-1\r\n", $res);
    }

    private function writeCmd($sock, $args)
    {
        fwrite($sock, "*".count($args)."\r\n");
        foreach ($args as $arg) $this->writeBulkString($sock, $arg);
    }

    private function writeBulkString($sock, $str)
    {
        fwrite($sock, $this->bulkString($str));
    }

    private function bulkString($str)
    {
        return "$".strlen($str)."\r\n$str\r\n";
    }
}
