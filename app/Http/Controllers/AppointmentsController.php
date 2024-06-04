<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AppointmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //retrieve all appointments from the user
        $appointment = Appointments::where('user_id', Auth::user()->id)->get();
        $lawyer = User::where('type', 'lawyer')->get();

        //sorting appointment and lawyer details
        //and get all related appointment
        foreach ($appointment as $data) {
            foreach ($lawyer as $info) {
                $details = $info->lawyer;
                if ($data['law_id'] == $info['id']) {
                    $data['lawyer_name'] = $info['name'];
                    $data['lawyer_profile'] = $info['profile_photo_url']; //typo error found
                    $data['category'] = $details['category'];
                }
            }
        }

        return $appointment;
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
        // Cek apakah slot waktu yang diminta sudah dipesan
        $existingAppointment = Appointments::where('law_id', $request->get('lawyer_id'))
            ->where('date', $request->get('date'))
            ->where('time', $request->get('time'))
            ->first();

        if ($existingAppointment) {
            return response()->json([
                'error' => 'Slot already booked!'
            ], 400);
        }

        // Jika slot waktu tersedia, simpan janji baru
        $appointment = new Appointments();
        $appointment->user_id = Auth::user()->id;
        $appointment->law_id = $request->get('lawyer_id');
        $appointment->date = $request->get('date');
        $appointment->day = $request->get('day');
        $appointment->time = $request->get('time');
        $appointment->status = 'upcoming'; //new appointment will be saved as 'upcoming' by default
        $appointment->save();

        // Jika berhasil, kembalikan status kode 200
        return response()->json([
            'success' => 'New Appointment has been made successfully!',
        ], 200);
    }

    public function checkSlot(Request $request)
    {
        $existingAppointment = Appointments::where('law_id', $request->get('lawyer_id'))
            ->where('date', $request->get('date'))
            ->where('time', $request->get('time'))
            ->first();

        if ($existingAppointment) {
            return response()->json([
                'error' => 'Slot already booked!'
            ], 400);
        }

        return response()->json([
            'success' => 'Slot available!'
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
