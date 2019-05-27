<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Categories;

class CategoryCrawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Category Crawler';

    const BASE_CARS = 'https://www.ss.com/lv/transport/cars/';
    const BASE_WEBSITE = 'https://www.ss.com/';

    /**
     * Create a new command instance.
     *
     * @return void
     */
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
        $this->crawlCategories(self::BASE_CARS, self::BASE_WEBSITE);
    }

    private function getSsContent($page)
    {
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

    private function crawlCategories($page, $baseWebsite)
    {
        $html_base = $this->getSsContent($page);

        foreach($html_base->find('a') as $element) {
            $link = $element->href;
            $categoryName = $element->plaintext;
            if ($categoryName) {

                $categoryData = [
                    'category_name' => $categoryName,
                    'category_url' => $baseWebsite . $link
                ];

                Categories::create($categoryData);
            }
        }
    }
}
