<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;
defined('GVALID') or die;

class image
{
    private $args, $width = 100, $height = 100;
    public $notValid;

    public function __construct($args = array())
    {
        global $mysql;

        $defaults = array(
            'iid' => 0,
            'src' => '/gsd-image.php',
            'alt' => '',
            'class' => '',
            'width' => '',
            'height' => '',
        );

        $this->args = array_merge($defaults, $args);
        if ($this->args['src'] && !is_file(ROOTPATH.$this->args['src'])) {
            $width = is_numeric($this->args['width']) || $this->args['width'] == 'auto' ? $this->args['width'] : $this->width;
            $height = is_numeric($this->args['height']) || $this->args['height'] == 'auto' ? $this->args['height'] : $this->height;
            $this->args['src'] = sprintf('/gsd-image.php?width=%s&height=%s', $width, $height);
        } elseif ($this->args['iid']) {
            $mysql->reset()
                ->select('extension')
                ->from('images')
                ->where('iid = ? AND deleted IS NULL')
                ->values($this->args['iid'])
                ->exec();

            $extension = $mysql->singleresult();

            if ($extension) {
                $this->args['src'] = sprintf(ASSETPATHURL.'images/%s.%s', $this->args['iid'], $extension);
            } else {
                $this->args = $defaults;
                $this->notValid = 1;
            }
        }
        $this->args['width'] = $this->args['width'] ? sprintf(' width="%s"', $this->args['width']) : '';
        $this->args['height'] = $this->args['height'] ? sprintf(' height="%s"', $this->args['height']) : '';
    }

    public function getsrc()
    {
        return $this->args['src'];
    }

    public function __toString()
    {
        $class = $this->args['class'] ? sprintf(' class="%s"', $this->args['class']) : '';
        $extra = @$this->args['max-height'] ? sprintf(' style="max-height: %spx;%s"', $this->args['max-height'], @$this->args['style']) : sprintf(' style="%s"', @$this->args['style']);

        return sprintf('<img src="%s"%s%s alt="%s"%s%s />', $this->args['src'], $this->args['width'], $this->args['height'], $this->args['alt'], $class, $extra);
    }
}
