<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\User;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $news = News::all();
        // Replace users_id with name
        $data = [];
        foreach ($news as $item) {
            // Push to array
            // Get name from users_id
            $user = User::where('id', $item->users_id)->first();
            $name = $user->name;

            // Format output date as 2023-01-01 12.15
            $date = date('Y-m-d H:i', strtotime($item->created_at));

            array_push($data, [
                'id' => $item->id,
                'title' => $item->title,
                'content' => $item->content,
                'image' => $item->image,
                'author' => $name,
                'created_at' => $date,
                'updated_at' => $item->updated_at
            ]);
        }

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);

        $data = $request->all();

        // Image upload
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust validation rules as needed
            ]);

            $image = $request->file('image');
            $filesize = $request->file('image')->getSize();

            // Generate a unique name for the image
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Move the uploaded image to a storage directory
            $image->move(public_path('uploads'), $imageName);

            // Create the URL for the uploaded image
            $imageUrl = asset('uploads/' . $imageName);

            // Add image to data array
            $data['image'] = $imageUrl;
        }

        // Stored logged in user id
        $data['users_id'] = auth()->user()->id;

        return News::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $news = News::find($id);

        if ($news != null) {
            return $news;
        }

        return response()->json([
            'Post not found'
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        var_dump($id);
        var_dump($request->all());
        $news = News::find($id);

        if ($news != null) {
            $request->validate([
                'title' => 'required',
                'content' => 'required'
            ]);


            $news->update($request->all());
            return $news;
        }

        return response()->json([
            'Post not found'
        ], 404);
    }

    /**
     * Update the specified resource in storage, but with POST method
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePost(Request $request)
    {
        $news = News::find($request->id);

        if ($news != null) {
            $request->validate([
                'title' => 'required',
                'content' => 'required'
            ]);

            // Image upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust validation rules as needed
                ]);

                $image = $request->file('image');
                $filesize = $request->file('image')->getSize();

                // Generate a unique name for the image
                $imageName = time() . '.' . $image->getClientOriginalExtension();

                // Move the uploaded image to a storage directory
                $image->move(public_path('uploads'), $imageName);

                // Create the URL for the uploaded image
                $imageUrl = asset('uploads/' . $imageName);

                // Remove image from request
                unset($request['image']);

                // Add image to data array
                $data['image'] = $imageUrl;
                $data = array_merge($request->all(), $data);
            }

            $news->update($data);
            return $news;
        }

        return response()->json([
            'Post not found'
        ], 404);
    }

    /**
     * Update image 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateImage(Request $request, $id)
    {
        $news = News::find($id);

        if ($news != null) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust validation rules as needed
            ]);

            $image = $request->file('image');
            $filesize = $request->file('image')->getSize();

            // Generate a unique name for the image
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Move the uploaded image to a storage directory
            $image->move(public_path('uploads'), $imageName);

            // Create the URL for the uploaded image
            $imageUrl = asset('uploads/' . $imageName);

            // Add image to data array
            $data['image'] = $imageUrl;

            $news->update($data);
            return $news;
        }

        return response()->json([
            'Post not found'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $news = News::find($id);
        if ($news != null) {
            $news->delete();
            return response()->json([
                'Post deleted'
            ]);
        }

        return response()->json([
            'Post not found'
        ], 404);
    }
}
