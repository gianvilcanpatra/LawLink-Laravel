<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use App\Models\Reviews;
use App\Models\Appointments;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LawsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get lawyer's appointment, patients and display on dashboard
        $lawyer = Auth::user();
        $appointments = Appointments::where('law_id', $lawyer->id)->where('status', 'upcoming')->get();
        $reviews = Reviews::where('law_id', $lawyer->id)->where('status', 'active')->get();
        $totalHistoryAppointments = Appointments::where('law_id', $lawyer->id)
            ->whereIn('status', ['cancel', 'complete'])
            ->count();

        //return all data to dashboard
        return view('dashboard')->with(['lawyer' => $lawyer, 'appointments' => $appointments, 'reviews' => $reviews, 'totalHistoryAppointments' => $totalHistoryAppointments]);
    }

    public function getLawyerDetails($law_id)
    {
        $lawyer = Lawyer::where('law_id', $law_id)->first();

        if (!$lawyer) {
            return response()->json(['error' => 'Lawyer not found'], 404);
        }

        // Get average rating
        $averageRating = Reviews::where('law_id', $law_id)->avg('ratings');
        $reviews = Reviews::where('law_id', $law_id)->where('status', 'active')->get();

        return response()->json([
            'lawyer' => $lawyer,
            'averageRating' => $averageRating,
            'reviews' => $reviews,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //this controller is to store booking details post from mobile app
        $reviews = new Reviews();
        //this is to update the appointment status from "upcoming" to "complete"
        $appointment = Appointments::where('id', $request->get('appointment_id'))->first();

        //save the ratings and reviews from user
        $reviews->user_id = Auth::user()->id;
        $reviews->law_id = $request->get('lawyer_id');
        $reviews->ratings = $request->get('ratings');
        $reviews->reviews = $request->get('reviews');
        $reviews->reviewed_by = Auth::user()->name;
        $reviews->status = 'active';
        $reviews->save();

        //change appointment status
        $appointment->status = 'complete';
        $appointment->save();

        return response()->json([
            'success' => 'The appointment has been completed and reviewed successfully!',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
