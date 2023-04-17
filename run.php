<?php
/**
 * Simple updates of WP:SNOOKS players.
 */
// lib
require_once './PlayersGenerator.php';
require_once './Logger.php';

// setup
$basePath = './wd/';

// init
$gen = new PlayersGenerator($basePath);
$console = new Logger();

// update
$console->log("Updating...");
$gen->update();
$console->log("Update done");
