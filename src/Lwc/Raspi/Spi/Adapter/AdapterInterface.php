<?php
namespace Lwc\Raspi\Spi\Adapter;

interface AdapterInterface
{

    public function writeCommand ($command);

    public function writeData ($data);
}