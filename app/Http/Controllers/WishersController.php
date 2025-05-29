<?php

namespace App\Http\Controllers;

use App\Classes\ImageUpload;
use App\Http\API\BaseController;
use App\Models\Wishers;
use App\Http\Requests\StoreWishersRequest;
use App\Http\Requests\UpdateWishersRequest;
use App\Http\Resources\web\WishersResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishersController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $blogNews = Wishers::when($request->id, function ($q, $id) {
                $q->where('id', $id);
            })->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })->orderBy('sorting_index', 'ASC')->paginate($request->per_page ?? 15);

            return WishersResource::collection($blogNews);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Wishers Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Wishers Show failed.', $exception->getMessage());
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
    public function store(StoreWishersRequest $request, ImageUpload $imageUpload)
    {
        DB::beginTransaction();
        try {
            $wishers = Wishers::create([
                'image' => null,
                'sorting_index' => $request->sorting_index,
                'status' => $request->status,
            ]);
            if ($request->image) {
                $wishers->image = $imageUpload->fileUpload(
                    file: $request->image,
                    data: $wishers,
                    folder: 'wishers-images',
                    width: 1500,
                    // hight: 1500,
                    fileName: 'image' . $wishers->id
                );
                $wishers->save();
            }
            DB::commit();
            return $this->sendResponse(
                message: 'wishers created successfully.',
                data: new WishersResource($wishers),
                status: 201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(
                message: 'wishers creation failed.',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        try {
            $wishers = Wishers::where('id', $request->route('wisher'))->firstOrFail();

            return (new WishersResource($wishers))->additional([
                "success" => true,
                "message" => __("Wishers show successfully.")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Wishers Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Wishers Show failed.', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wishers $wishers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWishersRequest $request, Wishers $wishers, ImageUpload $imageUpload)
    {
        DB::beginTransaction();
        try {
            $wishers = Wishers::where('id', $request->route('wisher'))->firstOrFail();
            $wishers->sorting_index = $request->sorting_index;
            $wishers->status = $request->status;
            if ($request->image) {
                if ($wishers->image) {
                    $imageUpload->deleteFile($wishers->image, 'wishers-images');
                }

                $wishers->image = $imageUpload->fileUpload(
                    file: $request->image,
                    data: $wishers,
                    folder: 'wishers-images',
                    width: 1500,
                    // hight: 1500,
                    fileName: 'image' . $wishers->id
                );
            }
            $wishers->save();
            DB::commit();
            return $this->sendResponse(
                message: 'wishers updated successfully.',
                data: new WishersResource($wishers),
                status: 200
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError(
                message: 'wishers update failed.',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ImageUpload $imageUpload)
    {
        try {
            DB::beginTransaction();
            $wishers = Wishers::where('id', $request->route('wisher'))->firstOrFail();
            if ($wishers->image) {
                $imageUpload->deleteFile($wishers->image, 'slider-images');
            }
            $wishers->delete();
            DB::commit();
            return (new WishersResource($wishers))->additional([
                "success" => true,
                "message" => __("Wishers Delete successfully")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Wishers Delete failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Wishers Delete failed.', $exception->getMessage());
        }
    }
}
