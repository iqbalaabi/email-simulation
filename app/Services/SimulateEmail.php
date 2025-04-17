<?php
namespace App\Services;

class SimulateEmail {

    const INPUT_DOCX_FILE_PATH ="app/file-1mb.docx";


    /**
     * Read input file and simulate to make the content up to ~70MB
     *
     * @return array
     */
    public static function getTextOfEmailSimulation():array
    {
        
        $content = self::extractDocxText(storage_path(self::INPUT_DOCX_FILE_PATH));
        
        $emails = self::simulateEmailChain($content);

        return $emails;
    }

    /**
     * Read the text from DOCX file 
     *
     * @param string $filePath
     * @return string
     * @throws Exception if file any problem with file opening
     */
    private static function extractDocxText($filePath): string
    {
        $zip = new \ZipArchive();
    
        if ($zip->open($filePath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            $zip->close();
    
            if ($xml === false) {
                throw new \Exception('Failed to read document.xml from .docx');
            }
    
            // Clean up any invalid characters
            $xml = preg_replace('/[\\x00-\\x1F\\x80-\\x9F]/u', '', $xml);
    
            // Load the XML safely
            $dom = new \DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = false;
    
            libxml_use_internal_errors(true);
            if (!$dom->loadXML($xml)) {
                throw new \Exception('Failed to parse document.xml');
            }
            libxml_clear_errors();
    
            $text = '';
    
            // Collect all text nodes
            $nodeList = $dom->getElementsByTagNameNS('*', 't');
            foreach ($nodeList as $node) {
                $text .= $node->nodeValue . "\n";
            }
    
            return trim($text);
        }
    
        throw new \Exception('Unable to open .docx file');
    }

    /**
     * Simulate over text to make a larger PDF file
     *
     * @param string $content
     * @param integer $count
     * @return array
     */
    private static function simulateEmailChain(string $content, int $count = 25): array
    {
        $emails = [];
        $sender = 'person1@gmail.com';
        $receiver = 'person2@gmail.com';
        $subject = 'RE-Email Simulation to Generate PDF';

        for ($i = 1; $i <= $count; $i++) {
            $emails[] = [
                'from' => $i % 2 === 0 ? $receiver : $sender,
                'to' => $i % 2 === 0 ? $sender : $receiver,
                'subject' => $subject,
                'body' => $content,
                'timestamp' => now()->addMinutes($i)->format('Y-m-d H:i:s'),
            ];
        }

        return $emails;
    }
}