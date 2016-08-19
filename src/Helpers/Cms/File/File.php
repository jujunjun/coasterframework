<?php namespace CoasterCms\Helpers\Cms\File;

class File
{

    public static function insertAtLine($src, array $insertions)
    {
        $fileHandle = fopen($src, 'r') or die("couldn't open $src");
        $lineNo = 1;
        $newLines = [];
        $insertLines = array_keys($insertions);
        while (($currentLine = fgets($fileHandle, 4096)) !== false) {

            if (in_array($lineNo, $insertLines)) {
                foreach ($insertions[$lineNo] as $line) {
                    $newLines[] = $line . "\r\n";
                }
                $newLines[] = $currentLine;
            } else {
                $newLines[] = $currentLine;
            }
            $lineNo++;
        }
        fclose($fileHandle);

        $fileHandle = fopen($src, 'w') or die("couldn't open $src");
        foreach ($newLines as $newLine) {
            fwrite($fileHandle, $newLine);
        }
        fclose($fileHandle);
    }

    public static function replaceString($src, $match, $replace)
    {
        $fileContent = file_get_contents($src);
        $str = str_replace($match, $replace, $fileContent);
        file_put_contents($src, $str);
    }

    public static function getEnvContents()
    {
        try {
            $envFileContents = file_get_contents(base_path('.env'));
            if (!trim($envFileContents)) {
                $envFileContents = file_exists(base_path('.env.example')) ? file_get_contents(base_path('.env.example')) : '';
            }
        } catch(\Exception $e) {
            $envFileContents = '';
        }
        return $envFileContents;
    }

}