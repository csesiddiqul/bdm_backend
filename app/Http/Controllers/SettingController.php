<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Classes\ImageUpload;
use App\Http\API\BaseController;
use App\Http\Resources\web\SettingResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;


class SettingController extends BaseController
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
    public function store(StoreSettingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $setting = Setting::findOrFail($id);
            return (new SettingResource($setting))->additional([
                "success" => true,
                "message" => __("Setting show successfully.")
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
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(UpdateSettingRequest $request, $id, ImageUpload $imageUpload)
    {
        DB::beginTransaction();

        try {
            $setting = Setting::findOrFail($id);
            $setting->update([
                'website_title' => $request->website_title,
                'slogan' => $request->slogan,
                'location' => $request->location,
                'email' => $request->email,
                'phone' => $request->phone,
                'whatsapp' => $request->whatsapp,
                'telephone' => $request->telephone,
                'googlemap' => $request->googlemap,
                'websitelink' => $request->websitelink,
                'facebook' => $request->facebook,
                'twitter' => $request->twitter,
                'instagram' => $request->instagram,
                'linkedin' => $request->linkedin,
                'youtube' => $request->youtube,
                'copyrighttext' => $request->copyrighttext,
                'tramscondition' => $request->tramscondition,
                'privacypolicy' => $request->privacypolicy,

                'count_section' => $request->count_section,
                'help_section' => $request->help_section,
                'quick_link' => $request->quick_link,


            ]);

            if ($request->hasFile('headerlogo')) {
                $setting->headerlogo = $imageUpload->fileUpload(
                    file: $request->file('headerlogo'),
                    data: $setting,
                    folder: 'settings',
                    fileName: 'headerlogo_' . $setting->id
                );
            }




            if ($request->hasFile('favicon')) {

                $setting->favicon = $imageUpload->fileUpload(
                    file: $request->file('favicon'),
                    data: $setting,
                    folder: 'settings/',
                    fileName: 'favicon_' . $setting->id
                );
            }

            $setting->save();

            DB::commit();

            return $this->sendResponse(
                message: 'Settings updated successfully.',
                data: new SettingResource($setting),
                status: 200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update failed: ' . $e->getMessage());

            return $this->sendError(
                message: 'Update failed.',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
