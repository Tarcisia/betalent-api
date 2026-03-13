<?php


namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()->latest()->paginate(10);

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::query()->create($request->validated());

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return new ProductResource($product->fresh());
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Produto excluído com sucesso.'
        ]);
    }
}
