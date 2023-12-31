<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProductResource;

class ProductApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::latest("id")->paginate(10)->onEachSide(1);
        // return response()->json($products,200);
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            "name" => "required|min:3|max:50",
            "price" => "required|numeric|min:1",
            "stock" => "required|numeric|min:1",
            "photos" => "required",
            "photos.*" => "file|mimes:jpeg,png|max:512"
        ]);

        $product = Product::create([
            "name" => $request->name,
            "price" => $request->price,
            "stock" => $request->stock,
            "user_id" => Auth::id()
        ]);
        $photos = [];
        foreach ($request->file('photos') as $key=>$photo) {
            $newName = $photo->store("public"); //storage/app/public

            //Inserting and Updating Related Model
            $photos["key"] = new Photo(['name'=>$newName]);
        }
        $product->photos()->saveMany($photos); //using relation

        return response()->json([
            "message" => "Product Created",
            "success" => true,
            "product" => new ProductResource($product)
        ]); //json(data,status code)
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        if(is_null($product)){
            return response()->json(["message"=>"Product is not Found"],404);
        }
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            "name" => "nullable|min:3|max:50",
            "price" => "nullable|numeric|min:1",
            "stock" => "nullable|numeric|min:1"
        ]);
        $product = Product::find($id);
        if(is_null($product)){
            return response()->json(["message"=>"Product is not Found"],404);
        }

        if($request->has("name")){
            $product->name = $request->name;
        }
        if($request->has("price")){
            $product->price = $request->price;
        }
        if($request->has("stock")){
            $product->stock = $request->stock;
        }
        $product->update();

        return response()->json([
            "message" => "Product Updated",
            "success" => true,
            "product" => new ProductResource($product)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if(is_null($product)){
            return response()->json(["message"=>"Product is not Found"],404);
        }
        $product->delete();
        return response()->json(["message"=>"Product is Deleted"],200);
    }
}
