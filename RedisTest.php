<?php

class RedisTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $errno = $errstr = null;
        $sock = fsockopen("127.0.0.1", 6379, $errno, $errstr, 10);
        $this->assertThat($sock, $this->isType("resource"));

        fwrite($sock, "*3\r\n");
        $this->writeBulkString($sock, "set");
        $this->writeBulkString($sock, "foo");
        $this->writeBulkString($sock, "bar");
        $res = fread($sock, 1000);
        $this->assertEquals("+OK\r\n", $res);

        fwrite($sock, "*2\r\n");
        $this->writeBulkString($sock, "get");
        $this->writeBulkString($sock, "foo");
        $res = fread($sock, 1000);
        $this->assertEquals("$3\r\nbar\r\n", $res, "$res something different");
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
