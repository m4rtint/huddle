<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests\ProfileRequest;

use App\Models\Profile;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Retrieve all Profiles of a User.
     *
     * @return Collection|Response
     */
    public function index(ProfileRequest $request, $uid)
    {
        try {
            return Profile::where('user_id', $uid)->get();
        } catch (Exception $e) {
            return response()->error();
        }
    }

    /**
     * Create a Profile for a User.
     *
     * @return Response
     */
    public function store(ProfileRequest $request, $uid)
    {
        try {

            // Check if the User exists.
            $user = User::find($uid);
            if (!$user) {
                return response()->error(404, 'User Not Found');
            }

            // Create the Profile.
            $profile = new Profile($request->all());
            $profile->user()->associate($user);
            $profile->save();

            return response()->success();
        } catch (Exception $e) {
            return response()->error();
        }
    }

    /**
     * Update a Profile of a User.
     *
     * @return Response
     */
    public function update(ProfileRequest $request, $uid, $pid)
    {
        try {
            $profile = Profile::find($pid);
            if(!$profile) {
                return response()->error();
            }
            $profile->update($request->all());
            return response()->success();
        } catch (Exception $e) {
            return response()->error();
        }
    }

    /**
     * Delete a Profile of a User.
     *
     * @return Response
     */
    public function destroy(ProfileRequest $request, $uid, $pid)
    {
        try {
            $profile = Profile::find($pid);
            if (!$profile) {
                return response()->error("Profile not found");
            }
            if ($profile->is_owner == 1) {
                return response()->error();
            }
            $profile->delete();
            return response()->success();
        } catch (Exception $e) {
            return response()->error();
        }
    }

    /**
     * Retrieve all Conferences a Profile attends.
     *
     * @return Collection|Response
     */
    public function conferences(ProfileRequest $request, $id)
    {
        try {

            $profile = Profile::find($id);
            if (!$profile) {
                return response()->success("204","Profile not found");
            }
            return $profile->conferences;

        } catch (Exception $e) {
            return response()->error();
        }
    }

    /**
     * Retrieve all Events a Profile attends.
     *
     * @return Collection|Response
     */
    public function events(ProfileRequest $request, $id)
    {
        try {
            $profile = Profile::find($id);
            if (!$profile) {
                return response()->success("204","Profile not found");
            }
            return $profile->events;
        } catch (Exception $e) {
            return response()->error();
        }
    }

    public function rooms($pid) {
        return \DB::table('profiles')
        ->where('profiles.id', $pid)
        ->join('profile_attends_conferences', 'profile_attends_conferences.profile_id', '=', 'profiles.id')
        ->join('conferences','conferences.id','=','profile_attends_conferences.conference_id')
        ->join('conference_accommodations','conferences.id','=','conference_accommodations.conference_id')
        ->join('rooms', 'conference_accommodations.accommodation_id','=','rooms.accommodation_id')
        ->join('profile_stays_in_rooms','profile_stays_in_rooms.room_id', '=', 'rooms.id')
        ->join('accommodations','conference_accommodations.accommodation_id','=','accommodations.id')
        ->get(['room_no', 'profiles.id', 'conferences.id', 'accommodations.name']);
    }

    public function conferenceVehicles($pid) {
        return \DB::table('profiles')
        ->where('profiles.id', $pid)
        ->join('profile_attends_conferences', 'profile_attends_conferences.profile_id', '=', 'profiles.id')
        ->join('conferences','conferences.id','=','profile_attends_conferences.conference_id')
        ->join('conference_vehicles','conferences.id','=','conference_vehicles.conference_id')
        ->join('vehicles', 'conference_vehicles.vehicle_id','=','vehicles.id')
        ->join('profile_rides_vehicles','profile_rides_vehicles.vehicle_id', '=', 'vehicles.id')
        ->get(['vehicles.name','conferences.id', 'conference_vehicles.type']);
    }

    public function eventVehicles($pid) {
        return \DB::table('profiles')
        ->where('profiles.id', $pid)
        ->join('profile_attends_events', 'profile_attends_events.profile_id', '=', 'profiles.id')
        ->join('events','events.id','=','profile_attends_events.event_id')
        ->join('event_vehicles','events.id','=','event_vehicles.event_id')
        ->join('vehicles', 'event_vehicles.vehicle_id','=','vehicles.id')
        ->join('profile_rides_vehicles','profile_rides_vehicles.vehicle_id', '=', 'vehicles.id')
        ->get(['vehicles.name','events.id', 'event_vehicles.type']);
    }
}
