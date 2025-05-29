<?php

namespace App\Http\Controllers;

use App\Classes\ImageUpload;
use App\Http\API\BaseController;
use App\Http\Resources\web\MissionVisionResource;
use App\Models\MissionVision;
use App\Http\Requests\StoreMissionVisionRequest;
use App\Http\Requests\UpdateMissionVisionRequest;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class MissionVisionController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreMissionVisionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $misionvision = MissionVision::findOrFail($id);
            return (new MissionVisionResource($misionvision))->additional([
                "success" => true,
                "message" => __("Mision Vision show successfully.")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Data Not Found.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Data Not Found.', $exception->getMessage());
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MissionVision $missionVision)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMissionVisionRequest $request, $id, ImageUpload $imageUpload)
    {
        DB::beginTransaction();
        try {
            $missionVision = MissionVision::updateOrCreate(
                ['id' => $id],
                [
                    'mtitle' => $request->mtitle,
                    'mdescription' => $request->mdescription,
                    'vtitle' => $request->vtitle,
                    'vdescription' => $request->vdescription,
                ]
            );

            if ($request->hasFile('mimage')) {
                $missionVision->mimage = $imageUpload->fileUpload(
                    file: $request->mimage,
                    data: $missionVision,
                    folder: 'mission-vision-images',
                    fileName: 'mimage' . $missionVision->id
                );
            }

            if ($request->hasFile('vimage')) {
                $missionVision->vimage = $imageUpload->fileUpload(
                    file: $request->vimage,
                    data: $missionVision,
                    folder: 'mission-vision-images',
                    fileName: 'vimage' . $missionVision->id
                );
            }

            $missionVision->save();

            DB::commit();

            return $this->sendResponse(
                message: 'Data saved successfully.',
                data: new MissionVisionResource($missionVision),
                status: 200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Save failed: ' . $e->getMessage());

            return $this->sendError(
                message: 'Data save failed.',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MissionVision $missionVision)
    {
        //
    }
}
