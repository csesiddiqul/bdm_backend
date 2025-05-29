<?php

namespace App\Http\Controllers;

use App\Classes\ImageUpload;
use App\Enums\ApprovedStatusEnum;
use App\Enums\RolesEnum;
use App\Http\API\BaseController;
use App\Http\Requests\StoreDoctorProfileRequest;
use App\Http\Requests\UpdateDoctorProfileRequest;
use App\Http\Resources\Auth\UserProfileResource;
use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorProfileController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = User::with('doctorProfile')->has('doctorProfile')
                ->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhereHas('doctorProfile', function ($q) use ($request) {
                            $q->where('designation', 'like', '%' . $request->search . '%')
                                ->orWhere('specialization', 'like', '%' . $request->search . '%')
                                ->orWhere('department', 'like', '%' . $request->search . '%');
                        });
                });

            $query->when($request->id, function ($q) use ($request) {
                $q->where('id', $request->id);
            });

            $query->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            });

            $doctors = $query->orderBy('created_at', 'asc')
                ->paginate($request->per_page ?? 15);

            return UserProfileResource::collection($doctors);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('BlogNews Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('BlogNews Show failed.', $exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorProfileRequest $request, ImageUpload $imageUpload)
    {

        DB::beginTransaction();

        try {
            // 1. Create the User
            $user = User::create([
                'name' => $request->name,
                'phone' => normalizePhone($request->phone),
                'email' => $request->email,
                'status' => ApprovedStatusEnum::Approved->value,
                'password' => Hash::make($request->password),
            ]);

            if ($user) {
                $user->remember_token = random_int(100000, 999999);
                $user->token_expires_at = now()->addMinutes(5);

                // 2. Upload Profile Image if exists
                if ($request->profile_image) {
                    $user->profile_image = $imageUpload->fileUpload(
                        file: $request->profile_image,
                        data: $user,
                        folder: 'profile-images',
                        width: 558,
                        height: 575,
                        fileName: 'photo_' . $user->id
                    );
                }

                $user->save();
            }

            // 4. Assign Guest Role and Handle Additional Info
            if (RolesEnum::Guest->value) {
                $user->assignRole(RolesEnum::Guest->value);
            } else {
                DB::rollBack();
                return $this->sendError('Invalid role ID provided.', ['error' => RolesEnum::Guest->value], 400);
            }

            DoctorProfile::create([
                'user_id' => $user->id,
                'designation' => $request->designation,
                'description' => $request->description,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'department' => $request->department,
                'specialization' => $request->specialization,
                'experience_years' => $request->experience_years,
                'education' => $request->education,
                'chamber_address' => $request->chamber_address,
                'available_days' => $request->available_days,
                'available_time' => $request->available_time,
                'sorting_index' => $request->sorting_index,
                'status' => true,
            ]);

            DB::commit();

            $user->load('doctorProfile'); // eager load relation

            return $this->sendResponse(
                ['data' => new UserProfileResource($user)],
                'Doctor registered successfully.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Registration failed. Please try again.', ['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $doctor = User::with('doctorProfile')->find($id);
            if (!$doctor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor not found.'
                ], 404);
            } else {
                $doctor->load('doctorProfile');
                return (new UserProfileResource($doctor))->additional([
                    "success" => true,
                    "message" => __("Data show successfully.")
                ]);
            }
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Data Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Data Show failed.', $exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorProfileRequest $request, ImageUpload $imageUpload, $id)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);

            $user->name = $request->name;
            $user->phone = normalizePhone($request->phone);
            $user->email = $request->email;

            if ($request->password) {
                $user->password = Hash::make($request->password);
            }


            if ($request->profile_image) {
                $user->profile_image = $imageUpload->fileUpload(
                    file: $request->profile_image,
                    data: $user,
                    folder: 'profile-images',
                    width: 100,
                    height: 150,
                    fileName: 'photo_' . $user->id
                );
            }

            $user->save();

            $doctorProfileData = [
                'designation' => $request->designation,
                'description' => $request->description,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'department' => $request->department,
                'specialization' => $request->specialization,
                'experience_years' => $request->experience_years,
                'education' => $request->education,
                'chamber_address' => $request->chamber_address,
                'available_days' => $request->available_days,
                'available_time' => $request->available_time,
                'sorting_index' => $request->sorting_index,
                'status' => $request->status ?? true,
            ];

            if ($user->doctorProfile) {
                $user->doctorProfile->update($doctorProfileData);
            } else {
                $doctorProfileData['user_id'] = $user->id;
                DoctorProfile::create($doctorProfileData);
            }

            DB::commit();

            $user->load('doctorProfile');

            return $this->sendResponse(
                ['data' => new UserProfileResource($user)],
                'Doctor profile updated successfully.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Update failed. Please try again.', ['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::with('doctorProfile')->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        if (!$user->doctorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'This user does not have a doctor profile and cannot be deleted.'
            ], 403);
        }
        $doctorProfile = DoctorProfile::where('user_id', '=', $id)->firstOrFail();
        $doctorProfile->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Doctor user deleted successfully.'
        ]);
    }
}
