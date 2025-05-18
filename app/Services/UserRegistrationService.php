<?php

namespace App\Services;

use App\Models\BloodDonorInfo;
use App\Models\Staff;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Models\Address;
use App\Models\PatientInfo;
use App\Classes\ImageUpload;

class UserRegistrationService
{
    /**
     * Handle role-specific data for the user.
     *
     * @param User $user
     * @param RegisterRequest $request
     * @return void
     */


    public function handleStoreUserInfo(User $user, $request)
    {

    }


    public function handleUpdatePatientInfo($request, $id): void
    {
        $patientInfo = PatientInfo::where('user_id', $id)->firstOrFail();

        $patientInfo->update([
            'disease_type_id' => $request->disease_type_id,
            'gender_id' => $request->gender_id,
            'date_of_birth' => $request->date_of_birth,
            'age' => $request->age,
            'blood_group_id' => $request->blood_group_id,
            'marital_status_id' => $request->marital_status_id,
            'height_id' => $request->height_id,
            'weight_id' => $request->weight_id,
            'occupation' => $request->occupation,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'husband_name' => $request->husband_name,
            'wife_name' => $request->wife_name,
            'number_of_children' => $request->number_of_children,

            'father_occupation' => $request->father_occupation,
            'father_income_status' => $request->father_income_status,
            'old_bts_id' => $request->old_bts_id,
            'number_of_siblings' => $request->number_of_siblings,
            'siblings_status' => $request->siblings_status,


            'emergency_contact_number' => $request->emergency_contact_number,
            'electrophoresis_report' => $request->electrophoresis_report
                ? (new ImageUpload())->fileUpload(
                    file: $request->electrophoresis_report,
                    data: 'ABC',
                    folder: 'electrophoresis-report',
                    fileName: 'electrophoresis_file' . $id
                )
                : $patientInfo->electrophoresis_report,
        ]);


        $present_address = $request->present_address;
        if ($present_address) {

            $patientInfo->addresses()->updateOrCreate(
                [
                    'type' => 'Present',
                    'addressable_type' => 'App\Models\PatientInfo'
                ],

                [
                    "country_id" => $present_address['country_id'] ?? null,
                    "city_id" => $present_address['city_id'] ?? null,
                    "post_code" => $present_address['post_code'] ?? null,
                    "address" => $present_address['address'] ?? null,
                ]
            );
        }

        $permanent_address = $request->permanent_address;
        if ($permanent_address) {
            $patientInfo->addresses()->updateOrCreate(
                [
                    'type' => 'Permanent',
                    'addressable_type' => 'App\Models\PatientInfo'
                ],
                [
                    "country_id" => $permanent_address['country_id'] ?? null,
                    "city_id" => $permanent_address['city_id'] ?? null,
                    "post_code" => $permanent_address['post_code'] ?? null,
                    "address" => $permanent_address['address'] ?? null,
                ]
            );
        }
    }
}
