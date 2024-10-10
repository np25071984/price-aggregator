<?php

namespace App;

readonly class DirectoryReader
{
    private string $path;

    public function __construct(string $path) {
        $this->path = realpath($path);
    }

    public function read(array $fileTypes): array
    {
        $filesList = array_diff(scandir($this->path), ["..", "."]);
        $res = [];
        foreach ($filesList as $fileName) {
            foreach ($fileTypes as $type) {
                if (mb_strpos(mb_strtolower($fileName), "." . mb_strtolower($type)) !== false) {;
                    $res[$this->path . DIRECTORY_SEPARATOR . $fileName] = mb_ucfirst(mb_strtolower($type));
                    break;
                }
            }
        }

        return $res;
    }
}