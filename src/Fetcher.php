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

        $out = sprintf('<?php return %s', var_export($this->strings, true));

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
        preg_match_all('/(?:\@lang|App\.i18n\.get)\((["\'])([^\1]*?)\1\)/', $content, $matches);

        return $matches[2];
    }
}
