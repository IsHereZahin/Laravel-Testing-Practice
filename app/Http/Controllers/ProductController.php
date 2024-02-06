<?php

namespace App\Http\Controllers;

use App\Jobs\NewProductNotifyJob;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Products::paginate(10);
        return view('product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' =>'required',
            'price' =>'required',
            'image' =>'required',
        ]);

        $product = Products::create([
            'name' => $validatedData['name'],
            'price' => $validatedData['price'],
            'image' => $validatedData['image'],
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $currentDateTime = now()->format('Ymd_His');
            $filename = $currentDateTime .'.'. $file->getClientOriginalExtension();

            // Ensure images directory exists
            $directory = public_path('images');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $file->move($directory, $filename);
            $imagePath = 'images/' . $filename;

            // Update the product with the image path
            $product->image = $imagePath;
            $product->save();
        }

        NewProductNotifyJob::dispatch($product);

        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Products::query()->findOrFail($id);
        return view('product.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' =>'required',
            'price' =>'required',
        ]);

        Products::query()->findOrFail($id)->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);
        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Products::query()->findOrFail($id)->delete();
        return redirect()->route('products.index');
    }

    public function download()
    {
        return response()->download(public_path('files/download.pdf'));
    }
}
