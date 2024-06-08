<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\User;
use App\Models\Lawyer;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Reviews;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = array();
        $user = Auth::user();
        $lawyer = User::where('type', 'lawyer')->get();
        $details = $user->user_details;
        $lawyerData = Lawyer::all();
        $date = now()->format('n/j/Y');

        $appointment = Appointments::where('user_id', $user->id)
            ->where('status', 'upcoming')
            ->where('date', $date)
            ->first();

        foreach ($lawyerData as $data) {
            foreach ($lawyer as $info) {
                if ($data['law_id'] == $info['id']) {
                    $data['lawyer_name'] = $info['name'];
                    $data['lawyer_profile'] = $info['profile_photo_url'];

                    if (isset($appointment) && $appointment['law_id'] == $info['id']) {
                        $data['appointments'] = $appointment;
                    }

                    // Ambil rating dan ulasan dari tabel reviews
                    $reviews = Reviews::where('law_id', $data['law_id'])->where('status', 'active')->get();
                    $data['reviews'] = $reviews;
                    $data['average_rating'] = $reviews->avg('ratings'); // Rata-rata rating
                }
            }
        }

        $user['lawyer'] = $lawyerData;
        $user['details'] = $details;

        return $user;
    }

    public function getReviews($lawId)
    {
        $reviews = Reviews::where('law_id', $lawId)->get();

        if ($reviews->isEmpty()) {
            return response()->json(['message' => 'No reviews found'], 404);
        }

        return response()->json($reviews, 200);
    }




    /**
     * loign.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $reqeust)
    {
        //validate incoming inputs
        $reqeust->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        //check matching user
        $user = User::where('email', $reqeust->email)->first();

        //check password
        if (!$user || !Hash::check($reqeust->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect'],
            ]);
        }

        //then return generated token
        return $user->createToken($reqeust->email)->plainTextToken;
    }

    /**
     * register.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        //validate incoming inputs
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'type' => 'user',
            'password' => Hash::make($request->password),
        ]);

        $userInfo = UserDetails::create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        return $user;
    }

    /**
     * update favorite lawyer list
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFavDoc(Request $request)
    {

        $saveFav = UserDetails::where('user_id', Auth::user()->id)->first();

        $lawList = json_encode($request->get('favList'));

        //update fav list into database
        $saveFav->fav = $lawList;  //and remember update this as well
        $saveFav->save();

        return response()->json([
            'success' => 'The Favorite List is updated',
        ], 200);
    }

    /**
     * logout.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => 'Logout successfully!',
        ], 200);
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
        //
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
