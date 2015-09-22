<?php

namespace wpLinksets\Link;

class YoutubeTest extends \PHPUnit_Framework_TestCase
{
    public function get_video_id()
    {
        return '2sj2iQyBTQs';
    }

    public function test_construct()
    {
        $video_id = $this->get_video_id();
        $link = new Youtube($video_id);
        $this->assertEquals($video_id, $link->get_video_id());

        $link = new Youtube('');
        $this->assertEquals('', $link->get_video_id());

        $link = new Youtube();
        $this->assertNull($link->get_video_id());
    }

    public function test_get_type()
    {
        $link = new Youtube();
        $this->assertEquals(Youtube::TYPE, $link->get_type());
    }

    public function test_get_thumb()
    {
        $video_id = $this->get_video_id();
        $link = new Youtube($video_id);

        $thumb = $link->get_thumb();
        $this->assertInstanceOf('\wpLinksets\Thumb\Youtube', $thumb);
    }

    public function test_extract_video_id()
    {
        $video_id = $this->get_video_id();

        $test_video_id = $video_id;
        $this->assertEquals($video_id, Youtube::extract_video_id($test_video_id));

        $test_video_id = sprintf('https://www.youtube.com/watch?v=%s', $video_id);
        $this->assertEquals($video_id, Youtube::extract_video_id($test_video_id));

        $test_video_id = sprintf('https://www.youtube.com/v/%s', $video_id);
        $this->assertEquals($video_id, Youtube::extract_video_id($test_video_id));

        $test_video_id = sprintf('https://www.youtube.com/embed/%s', $video_id);
        $this->assertEquals($video_id, Youtube::extract_video_id($test_video_id));

        $test_video_id = sprintf('https://youtu.be/%s', $video_id);
        $this->assertEquals($video_id, Youtube::extract_video_id($test_video_id));
    }
}