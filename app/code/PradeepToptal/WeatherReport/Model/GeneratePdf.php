<?php
declare(strict_types=1);

namespace PradeepToptal\WeatherReport\Model;

use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class GeneratePdf
 * @package PradeepToptal\WeatherReport\Model
 */
class GeneratePdf
{
    const ROWS_PER_PAGE = 30;
    const FILE_EXTENSION = '.pdf';

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * GeneratePdf constructor.
     * @param FileFactory $fileFactory
     * @param DateTime $dateTime
     */
    public function __construct(
        FileFactory $fileFactory,
        DateTime $dateTime
    )
    {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
    }

    /**
     * @param $reportItems
     * @throws \Zend_Pdf_Exception
     */
    public function generate($reportItems)
    {
        if(isset($reportItems['data']) && is_array($reportItems['data']) && count($reportItems['data'])) {
            $fileName = $this->getReportFileName($reportItems['data']);
            $arrayChunk = array_chunk($reportItems['data'], self::ROWS_PER_PAGE);
            $counter = 0;
            $pdf = new \Zend_Pdf();
            foreach($arrayChunk as $array) {
                $pdf->pages[] = $pdf->newPage(\Zend_Pdf_Page::SIZE_LETTER);
                $this->drawPdf($array, $pdf, $counter);
                $counter++;
            }

            $this->fileFactory->create(
                $fileName,
                $pdf->render(),
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }
    }

    protected function drawPdf($reportItems, $pdf, $counter)
    {
        $page = $pdf->pages[$counter]; // this will get reference to the first page.
        $style = new \Zend_Pdf_Style();
        $style->setLineColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
        $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
        $style->setFont($font, 15);
        $page->setStyle($style);
        $x = 0;
        $this->y = 850 - 100; //print table row from page top – 100px

        $style->setFont($font, 14);
        $page->setStyle($style);

        $style->setFont($font, 13);
        $page->setStyle($style);
        $style->setFont($font, 11);
        $page->setStyle($style);
        if (isset($reportItems[0]['city']) && isset($reportItems[0]['country'])) {
            $page->drawText(__("Location : %1", $reportItems[0]['city'] . ',' . $reportItems[0]['country']), $x + 50, $this->y, 'UTF-8');
        }

        $style->setFont($font, 10);
        $page->setStyle($style);
        $yPad = 10;
        foreach ($reportItems as $report) {
            $yPad = $yPad + 20;
            $page->drawText($report['date'], $x + 50, $this->y - $yPad, 'UTF-8');
            $page->drawText($report['temp_min'] . '/' . $report['temp_max'] . '°C', $x + 230, $this->y - $yPad, 'UTF-8');
            $page->drawText($report['weather_description'], $x + 370, $this->y - $yPad, 'UTF-8');
        }
    }

    /**
     * Generate dynamic unque file name for PDF
     * @param $reportItems
     * @return string
     */
    protected function getReportFileName($reportItems)
    {
        $randomName = rand().$this->dateTime->date('ymdhis');
        $fileName = md5($randomName);
        if (isset($reportItems[0]['city']) && isset($reportItems[0]['country'])) {
            $fileName = $reportItems[0]['city'].$reportItems[0]['country'].date('ymdhis').rand();
        }
        $fileName = $fileName.self::FILE_EXTENSION;
        return $fileName;
    }
}
