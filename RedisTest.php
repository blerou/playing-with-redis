<?php

class RedisTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $errno = $errstr = null;
        $sock = fsockopen("127.0.0.1", 6379, $errno, $errstr, 10);
        $this->assertThat($sock, $this->isType("resource"));

        fwrite($sock, "*3\r\n");
        fwrite($sock, "$3\r\n");
        fwrite($sock, "set\r\n");
        fwrite($sock, "$3\r\n");
        fwrite($sock, "foo\r\n");
        fwrite($sock, "$3\r\n");
        fwrite($sock, "bar\r\n");
        $res = fread($sock, 1000);
        $this->assertEquals("+OK\r\n", $res);

        fwrite($sock, "*2\r\n");
        fwrite($sock, "$3\r\n");
        fwrite($sock, "get\r\n");
        fwrite($sock, "$3\r\n");
        fwrite($sock, "foo\r\n");
        $res = fread($sock, 1000);
        $this->assertEquals("$3\r\nbar\r\n", $res, "$res something different");
    }
}
