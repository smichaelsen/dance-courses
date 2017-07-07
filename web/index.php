<?php

require_once(__DIR__ . '/../vendor/autoload.php');

$bowl = new \Smichaelsen\SaladBowl\Bowl(__DIR__ . '/../');

$bowl->getServer()->listen();
