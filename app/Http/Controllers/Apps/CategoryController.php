<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // GET CATEGORIES
        $categories = Category::when(request()->q, function ($categories) {
            $categories = $categories->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);

        // RETURN INERTIA
        return Inertia::render('Apps/Categories/Index', [
            'categories' => $categories
        ]);
    }

    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        return Inertia::render('Apps/Categories/Create');
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        // VALIDATE
        $this->validate($request, [
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'name'          => 'required|unique:categories',
            'description'   => 'required'
        ]);

        // UPLOAD IMAGE
        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        // CREATE CATEGORY
        Category::create([
            'image'         => $image->hashName(),
            'name'          => $request->name,
            'description'   => $request->description,
        ]);

        // REDIRECT
        return redirect()->route('apps.categories.index');
    }

    /**
     * edit
     *
     * @param  mixed $category
     * @return void
     */
    public function edit(Category $category)
    {
        return Inertia::render('Apps/Categories/Edit', ['category' => $category]);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $category
     * @return void
     */
    public function update(Request $request, Category $category)
    {
        // VALIDATE
        $this->validate($request, [
            'name'          => 'required|unique:categories,name,' . $category->id,
            'description'   => 'required',
        ]);

        // CHECK IMAGE
        if ($request->file('image')) {
            // REMOVE OLD IMAGE
            Storage::disk('local')->delete('public/categories/' . basename($category->image));

            // UPLOAD NEW IMAGE
            $image = $request->file('image');
            $image->storeAs('public/categories/', $image->hashName());

            // UPDATE CATEGORY WITH NEW IMAGE
            $category->update([
                'image'         => $image->hashName(),
                'name'          => $request->name,
                'description'   => $request->description,
            ]);
        }

        // UPDATE WITHOUT IMAGE
        $category->update([
            'name'          => $request->name,
            'description'   => $request->description,
        ]);

        // RETURN
        return redirect()->route('apps.categories.index');
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        // FIND CATEGORY BY ID
        $category = Category::findOrFail($id);

        // REMOVE IMAGE
        Storage::disk('local')->delete('public/categories/' . basename($category->image));

        // DELETE RECORD
        $category->delete();

        // RETURN REDIRECT
        return redirect()->route('apps.categories.index');
    }
}
