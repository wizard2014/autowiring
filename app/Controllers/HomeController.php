<?php

namespace App\Controllers;

use App\Config\Config;

class HomeController
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function index()
    {
        return [
            $this->config->get('app.name')
        ];
    }
}
