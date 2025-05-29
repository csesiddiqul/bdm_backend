<?php

namespace App\Http\Controllers;

use App\Classes\ImageUpload;
use App\Http\API\BaseController;
use App\Http\Resources\web\BdmHospitalResource;
use App\Models\BdmHospital;
use App\Models\MissionVision;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BdmHospitalController extends BaseController
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $bdmHospital = BdmHospital::findOrFail($id);
            return (new BdmHospitalResource($bdmHospital))->additional([
                "success" => true,
                "message" => __("Bdm Hospital show successfully.")
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
    public function edit(BdmHospital $bdmHospital)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id, ImageUpload $imageUpload)
    {
        DB::beginTransaction();
        try {
            $bdmHospital = BdmHospital::findOrFail($id);
            $bdmHospital->title = $request->title;
            $bdmHospital->description = $request->description;

            if ($request->hasFile('image')) {
                $bdmHospital->image = $imageUpload->fileUpload(
                    file: $request->image,
                    data: $bdmHospital,
                    folder: 'bdm-hospital-images',
                    fileName: 'mimage' . $bdmHospital->id
                );
            }

            $bdmHospital->save();

            DB::commit();

            return $this->sendResponse(
                message: 'Data saved successfully.',
                data: new BdmHospitalResource($bdmHospital),
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
    public function destroy(BdmHospital $bdmHospital)
    {
        //
    }
}
