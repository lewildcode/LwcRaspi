<?php
namespace Lwc\Raspi\Gpio\Adapter;

interface AdapterInterface
{
    public function allocate($pin, $direction);
    public function release($pin);
    public function get($pin);
    public function set($pin);
    public function clear($pin);
}