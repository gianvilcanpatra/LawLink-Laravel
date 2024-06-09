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
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $lawyers = User::where('type', 'lawyer')->get();
        $details = $user->user_details;
        $lawyerData = Lawyer::all();
        $date = now()->format('n/j/Y');

        $appointment = Appointments::where('user_id', $user->id)
            ->where('status', 'upcoming')
            ->where('date', $date)
            ->first();

        foreach ($lawyerData as $data) {
            foreach ($lawyers as $info) {
                if ($data['law_id'] == $info['id']) {
                    $data['lawyer_name'] = $info['name'];
                    $data['lawyer_profile'] = $info['profile_photo_url'];

                    if (isset($appointment) && $appointment['law_id'] == $info['id']) {
                        $data['appointments'] = $appointment;
                    }

                    // Fetch active reviews for the lawyer
                    $reviews = Reviews::where('law_id', $data['law_id'])->where('status', 'active')->get();
                    $data['reviews'] = $reviews;
                    $data['average_rating'] = $reviews->avg('ratings'); // Average rating
                    $data['total_reviews'] = $reviews->count(); // Total reviews
                }
            }
        }

        $user['lawyer'] = $lawyerData;
        $user['details'] = $details;

        return response()->json($user);
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
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully'], 200);
    }
    //UPLOAD PHOTO
    public function uploadPhoto(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Delete old profile photo if exists
        if ($user->profile_photo_path) {
            Storage::delete($user->profile_photo_path);
        }

        // Store new profile photo
        $path = $request->file('profile_photo')->store('profile_photos');
        $user->profile_photo_path = $path;
        $user->save();

        return response()->json(['message' => 'Profile photo updated successfully'], 200);
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
