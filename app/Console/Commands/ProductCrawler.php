<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Products;

class ProductCrawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Product Crawler';

    const AUDI_LINK = 'https://www.ss.com/lv/transport/cars/audi/';
    const BMW_LINK = 'https://www.ss.com/lv/transport/cars/bmw/';
    const VOLKSWAGEN_LINK = 'https://www.ss.com/lv/transport/cars/volkswagen/';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $carsLinks = [];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $carModelLinks = [
            self::AUDI_LINK,
            self::BMW_LINK,
            self::VOLKSWAGEN_LINK,
        ];
        foreach ($carModelLinks as $carModelLink) {
            var_dump($carModelLink);
            $this->getPages($carModelLink);
        }
    }

    public function getPages($carModelLinks) {
        $page = $carModelLinks;
        for ($i = 1; $i <= 100; $i++) {
            $this->getAllCars($page."page".$i.".html");
            echo "Page: $i \n";
        }
    }

    private function getAllCars($page) {
        $html_base = $this->getSsContent($page);

        foreach($html_base->find('table tr[id^="tr_4"]') as $element) {
            $href = $element->find('a');
            if(count($href) == 0){
                continue;
            }
            $href = $href[0]->href;

            $re = '/\/msg\/lv\/transport\/cars(.*)/';
            preg_match($re, $href, $matches, PREG_OFFSET_CAPTURE, 0);
            if(count($matches) >= 1) {
                $imageUrl = $element->find('td', 1)->find('a img', 0)->src;

                if (\strpos($href, 'audi') !== false) {
                    $manufacturer = 'audi';
                }
                if (\strpos($href, 'bmw') !== false) {
                    $manufacturer = 'bmw';
                }
                if (\strpos($href, 'volkswagen') !== false) {
                    $manufacturer = 'volkswagen';
                } else {
                    continue;
                }
                var_dump($href);
                var_dump($manufacturer);

                $description = $element->find('td', 2)->find('div a', 0)->innertext;
                $finalDescription = strip_tags($description);
                $model = $element->find('td', 3)->innertext;
                $finalModel = strip_tags($model);
                $year = $element->find('td', 4)->innertext;
                $finalYear = strip_tags($year);
                $engine = $element->find('td', 5)->innertext;
                $finalEngine = strip_tags($engine);
                $mileage = preg_replace('/[^0-9]/i', '', $element->find('td', 6)->innertext);
                $finalMilage = strip_tags($mileage);
                $price = preg_replace('/[^0-9]/i', '', $element->find('td', 7)->innertext);
                $finalPrice = strip_tags($price);

                if ((int)$finalPrice <= 0) {
                    continue;
                }

                $productData = [
                    'image_url' => $imageUrl,
                    'manufacturer' => $manufacturer,
                    'description' => $finalDescription,
                    'model' => $finalModel,
                    'year' => $finalYear,
                    'engine' => $finalEngine,
                    'mileage' => $finalMilage,
                    'price' => $finalPrice,
                ];

                try {
                    Products::create($productData);
                } catch (\Illuminate\Database\QueryException $e) {
                    $errorCode = $e->errorInfo[1];
                    if ($errorCode == 1062) {
                        return 'Duplicate Entry';
                    }
                }
            }
        }
    }

    private function getSsContent($page) {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $page);
        curl_setopt($curl, CURLOPT_REFERER, $page);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($curl);
        curl_close($curl);
        // Create a DOM object
        $html_base = new \simple_html_dom();
        // Load HTML from a string
        $html_base->load($str);

        return $html_base;
    }
}
