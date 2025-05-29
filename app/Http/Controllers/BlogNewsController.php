<?php

namespace App\Http\Controllers;

use App\Classes\ImageUpload;
use App\Http\API\BaseController;
use App\Models\BlogNews;
use App\Http\Requests\StoreBlogNewsRequest;
use App\Http\Requests\UpdateBlogNewsRequest;
use App\Http\Resources\web\BlogNewsResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BlogNewsController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $blogNews = BlogNews::when($request->id, function ($q, $id) {
                $q->where('id', $id);
            })
                ->when($request->search, function ($q, $search) {
                    $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%']);
                })
                ->when($request->status, function ($q, $status) {
                    $q->where('status', $status);
                })->orderBy('sorting_index', 'asc')->paginate($request->per_page ?? 15);

            return BlogNewsResource::collection($blogNews);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('BlogNews Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('BlogNews Show failed.', $exception->getMessage());
        }
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
    public function store(StoreBlogNewsRequest $request, ImageUpload $imageUpload)
    {
        DB::beginTransaction();
        try {
            $blogNews = BlogNews::create([
                'image' => null,
                'sorting_index' => $request->sorting_index,
                'title' => $request->title,
                'description' =>  $request->description,
                'status' => $request->status,
            ]);
            if ($request->image) {
                $blogNews->image = $imageUpload->fileUpload(
                    file: $request->image,
                    data: $blogNews,

                    folder: 'blog-news-images',

                    width: 1500,
                    // hight: 1500,
                    fileName: 'image' . $blogNews->id
                );
                $blogNews->save();
            }
            DB::commit();
            return $this->sendResponse(
                message: 'BlogNews created successfully.',
                data: new BlogNewsResource($blogNews),
                status: 201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(
                message: 'BlogNews creation failed.',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogNews $blogNews)
    {
        try {
            $blogNews = BlogNews::findOrFail($blogNews->id);
            return (new BlogNewsResource($blogNews))->additional([
                "success" => true,
                "message" => __("Blog News show successfully.")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Blog News Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Blog News Show failed.', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogNews $blogNews)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogNewsRequest $request, BlogNews $blogNews, ImageUpload $imageUpload)
    {
        DB::beginTransaction();
        try {
            $blogNews->sorting_index = $request->sorting_index;
            $blogNews->title = $request->title;
            $blogNews->description = $request->description;
            $blogNews->status = $request->status;
            if ($request->image) {
                if ($blogNews->image) {

                    $imageUpload->deleteFile($blogNews->image, 'blog-news-images');
                }

                $blogNews->image = $imageUpload->fileUpload(
                    file: $request->image,
                    data: $blogNews,
                    folder: 'blog-news-images',
                    width: 1500,
                    // hight: 1500,
                    fileName: 'image' . $blogNews->id
                );
            }
            $blogNews->save();
            DB::commit();
            return $this->sendResponse(
                message: 'Blog News updated successfully.',
                data: new BlogNewsResource($blogNews),
                status: 200
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError(
                message: 'Blog News update failed.',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogNews $blogNews, ImageUpload $imageUpload)
    {
        try {
            DB::beginTransaction();
            $blogNews = BlogNews::findOrFail(id: $blogNews->id);
            if ($blogNews->image) {
                $imageUpload->deleteFile($blogNews->image, 'slider-images');
            }
            $blogNews->delete();
            DB::commit();
            return (new BlogNewsResource($blogNews))->additional([
                "success" => true,
                "message" => __("Blog News Delete successfully")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Blog News Delete failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Blog News Delete failed.', $exception->getMessage());
        }
    }
}
