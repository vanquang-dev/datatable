<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */


    //  Index View
    public function index(Request $request)
    {
    	if ( $request->input('client') ) {
    	    return Product::select('id', 'name', 'description', 'image', 'created')->get();
    	}

        $columns = ['name', 'description', 'image', 'created'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = Product::select('id', 'name', 'description', 'image', 'created')->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                ->orWhere('description', 'like', '%' . $searchValue . '%');
            });
        }

        $products = $query->paginate($length);
        return ['data' => $products, 'draw' => $request->input('draw')];
    }

    public function store(Request $request)
    {
        dd($request->all());
        $this->validate($request,[
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'name' => 'required',
            'description' => 'required'
        ]);
        
        $image = request()->file('image');
        $imageName = $image->getClientOriginalName();
        $imageName = time().'_'.$imageName;
        $image->move(public_path('/images'), $imageName);
        $imageNameDB = 'images/'.$imageName;
        
        return Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imageNameDB,
        ]);
    }

    public function update($id, Request $request)
    {   
        // dd($request->all());
        if($request->hasFile('image')){
            $this->validate($request,[
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
                'name' => 'required',
                'description' => 'required'
            ]);
            $product = Product::findOrFail($id);
            \File::delete(public_path($product->image));
            $image = request()->file('image');
            $imageName = $image->getClientOriginalName();
            $imageName = time().'_'.$imageName;
            $image->move(public_path('/images'), $imageName);
            $imageNameDB = 'images/'.$imageName;
            
            Product::findOrFail($id)->update([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $imageNameDB,
            ]);
            dd($request->all());
        } else {
            $this->validate($request,[
                'image' => 'required',
                'name' => 'required',
                'description' => 'required'
            ]);
            Product::findOrFail($id)->update($request->all());
            dd($request->all());
        }
    }

    public function show($id)
    {
        $product =  Product::findOrFail($id);
        return ['data' => $product];
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
    }
}

