<?php

namespace App\Http\Controllers;
use Sentinel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests\ConferenceRequest;

use App\Models\Conference;
use App\Models\User;

class ConferenceController extends Controller
{
    /**
     * Retrieve all Conferences of a certain status.
     *
     * @return Collection|Response
     */
    public function indexWithStatus(Request $request, $status)
    {
        try {
            return Conference::where('status', $status)->get();
        } catch (Exception $e) {
            return response()->error();
        }
    }

    /**
     * Retrieve all Events of a Conference of a certain status.
     *
     * @return Collection|Response
     */
    public function eventsWithStatus(Request $request, $cid, $status)
    {
        try {

            // Check if the Conference exists.
            $conference = Conference::find($cid);
            if (!$conference) {
                return response()->error(404, 'Conference Not Found');
            }

            // Retrieve its Events.
            return $conference->events()->where('status', $status)->get();
        } catch (Exception $e) {
            return response()->error();
        }
    }

    /**
     * Retrieve all Conferences.
     *
     * @return Collection|Response
     */
    public function index(ConferenceRequest $request)
    {
        try {
            return Conference::all();
        } catch (Exception $e) {
            return response()->error();
        }
    }

    /**
     * Create a Conference.
     *
     * @return Response
     */
    public function store(ConferenceRequest $request)
    {
        try {

            // Create the Conference.
            $conference = Conference::create($request->all());

            // Check if the API token in the request matches the API token in the database.
            $apiToken = $request->header('X-Auth-Token');
            $user = User::where('api_token', $apiToken)->first();

            // Assign the User as a Conference Manager.
            $user->conferences()->attach($conference->id);

            //Add Activity to log
            $this->addActivity($request->header('ID'),'request', $conference->id, 'conference');

            return response()->success();
        } catch (Exception $e) {
            return response()->error();
        }
    }

    /**
     * Retrieve a Conference.
     *
     * @return Model|Response
     */
    public function show(Request $request, $id)
    {
        try {

            // Check if the Conference exists.
            $conference = Conference::find($id);
            if (!$conference) {
                return response()->error(404);
            }

            // Retrieve the Conference.
            return $conference;
        } catch (Exception $e) {
            return response()->error();
        }
    }

    /**
     * Update a Conference.
     *
     * @return Response
     */
    public function update(ConferenceRequest $request, $id)
    {
        try {

            // Check if the Conference exists.
            $conference = Conference::find($id);
            if (!$conference) {
                return response()->error(404, 'Conference Not Found');
            }

            if (!$this->isConferenceManager($request, $id)) {
                return response()->error(403, 'You are not a manager of this conference.');
            }

            if (($request->status == 'approved' || $request->status == 'denied')) {
                $conference->update($request->all());
                $this->addActivity($request->header('ID'),$request->status, $id, 'conference');
                $this->sendCreationEmail('conference', $id, $request->status);
            } elseif(($request->status != 'approved' && $request->status != 'denied')) {
                $conference->fill($request->all())->save();
                $this->addActivity($request->header('ID'),'update', $id, 'conference');
            } else {
                return response()->error(403);
            }

            $conference->fill($request->all())->save();


            return response()->success();
        } catch (Exception $e) {
            return response()->error();
        }
    }

    /**
     * Delete a Conference.
     *
     * @return Response
     */
    public function destroy(ConferenceRequest $request, $id)
    {
        try {

            // Check if the Conference exists.
            $conference = Conference::find($id);
            if (!$conference) {
                return response()->error(404);
            }

            if (!$this->isConferenceManager($request, $id)) {
                return response()->error(403, 'You are not a manager of this conference.');
            }

            // Delete the Conference.
            $conference->delete();

            //Add Activity to log
            $this->addActivity($request->header('ID'),'delete', $id, 'conference');

            return response()->success();
        } catch (Exception $e) {
            return response()->error();
        }
    }
}
