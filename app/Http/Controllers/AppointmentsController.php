<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointments;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

    public function cancel(Request $request, $id)
    {
        // Temukan janji temu berdasarkan ID
        $appointment = Appointments::find($id);

        // Periksa apakah janji temu ditemukan dan milik user yang sedang login
        if (!$appointment || $appointment->user_id != Auth::user()->id) {
            return response()->json(['error' => 'Appointment not found or unauthorized'], 404);
        }

        // Ubah status janji temu menjadi "canceled"
        $appointment->status = 'cancel';
        $appointment->save();

        return response()->json(['success' => 'Appointment has been canceled'], 200);
    }

    public function reschedule(Request $request)
    {
        // Validate input
        $request->validate([
            'appointment_id' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required|string',
        ]);

        // Find the appointment
        $appointment = Appointments::find($request->get('appointment_id'));

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found!'], 404);
        }

        // Check if the new slot is available
        $existingAppointment = Appointments::where('law_id', $appointment->law_id)
            ->where('date', $request->get('date'))
            ->where('time', $request->get('time'))
            ->first();

        if ($existingAppointment) {
            return response()->json(['error' => 'Slot already booked!'], 400);
        }

        // Update the appointment with new date and time
        $appointment->date = $request->get('date');
        $appointment->time = $request->get('time');
        $appointment->save();

        return response()->json(['success' => 'Appointment rescheduled successfully!'], 200);
    }

    public function upcoming()
    {
        $user = Auth::user();
        $appointments = Appointments::where('law_id', $user->id)->where('status', 'upcoming')->get();

        return view('upcoming-appointments', compact('appointments'));
    }

    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointments::find($id);

        if (!$appointment) {
            return redirect()->back()->with('error', 'Appointment not found');
        }

        $appointment->status = $request->input('status');
        $appointment->save();

        return redirect()->back()->with('success', 'Appointment status updated successfully');
    }

    public function history()
    {
        $lawyer = Auth::user();
        $appointments = Appointments::where('law_id', $lawyer->id)
            ->whereIn('status', ['cancel', 'complete'])
            ->get();

        return view('history', compact('appointments'));
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
