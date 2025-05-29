<?php

namespace App\Http\Controllers;

use App\Classes\ImageUpload;
use App\Http\API\BaseController;
use App\Models\Gallery;
use App\Http\Requests\StoreGalleryRequest;
use App\Http\Requests\UpdateGalleryRequest;
use App\Http\Resources\GalleryResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GalleryController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $slider = Gallery::when($request->id, function ($q, $id) {
                $q->where('id', $id);
            })
                ->when($request->search, function ($q, $search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
                })
                ->when($request->status, function ($q, $status) {
                    $q->where('status', $status);
                })->orderBy('sorting_index', 'ASC')->paginate($request->per_page ?? 15);

            return GalleryResource::collection($slider);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Slider Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Slider Show failed.', $exception->getMessage());
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
    public function store(StoreGalleryRequest $request, ImageUpload $imageUpload)
    {
        DB::beginTransaction();
        try {
            $gallery = Gallery::create([
                'image' => null,
                'type' => $request->type,
                'name' => $request->video_url,
                'video_url' => $request->video_url,
                'sorting_index' => $request->sorting_index,
                'status' => $request->status,
            ]);
            if ($request->image) {
                $gallery->image = $imageUpload->fileUpload(
                    file: $request->image,
                    data: $gallery,
                    folder: 'gallery-images',
                    width: 1600,
                    hight: 800,
                    fileName: 'image' . $gallery->id
                );
                $gallery->save();
            }
            DB::commit();
            return $this->sendResponse(
                message: 'Gallery created successfully.',
                data: new GalleryResource($gallery),
                status: 201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(
                message: 'Gallery creation failed.',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $gallery = Gallery::findOrFail($id);
            return (new GalleryResource($gallery))->additional([
                "success" => true,
                "message" => __("gallery show successfully.")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('gallery Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('gallery Show failed.', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gallery $gallery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGalleryRequest $request, $id, ImageUpload $imageUpload)
    {



        DB::beginTransaction();
        try {

            $gallery = Gallery::findOrFail($id);
            $gallery->sorting_index = $request->sorting_index;
            $gallery->name = $request->name;
            $gallery->video_url = $request->video_url;
            $gallery->type = $request->type;
            $gallery->status = $request->status;
            $gallery->save();

            DB::commit();
            return $this->sendResponse(
                message: 'gallery updated successfully.',
                data: new GalleryResource($gallery),
                status: 200
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError(
                message: 'gallery update failed.',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($gallery, ImageUpload $imageUpload)
    {
        try {
            DB::beginTransaction();
            $gallery = Gallery::findOrFail(id: $gallery);
            if ($gallery->image) {
                $imageUpload->deleteFile($gallery->image, 'slider-images');
            }
            $gallery->delete();
            DB::commit();
            return (new GalleryResource($gallery))->additional([
                "success" => true,
                "message" => __("gallery Delete successfully")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('gallery Delete failed.', $exception->getMessage(), 404);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('gallery Delete failed.', $exception->getMessage(), 404);
        }
    }
}
