<?php
$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);
foreach($ite as $file) {
    if ($file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (preg_match('/var\(--brand-(primary|secondary|accent),\s*#[a-fA-F0-9]{3,6}\)/', $content)) {
            $newContent = preg_replace('/var\(--brand-(primary|secondary|accent),\s*(#[a-fA-F0-9]{3,6})\)/', 'var(--brand-\-hex, \)', $content);
            file_put_contents($file->getPathname(), $newContent);
            echo 'Fixed: ' . $file->getPathname() . PHP_EOL;
        }
    }
}
