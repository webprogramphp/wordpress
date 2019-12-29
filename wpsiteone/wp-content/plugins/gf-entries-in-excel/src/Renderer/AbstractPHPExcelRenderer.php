<?php

namespace GFExcel\Renderer;

use GFExcel\GFExcel;
use GFExcel\Values\BaseValue;
use GFForms;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Exception;
use PhpOffice\PhpSpreadsheet\Writer\BaseWriter;

abstract class AbstractPHPExcelRenderer extends AbstractRenderer
{
    /** @var Spreadsheet */
    protected $spreadsheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        register_shutdown_function([$this, 'fatalHandler']);
    }

    /**
     * This is where the magic happens, and the actual file is being rendered.
     * @param string $extension
     * @param bool $save
     * @return string
     */
    public function renderOutput($extension = 'xlsx', $save = false)
    {
        $exception = null;
        try {
            $this->spreadsheet->setActiveSheetIndex(0);
            /** @var BaseWriter $objWriter */
            $objWriter = IOFactory::createWriter($this->spreadsheet, ucfirst($extension));
            $objWriter->setPreCalculateFormulas(false);

            if ($save) {
                $file = get_temp_dir() . $this->getFileName();
                $objWriter->save($file);
                return $file;
            }

            if ($extension === 'xlsx') {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            }

            if ($extension === 'csv') {
                header('Content-Type: text/csv');
            }

            header('Content-Disposition: attachment;filename="' . $this->getFileName() . '"');
            header('Cache-Control: max-age=1');

            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            ob_end_clean(); // Cleaning buffer for preventing file corruption
            $objWriter->save('php://output');
        } catch (\Exception $e) {
            //in case of php5.x
            $exception = $e;
        } catch (\Throwable $e) {
            $exception = $e;
        }

        if ($exception) {
            header('Content-Type: text/html');
            header_remove('Content-Disposition');
            $this->handleException($exception);
        }

        exit; // stop rest
    }

    /**
     * Handle fatal error during execution
     * @throws \Exception
     */
    public function fatalHandler()
    {
        $error = error_get_last();
        if ($error['type'] === E_ERROR) {
            $exception = new Exception($error['message']);
            $this->handleException($exception);
        }
    }

    protected function autoSizeColumns(Worksheet $worksheet, $columns_count)
    {
        for ($i = 1; $i <= $columns_count; $i++) {
            $worksheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }
        return $this;
    }

    /**
     * @param Worksheet $worksheet
     * @param array $matrix
     * @param int $form_id
     * @return $this
     */
    protected function addCellsToWorksheet(Worksheet $worksheet, array $matrix, $form_id)
    {
        foreach ($matrix as $x => $row) {
            $hide_row = (bool) gf_apply_filters([
                'gfexcel_renderer_hide_row',
                $form_id,
            ], false, $row);

            if ($hide_row) {
                $worksheet->getRowDimension($x + 1)->setVisible(false);
            }

            foreach ($row as $i => $value) {
                $worksheet->setCellValueExplicitByColumnAndRow(
                    $i + 1,
                    $x + 1,
                    $this->getCellValue($value),
                    $this->getCellType($value)
                );
                $cell = $worksheet->getCellByColumnAndRow($i + 1, $x + 1);

                try {
                    $this->setProperties($cell, $value);
                    $worksheet->getStyle($cell->getCoordinate())->getAlignment()->setWrapText(true);
                } catch (Exception $e) {
                    $this->handleException($e);
                }
            }
        }
        return $this;
    }

    abstract protected function getFileName();

    /**
     * @param Worksheet $worksheet
     * @param array $form
     * @return $this
     */
    protected function setWorksheetTitle(Worksheet $worksheet, $form)
    {
        $invalidCharacters = Worksheet::getInvalidCharacters();
        //First strip form title, so we still have 30 charachters.
        $form_title = str_replace($invalidCharacters, '', $form['title']);
        $worksheet_title = mb_substr(gf_apply_filters(
            [
                'gfexcel_renderer_worksheet_title',
                $form['id'],
            ],
            $form_title,
            $form
        ), 0, 31, 'utf-8');

        // Protect users from accidental override with invalid characters.
        $worksheet_title = str_replace($invalidCharacters, '', $worksheet_title);
        $worksheet->setTitle($worksheet_title);
        return $this;
    }

    /**
     * Get Cell type based on value booleans
     * @param $value
     * @return string
     */
    private function getCellType($value)
    {
        if ($value instanceof BaseValue) {
            if ($value->isNumeric()) {
                return DataType::TYPE_NUMERIC;
            }
            if ($value->isBool()) {
                return DataType::TYPE_BOOL;
            }
        }

        return DataType::TYPE_STRING;
    }

    /**
     * Retrieve the correctly formatted value of the cell
     * @param $value
     * @return string
     */
    private function getCellValue($value)
    {
        if ($value instanceof BaseValue) {
            return $value->getValue();
        }

        return $value;
    }

    /**
     * Set url on the cell if value has a url.
     *
     * @param Cell $cell
     * @param $value
     * @return bool
     */
    private function setCellUrl(Cell $cell, $value)
    {
        if (!$value instanceof BaseValue or
            !$value->getUrl() or
            gf_apply_filters(['gfexcel_renderer_disable_hyperlinks'], false)
        ) {
            return false;
        }

        try {
            $cell->getHyperlink()->setUrl($value->getUrl());
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param \Throwable|Exception $exception
     */
    private function handleException($exception)
    {
        global $wp_version;

        echo "<p><strong>Gravity Forms Entries in Excel: Whoops, unfortunately something is broken.</strong></p>";
        echo "<p><strong>Error message</strong>: " . nl2br($exception->getMessage()) . " </p>";
        echo "<p>If you need support for this, please contact me via the";
        echo " <a target='_blank' href='https://wordpress.org/support/plugin/gf-entries-in-excel'>support forum</a> ";
        echo "on the wordpress plugin.</p>";
        echo "<p>Check if someone else had the same error, before posting a new support question.<br/>";
        echo "And when opening a new question, <strong>please use the error message ";
        echo "as the title</strong>, and:</> <p><strong>Include the following details in your message:</strong></p>";
        echo "<ul>";
        echo "<li>Plugin Version: " . GFExcel::$version . "</li>";
        echo "<li>Gravity Forms Version: " . GFForms::$version . "</li>";
        echo "<li>PHP Version: " . PHP_VERSION;
        if (version_compare(PHP_VERSION, '5.6.1', '<')) {
            echo " (this version is too low, please update to at least PHP 5.6)";
        }
        echo "</li>";
        echo "<li>Wordpress Version: " . $wp_version . "</li>";
        echo "<li>Error message: " . nl2br($exception->getMessage()) . "</li>";
        echo "<li>Error stack trace:<br/><br/>" . nl2br($exception->getTraceAsString()) . "</li>";
        echo "</ul>";
        exit;
    }

    /**
     * @param Cell $cell
     * @param $value
     * @throws \GFExcel\Exception\Exception
     */
    private function setProperties(Cell $cell, $value)
    {
        $this->setCellUrl($cell, $value);
        $this->setFontStyle($cell, $value);
    }

    /**
     * @param Cell $cell
     * @param $value
     * @return bool
     * @throws \GFExcel\Exception\Exception
     */
    private function setFontStyle(Cell $cell, $value)
    {
        if (!$value instanceof BaseValue) {
            return false;
        }

        try {
            if ($value->isBold()) {
                $cell->getStyle()->getFont()->setBold(true);
            }
            if ($value->isItalic()) {
                $cell->getStyle()->getFont()->setItalic(true);
            }
            if ($color = $value->getColor()) {
                $color_field = $cell->getStyle()->getFont()->getColor();
                $color_field->setRGB($color);
                $cell->getStyle()->getFont()->setColor($color_field);
            }
            if ($color = $value->getBackgroundColor()) {
                $fill = $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
                $color_field = $fill->getStartColor()->setRGB($color);
                $fill->setStartColor($color_field);
            }

            return true;
        } catch (\GFExcel\Exception\Exception $e) {
            throw $e;
        } catch (Exception $e) {
            return false;
        }
    }
}
