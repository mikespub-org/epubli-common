<?php

namespace Epubli\Common\Tools;

class FileTools
{
    /**
     * create directory recursively and do not rely on recursive parameter of mkdir
     * (which does not set chmod for every but only for the last path node, resulting in possibly incoherent directory permissions)
     * @param $dir
     * @param int $mode
     * @return bool
     * @throws \Exception
     */
    public static function mkdirRecursive($dir, $mode = 0o777)
    {
        $permissionErrorHandler = function ($error = 0, $text = null, $file = null, $line = null) use ($dir, $mode) {
            if ($error instanceof \Exception) {
                throw $error;
            } else {
                throw new \Exception($text ?: "ERROR while creating directory [$dir] with mode $mode.", $error);
            }
        };
        set_error_handler($permissionErrorHandler, ~E_NOTICE & ~E_WARNING);

        /* get all path nodes for target folder */
        $dir_nodes = explode(DIRECTORY_SEPARATOR, (string) $dir);

        clearstatcache();
        if (!is_dir($dir)) {
            $basedir = '';
            foreach ($dir_nodes as $dir_node) {
                $basedir .= $dir_node . DIRECTORY_SEPARATOR;
                if (!is_dir($basedir)) {
                    $result = mkdir($basedir, $mode);
                    if (!$result) {
                        $permissionErrorHandler(0, "ERROR while creating directory [$basedir] with mode $mode: mkdir() returned false.");
                    }
                }
            }
        }
        if (!is_writable($dir)) {
            if (!chmod($dir, $mode)) {
                $permissionErrorHandler(0, "ERROR while chmodding directory [$dir] with mode $mode");
            }
        }
        restore_error_handler();
        return is_dir($dir) && is_writable($dir);
    }
}
