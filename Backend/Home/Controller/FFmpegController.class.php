<?php
namespace Home\Controller;

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate;
use FFMpeg\FFProbe;
use FFMpeg\Format;
use FFMpeg\Filters;
use Think\Controller;

require 'vendor/autoload.php';

class FFmpegController extends Controller
{

    public function index()
    {
        echo 'FFmpeg';
    }

    //Basic Usageerrdsadsa
    public function createFFmpeg()
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'] . __ROOT__ . '/Uploads/FFmpegVideo/';
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($path . 'out.mpg');
        $video->filters()
            ->resize(new Coordinate\Dimension(320, 240))
            ->synchronize();
        $video->frame(Coordinate\TimeCode::fromSeconds(10))
            ->save($path . 'frame.jpg');
        $video->save(new Format\Video\X264(), 'export-x264123.mp4');
        var_dump($video);
    }

    //Basic Usage
    public function movie()
    {
        $videoPath = 'http://' . $_SERVER['HTTP_HOST'] . __ROOT__ . '/Uploads/FFmpegVideo/out.mpg';
        $movie = new \ffmpeg_movie($videoPath, true);
        var_dump($movie->getDuration());
    }

    /**
     * 【测试通过！！！！！！！！！！！！！！！！！！！！】
     *  MP4 to MP3
     *  如果命令可以执行，运行以下行PHP代码配置FFMpeg包装器，并使用它为您的应用程序创建真棒视频功能。
     */
    public function mp4_to_mp3()
    {
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => 'd:\ffmpeg\bin\ffmpeg.EXE',
            'ffprobe.binaries' => 'd:\ffmpeg\bin\ffprobe.exe',
            'timeout' => 3600,
            'ffmpeg.threads' => 12,
        ]);
        $MP4Path = 'http://' . $_SERVER['HTTP_HOST'] . __ROOT__ . '/Uploads/FFmpegVideo/ffmpeg_mp4.mp4';
        //Open your video file
        $video = $ffmpeg->open($MP4Path);
        // Set an audio format
        $audio_format = new Format\Audio\Mp3();
        // Extract the audio into a new file
        $video->save($audio_format, 'ffmpeg_mp3.mp3');
        var_dump($video);
    }

    /**
     * 【测试通过！！！！！！！！！！！！！！！！！！！！】
     * Extracting image 提取图像
     * 此代码返回一个FFMpeg\Media\Frame对应于第二实例42.您可以通过任何FFMpeg\Coordinate\TimeCode作为参数
     */
    public function extracting_image()
    {
        $ffmpeg = FFMpeg::create();
        $MP4Path = 'F:\Tinywan\Video\ImageOut.mpg';
        //Open your video file
        $video = $ffmpeg->open($MP4Path);
        $rand = mt_rand(000, 222);
        //Set an image cut time
        $frame = $video->frame(Coordinate\TimeCode::fromSeconds($rand));
        // Extract the image into a new file
        $images = $frame->save('ffmpeg_mp4_' . $rand . '.jpg');
        var_dump($images);
    }

    /**
     * 将视频旋转到给定的角度。Rotates a video to a given angle.
     */
    public function video_rotate()
    {
        $ffmpeg = FFMpeg::create();
        $MP4Path = 'http://' . $_SERVER['HTTP_HOST'] . __ROOT__ . '/Uploads/FFmpegVideo/ffmpeg_mp4.mp4';
        //Open your video file
        $video = $ffmpeg->open($MP4Path);
        //The resize filter takes three parameters :
        $format = $video->filters()->rotate(Filters\Video\RotateFilter::ROTATE_90);
        // Extract the image into a new file
        $video->save($format, 'ffmpeg_mp4_90.mp4');
        var_dump($video);
    }

    /**
     * Watermark
     * 使用给定图像对视频进行水印。
     */
    public function videoWatermark()
    {
        $ffmpeg = FFMpeg::create();
        $MP4Path = 'F:\Tinywan\Video\fooimage2.avi';
        $watermarkPath = 'F:\Tinywan\Video\00018.jpg';
        $video = $ffmpeg->open($MP4Path);
        $watermark = $video->filters()->watermark($watermarkPath, array(
                'position' => 'relative',
                'bottom' => 50,
                'right' => 50,
            ));
        // Extract the image into a new file
        $video->save($watermark,'fooimage2_Watermark.avi');
        var_dump($video);
    }

    /**
     * Transcoding
     * 您可以使用音频转码FFMpeg\Media\Audio:save的方法。您将通过一个FFMpeg\Format\FormatInterface针对
     */
    public function Transcoding()
    {
        $ffmpeg = FFMpeg::create();
        $MP4Path = 'http://' . $_SERVER['HTTP_HOST'] . __ROOT__ . '/Uploads/FFmpegVideo/ffmpeg_mp4.mp4';

        //Open your video file
        $video = $ffmpeg->open($MP4Path);
        //The resize filter takes three parameters :
        $format = $video->filters()->watermark($watermarkPath, array('position' => 'relative', 'bottom' => 50, 'right' => 50));
        // Extract the image into a new file
        $video->save($format, 'ffmpeg_mp4_Watermark.mp4');
        var_dump($video);
    }

    /**
     * 的一种格式实现FFMpeg\Format\FormatInterface。
     * 要保存到视频文件，使用FFMpeg\Format\VideoInterface和FFMpeg\Format\AudioInterface音频文件。
     */
    public function Formats()
    {
        $ffmpeg = FFMpeg::create();
        $MP4Path = 'http://' . $_SERVER['HTTP_HOST'] . __ROOT__ . '/Uploads/FFmpegVideo/ffmpeg_mp4.mp4';
        $video = $ffmpeg->open($MP4Path);
        //The resize filter takes three parameters :
        $format = new Format\Video\X264();
        $format->on('progress', function ($video, $format, $percentage) {
            echo "$percentage % transcoded";
        });
        // Extract the image into a new file
        $video->save($format, 'video.avi');
        var_dump($video);
    }

    /**
     * 【测试通过！！！！！！！！！！！！！！！！！！！！】
     * 获取一个视频的编码格式 h264
     * 获取一个视频的时长
     */
    public function FFProbe()
    {
        $ffprobe = FFProbe::create();
        $MP4Path = 'http://' . $_SERVER['HTTP_HOST'] . __ROOT__ . '/Uploads/FFmpegVideo/ffmpeg_mp4.mp4';
        $encodeFormat = $ffprobe->streams($MP4Path)->videos()->first()->get('codec_name');
        echo '视频格式为：' . $encodeFormat . "<br/>";
        $duration = $ffprobe->format($MP4Path)->get('duration');
        echo '视频的时长:' . $duration . 's';
    }


}