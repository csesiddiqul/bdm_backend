<?php

namespace App\Http\Controllers;

use App\Http\API\BaseController;
use App\Http\Requests\StoreSliderRequest;
use App\Http\Requests\UpdateSliderRequest;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SliderController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $slider = Slider::when($request->id, function ($q, $id) {
                $q->where('id', $id);
            })->when($request->search, function ($q, $search) {
                $q->where('title', 'LIKE', '%' . $search . '%');
            })->orderBy('created_at', 'DESC')
                ->paginate($request->per_page ?? 15);
            return SliderResource::collection($slider);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Data Fetch failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Data Fetch failed.', $exception->getMessage());
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
     * Handle  Store Slider
     *
     * @param \App\Http\Requests\VerifyPhoneRequest $request
     * @return \Illuminate\Http\JsonResponse
     */


    public function store(StoreSliderRequest $request)
    {
        DB::beginTransaction();
        try {
            $imagePath = $request->file('image')->store('slider-image', 'public');
            $slider = Slider::create([
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'description' => $request->description,
                'status' => $request->status,
                'image' => 'storage/' . $imagePath,
            ]);

            DB::commit();
            return $this->sendResponse(
                'Slider created successfully.',
                new SliderResource($slider)
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(
                message: 'Slider creation failed !',
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
            $slider = Slider::findOrFail($id);
            return new SliderResource($slider);
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message: 'Slider not found!',
                errors: 'No slider found with the provided ID.',
                status: 404
            );
        } catch (\Exception $e) {
            return $this->sendError(
                message: 'Slider Show failed!',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Slider $slider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * Handle  Store Slider
     *
     * @param \App\Http\Requests\VerifyPhoneRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function update(UpdateSliderRequest $request, $id)
    {
        DB::beginTransaction();

        $slider = Slider::where('id', '=', $id)->firstOrFail();
        try {
            if ($request->hasFile('image')) {
                if ($slider->image && Storage::disk('public')->exists(str_replace('storage/', '', $slider->image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $slider->image));
                }

                $imagePath = $request->file('image')->store('slider-image', 'public');
                $slider->image = 'storage/' . $imagePath;
            }

            $slider->title = $request->title;
            $slider->subtitle = $request->subtitle;
            $slider->description = $request->description;
            $slider->status = $request->status;

            $slider->save();

            DB::commit();

            return $this->sendResponse(
                'Slider updated successfully.',
                new SliderResource($slider)
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(
                message: 'Slider update failed!',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $slider = Slider::where('id', '=', $id)->firstOrFail();

        DB::beginTransaction();

        try {
            if ($slider->image && Storage::disk('public')->exists(str_replace('storage/', '', $slider->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $slider->image));
            }

            $slider->delete();

            DB::commit();

            return $this->sendResponse(
                'Slider deleted successfully.'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError(
                message: 'Slider deletion failed!',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }
}
