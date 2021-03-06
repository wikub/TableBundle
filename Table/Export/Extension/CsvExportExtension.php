<?php

namespace EMC\TableBundle\Table\Export\Extension;

use EMC\TableBundle\Table\TableView;
use EMC\TableBundle\Table\Export\Export;

/**
 * CsvExportExtension
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class CsvExportExtension implements ExportExtensionInterface {

    /**
     * @var string
     */
    private $delimiter;

    /**
     * @var string
     */
    private $enclosure;

    /**
     * @var string
     */
    private $escape;

    function __construct($delimiter, $enclosure, $escape) {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    public function getIcon() {
        return 'fa fa-file-text-o';
    }

    public function getName() {
        return 'csv';
    }

    public function getText() {
        return 'CSV';
    }

    public function getContentType() {
        return 'application/text';
    }

    public function getFileExtension() {
        return 'csv';
    }

    public function export(TableView $view, $template = null, array $options = array()) {
        $out = tempnam('/tmp', 'export-out-');

        $data = $view->getData();
        $file = new \SplFileObject($out, 'w');
        $file->setCsvControl($this->delimiter, $this->enclosure, $this->escape);
        
        $row = array();
        foreach ($data['thead'] as $th) {
            $row[] = $th['title'];
        }
        $file->fputcsv($row);

        foreach ($data['tbody'] as $tr) {
            $row = array();
            foreach ($tr['data'] as $td) {
                $row[] = $td['value'];
            }
            $file->fputcsv($row);
        }

        $now = new \DateTime();

        $filename = preg_replace(array('/\[now\]/', '/\[caption\]/'), array($now->format('Y-m-d H\hi'), $data['caption']), 'Export');

        return new Export($file->getFileInfo(), $this->getContentType(), $filename, $this->getFileExtension());
    }

}
