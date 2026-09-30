<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('structure')]
#[Description('Display only the folder structure of the project')]
class ShowStructure extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $root = base_path();
        $exclude = ['vendor', 'node_modules', 'storage', '.git', 'bootstrap/cache'];

        $this->line('Project Folder Structure:');
        $this->line('');

        $output = $this->buildTree($root, '', $exclude, 0);
        $this->line($output);
    }

    /**
     * Recursively build a directory tree, showing ONLY folders.
     */
    private function buildTree($dir, $prefix = '', $exclude = [], $depth = 0)
    {
        // Limit the depth to 2 levels (change this number if you want more/less)
        if ($depth > 2) {
            return '';
        }

        // Scan the directory
        $items = array_diff(scandir($dir), ['.', '..']);

        // Filter out excluded folders AND filter out any files (keep only directories)
        $items = array_filter($items, function ($item) use ($dir, $exclude) {
            $path = $dir.DIRECTORY_SEPARATOR.$item;

            // Keep it ONLY if it is a directory AND not in the excluded list
            return is_dir($path) && ! in_array($item, $exclude);
        });

        sort($items);

        $count = count($items);
        $output = '';
        $i = 0;

        foreach ($items as $item) {
            $i++;
            $isLast = ($i == $count);
            $path = $dir.DIRECTORY_SEPARATOR.$item;

            // Since we already filtered files, we only have directories here
            $output .= $prefix.($isLast ? '└── ' : '├── ').$item.'/'."\n";
            $output .= $this->buildTree($path, $prefix.($isLast ? '    ' : '│   '), $exclude, $depth + 1);
        }

        return $output;
    }
}
