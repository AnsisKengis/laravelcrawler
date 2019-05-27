<?php

namespace App\Http\Controllers;

use App\Model\Products;
use Illuminate\Http\Request;

class CarsController extends Controller
{
    /**
     * @var Products
     */
    protected $products;

    public function __construct(
        Products $products
    )
    {
        $this->products = $products;
    }

    public function show($category = '',$page = 0, $year = '', Request $request)
    {
        $products = $this->products;
        $per_page = 20;
        if($category  != '')
        {
            $products = $products
                ->where('manufacturer', $category);
        }

        if($year  != '')
        {
            $products = $products
                ->where('year', $year);
        }

        $products = $products
            ->skip($per_page*$page)
            ->limit($per_page);



        $products = $products->get();




        return view('pages.cars_page', compact('products'));
    }
}
