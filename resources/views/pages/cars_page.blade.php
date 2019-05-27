@extends('layouts.app')

@section('cars_page')
    <div class="row" style="padding: 0 50px;">
        <div class="col-md-12">
            <table style="width:100%">
                <tr>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Engine</th>
                    <th>Mileage (Km)</th>
                    <th>Price</th>
                </tr>
                @foreach ($products as $product)
                    <tr class="car-rows">
                        <td><img src="<?php echo $product->image_url; ?>"></td>
                        <td><?php echo $product->description; ?></td>
                        <td><?php echo $product->model; ?></td>
                        <td><?php echo $product->year; ?></td>
                        <td><?php echo $product->engine; ?></td>
                        <td><?php echo $product->mileage; ?></td>
                        <td><?php echo $product->price . '€'; ?></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
