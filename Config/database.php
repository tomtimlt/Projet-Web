<?php
/**
 * Configuration de la base de données
 */
return [
    'host' => 'localhost',
    'dbname' => 'projet_web',
    'username' => 'webuser',
    'password' => 'motdepasse123',
    'charset' => 'utf8',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
