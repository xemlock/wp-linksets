<?php

namespace wpLinksets\Thumb;

class YoutubeTest extends \PHPUnit_Framework_TestCase
{
    public function get_video_id()
    {
        return '2sj2iQyBTQs';
    }

    public function test_get_url()
    {
        $video_id = $this->get_video_id();
        $thumb = new Youtube($video_id);

        $url = sprintf('http://img.youtube.com/vi/%s/hqdefault.jpg', $video_id);
        $this->assertEquals($url, $thumb->get_url());
    }

    public function test_get_orientation()
    {
        $video_id = $this->get_video_id();
        $thumb = new Youtube($video_id);

        $orientation = $thumb->get_orientation();
        $this->assertEquals(Youtube::ORIENTATION_LANDSCAPE, $orientation);
    }
}