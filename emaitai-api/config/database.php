<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Configuration Supabase
$supabase_url = $_ENV['SUPABASE_URL'];
$supabase_key = $_ENV['SUPABASE_SERVICE_KEY'];

// Extraction du host depuis l'URL Supabase
$parsed_url = parse_url($supabase_url);
$host = $parsed_url['host'];

// Connexion PostgreSQL
$dsn = "pgsql:host={$host};port=5432;dbname=postgres;sslmode=require";

try {
    $pdo = new PDO($dsn, 'postgres', $supabase_key, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
