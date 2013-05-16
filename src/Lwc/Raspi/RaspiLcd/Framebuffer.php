<?php
namespace Lwc\Raspi\RaspiLcd;

class Framebuffer
{

    protected $buffer;

    protected $width;

    protected $height;

    public function __construct ($width, $height)
    {
        $this->buffer = array();
        $this->width = $width;
        $this->height=  $height;
        $this->init();
    }

    public function init ()
    {
        for ($x = 0; $x < $this->width; $x ++) {
            $this->buffer[$x] = array();
            for ($y = 0; $y < $this->height; $y ++) {
                $this->buffer[$x][$y] = 0;
            }
        }
    }

    public function set ($x, $y)
    {
        $this->buffer[$x][$y] = 1;
    }

    public function clear ($x, $y)
    {
        $this->buffer[$x][$y] = 0;
    }
    
    public function get($x, $y) {
        return $this->buffer[$x][$y];
    }
}