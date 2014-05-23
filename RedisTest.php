<?php

class RedisTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $errno = $errstr = null;
        $sock = fsockopen("127.0.0.1", 6379, $errno, $errstr, 10);
        $this->assertThat($sock, $this->isType("resource"));

        $this->writeCmd($sock, ["set", "foo", "bar"]);
        $res = fread($sock, 1000);
        $this->assertEquals("+OK\r\n", $res);

        $this->writeCmd($sock, ["get", "foo"]);
        $res = fread($sock, 1000);
        $this->assertEquals($this->bulkString("bar"), $res, "$res something different");
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
