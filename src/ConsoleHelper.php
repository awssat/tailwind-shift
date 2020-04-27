<?php

namespace Awssat\TailwindShift;

use Symfony\Component\Console\Output\OutputInterface;

class ConsoleHelper
{
    protected $updater;
    protected $output;
    protected $recursive = false;
    protected $overwrite;
    protected $extensions;

    public function __construct(OutputInterface $output, $recursive, $overwrite, $extensions)
    {
        $this->updater = new Updater();
        $this->output = $output;
        $this->recursive = $recursive;
        $this->overwrite = $overwrite;
        $this->extensions = $extensions;
    }

    public function folderConvert($folderPath)
    {
        $this->output->writeln('<question>Start Converting Folder: </question>'.$folderPath);

        if ($this->recursive) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $folderPath,
                    \RecursiveDirectoryIterator::SKIP_DOTS
                ),
                \RecursiveIteratorIterator::SELF_FIRST,
                \RecursiveIteratorIterator::CATCH_GET_CHILD
            );
        } else {
            $iterator = new \DirectoryIterator($folderPath);
        }

        foreach ($iterator as $path => $directory) {
            $extensions = explode('.', $directory);
            $extension = end($extensions);
            if ($directory->isFile() && $this->isConvertableFile($extension)) {
                $this->fileConvert($directory->getRealPath(), $extension);
            }
        }
    }

    public function fileConvert($filePath, $extension)
    {
        //just in case
        $filePath = realpath($filePath);

        if (!is_file($filePath)) {
            $this->output->writeln('<comment>Couldn\'t convert: </comment>'.basename($filePath));

            return;
        }

        $content = file_get_contents($filePath);

        $lastDotPosition = strrpos($filePath, '.');

        if ($lastDotPosition !== false && !$this->overwrite) {
            $newFilePath = substr_replace($filePath, '.tw', $lastDotPosition, 0);
        } elseif (!$this->overwrite) {
            $newFilePath = $filePath.'.tw';
        } else {
            // Set the new path to the old path to make sure we overwrite it
            $newFilePath = $filePath;
        }

        $newContent = $this->updater
                    ->setContent($content)
                    ->setFileExtension($extension)
                    ->convert()
                    ->get();

        if ($content !== $newContent) {
            $this->output->writeln('<info>Converted: </info>'.basename($newFilePath));

            file_put_contents($newFilePath, $newContent);
        } else {
            $this->output->writeln('<comment>Nothing to convert: </comment>'.basename($filePath));
        }
    }

    public function codeConvert($code)
    {
        $convertedCode = $this->updater
                    ->setContent($code)
                    ->convert()
                    ->get();

        $this->output->writeln('<info>Converted Code: </info>'.$convertedCode);
    }

    public function fixTailwindConfig($filePath)
    {
        $originalContent = file_get_contents($filePath);

        $fixedContent = (new ConfigFileFixer($originalContent))
                    ->fix()
            ->get();

        $lastDotPosition = strrpos($filePath, '.');

        if ($lastDotPosition !== false && !$this->overwrite) {
            $newFilePath = substr_replace($filePath, '.tw', $lastDotPosition, 0);
        } elseif (!$this->overwrite) {
            $newFilePath = $filePath.'.tw';
        } else {
            // Set the new path to the old path to make sure we overwrite it
            $newFilePath = $filePath;
        }

        file_put_contents($newFilePath, $fixedContent);

        $this->output->writeln('<info>Tailwind config file has been fixed.</info>');
    }

    /**
     * Check whether a file is convertable or not based on its extension.
     *
     * @param string $extension
     */
    protected function isConvertableFile($extension)
    {
        return in_array($extension, $this->extensions);
    }
}
