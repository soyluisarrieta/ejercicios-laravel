<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $products = Product::all();
    return new ProductResource($products);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(ProductStoreRequest $request)
  {
    try {
      $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();

      // Create Product
      Product::create([
        'name' => $request->name,
        'image' => $imageName,
        'description' => $request->description,
      ]);

      // Save Image in Store folder
      Storage::disk('public')->put($imageName, file_get_contents($request->image));

      // Return Json Response
      return response()->json([
        'message' => 'Product successfully created.'
      ], 200);
    } catch (\Throwable $th) {
      return response()->json([
        'message' => 'Something went really wrong!'
      ], 500);
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    // Product detail
    $product = Product::find($id);
    if (!$product) {
      return response()->json([
        'message' => 'Product not found.'
      ], 404);
    }

    // Return Json Response
    return response()->json([
      'product' => $product
    ], 200);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateProductRequest $request, $id)
  {
    try {
      // Find product
      $product = Product::find($id);
      if (!$product) {
        return response()->json([
          'message' => 'Product Not Found.'
        ], 404);
      }

      $product->name = $request->name;
      $product->description = $request->description;

      $validated = $request->validated();

      if ($request->image) {
        // Public storage
        $storage = Storage::disk('public');

        // Generate unique image name
        $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();

        // Image save in public folder
        $storage->put($imageName, file_get_contents($request->image));
        // Update the product's image column with the new image name
        $product->image = $imageName;
      }

      $product->save($validated);
      return new ProductResource($product);
    } catch (\Exception $e) {
      // Return Json Response
      return response()->json([
        'message' => "Something went really wrong!"
      ], 500);
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    // Detail 
    $product = Product::find($id);
    if (!$product) {
      return response()->json([
        'message' => 'Product Not Found.'
      ], 404);
    }

    // Public storage
    $storage = Storage::disk('public');

    // Iamge delete
    if ($storage->exists($product->image))
      $storage->delete($product->image);

    // Delete Product
    $product->delete();

    // Return Json Response
    return response()->json([
      'message' => "Product successfully deleted."
    ], 200);
  }
}
