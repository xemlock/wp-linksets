<?php

namespace wpLinksets\Link;

class YoutubeTest extends \PHPUnit_Framework_TestCase
{
    public function test_set_video_id()
    {
        $video_id = '2sj2iQyBTQs';

        $link = new Youtube();
        $link->set_video_id($video_id);
        $this->assertEquals($video_id, $link->get_video_id());

        $link = new Youtube();
        $link->set_video_id(sprintf('https://www.youtube.com/watch?v=%s', $video_id));
        $this->assertEquals($video_id, $link->get_video_id());

        $link = new Youtube();
        $link->set_video_id(sprintf('https://www.youtube.com/v/%s', $video_id));
        $this->assertEquals($video_id, $link->get_video_id());

        $link = new Youtube();
        $link->set_video_id(sprintf('https://www.youtube.com/embed/%s', $video_id));
        $this->assertEquals($video_id, $link->get_video_id());

        $link = new Youtube();
        $link->set_video_id(sprintf('https://youtu.be/%s', $video_id));
        $this->assertEquals($video_id, $link->get_video_id());
    }
}