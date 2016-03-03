<?php

namespace VersionPress\Utils;

class ProcessUtils {

    /**
     * Similar to built-in escapeshellarg() but allows to pass OS according to which
     * the escaping should be done (Linux - quotes, Windows - double quotes).
     *
     * @param $arg
     * @param string|null $os "windows", "linux" or null to use the OS detection in escapeshellarg()
     * @return mixed|string
     */
    public static function escapeshellarg($arg, $os = null) {

        if (!$os) {
            return escapeshellarg($arg);
        }

        if ($os == "windows") {
            return self::_drush_escapeshellarg_windows($arg);
        } else {
            return self::_drush_escapeshellarg_linux($arg);
        }

    }

    /**
     * Linux shell escaping from Drush:
     * http://drupalcontrib.org/api/drupal/contributions!drush!includes!exec.inc/function/_drush_escapeshellarg_linux/7
     *
     * @param $arg
     * @return mixed|string
     */
    public static function _drush_escapeshellarg_linux($arg) {
        // For single quotes existing in the string, we will "exit"
        // single-quote mode, add a \' and then "re-enter"
        // single-quote mode.  The result of this is that
        // 'quote' becomes '\''quote'\''
        $arg = preg_replace('/\'/', '\'\\\'\'', $arg);

        // Replace "\t", "\n", "\r", "\0", "\x0B" with a whitespace.
        // Note that this replacement makes Drush's escapeshellarg work differently
        // than the built-in escapeshellarg in PHP on Linux, as these characters
        // usually are NOT replaced. However, this was done deliberately to be more
        // conservative when running _drush_escapeshellarg_linux on Windows
        // (this can happen when generating a command to run on a remote Linux server.)
        $arg = str_replace(array("\t", "\n", "\r", "\0", "\x0B"), ' ', $arg);

        // Add surrounding quotes.
        $arg = "'" . $arg . "'";

        return $arg;
    }

    /**
     * Windows shell escaping from Drush:
     * http://drupalcontrib.org/api/drupal/contributions!drush!includes!exec.inc/function/_drush_escapeshellarg_windows/7

     * @param $arg
     * @return mixed|string
     */
    public static function _drush_escapeshellarg_windows($arg) {
        // Double up existing backslashes
        $arg = preg_replace('/\\\/', '\\\\\\\\', $arg);

        // Double up double quotes
        $arg = preg_replace('/"/', '""', $arg);

        // Double up percents.
        $arg = preg_replace('/%/', '%%', $arg);

        // Add surrounding quotes.
        $arg = '"' . $arg . '"';

        return $arg;
    }
}
