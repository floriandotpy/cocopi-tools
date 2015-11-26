<?php

class Fetcher
{

    protected $strings = [];

    protected $extensions = ["php", "md", "html", "js", "tag"];

    public function fetchFrom($path)
    {
        $dir_iterator = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $file) {
            if (in_array($file->getExtension(), $this->extensions)) {

                echo "processing ".$file." ...\n";
                if($strings = $this->stringsFromFile($file->getPath().'/'.$file->getFilename())) {
                    $this->addStrings($strings);
                }
            }
        }

        return $this;
    }

    public function writeTo($path)
    {
        $this->strings['@meta'] = [
            'language' => 'English',
            'author'   => 'Cocopi',
            'date' => [
                'shortdays'   => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'longdays'    => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'shortmonths' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'longmonths'  => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
            ]
        ];

        ksort($this->strings);


        $out = sprintf('<?php return %s', $this->var_export54($this->strings));

        file_put_contents(rtrim($path, '/').'/en.php', $out);

        echo "\n\nDONE.\n\n";
    }

    protected function addStrings($strings)
    {
        foreach ($strings as $string) {
            $this->strings[$string] = $string;
        }
    }

    protected function stringsFromFile($path)
    {
        $content = file_get_contents($path);
        preg_match_all('/(?:\@lang|App\.i18n\.get|App\.ui\.notify)\((["\'])([^\1]*?)\1/', $content, $matches);

        return $matches[2];
    }

    /**
     * src: http://stackoverflow.com/questions/24316347/how-to-format-var-export-to-php5-4-array-syntax
     */
    protected function var_export54($var, $indent="")
    {
        switch (gettype($var)) {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                         . ($indexed ? "" : $this->var_export54($key) . " => ")
                         . $this->var_export54($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "TRUE" : "FALSE";
            default:
                return var_export($var, TRUE);
        }
    }
}
