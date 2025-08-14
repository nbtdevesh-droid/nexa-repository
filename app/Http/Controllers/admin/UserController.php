<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;


class UserController extends Controller
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users_list = $this->user->withTrashed()->latest('id');

            if ($request->has('keyword') && $request->keyword != "") {
                $keyword = $request->keyword;
                $users_list = $users_list->where(function ($query) use ($keyword) {
                    $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('phone', 'like', '%' . $keyword . '%');
                });
            }
            if ($request->has('filter') && $request->filter != "") {
                $filter = $request->filter;
            
                if ($filter == 'trashed') {
                    $users_list = $users_list->onlyTrashed();
                } elseif ($filter == 'all') {
                    $users_list = $users_list->withTrashed();
                }
                elseif ($filter == 'active') {
                    $users_list = $users_list->whereNull('deleted_at'); ;
                }
            }
            
            $users_list = $users_list->paginate(10);

            return response()->json([
                'data' => $users_list->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'image' => $user->image,
                        'phone' => $user->phone,
                        'status' => $user->status,
                        'deleted_by' => $user->delete_by ? $user->delete_by : '',
                        'deleted_at' => $user->deleted_at,
                        'country_code' => $user->country_code,
                        'country' => $user->country,
                    ];
                }),
                'current_page' => $users_list->currentPage(),
                'per_page' => $users_list->perPage(),
                'links' => (string) $users_list->links()->render()
            ]);
        }

        $users_list = $this->user->withTrashed()->orderBy('id', 'desc')->paginate(10);
        return view('admin.user.index', compact('users_list'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['countries'] = Country::orderBy('name', 'asc')->get();
        return view('admin.user.add_user', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->user->InsertId($request);

        if ($data != 1) {
            return response()->json(['success' => false, 'message' => 'Record Addition Failed']);
        }
        return response()->json(['success' => true, 'message' => 'Record Added Successfully']);
    }

    public function uniquePhoneNumberUser(Request $request)
    {
        $userId = $request->input('user_id');

        $exists = User::where('phone', $request->phone)
        ->where('country_code', $request->country_code)
        ->when($userId, function ($query, $userId) {
            return $query->where('id', '!=', $userId); // Ignore the current staff ID in case of update
        })
        ->exists();
    
        if ($exists) {
            return response()->json(false);
        } else {
            return response()->json(true);
        }
    }

    public function uniqueEmailUser(Request $request){
        $userId = $request->input('user_id');

        $exists = User::where('email', $request->email)->when($userId, function ($query, $userId) {
            return $query->where('id', '!=', $userId); // Ignore the current staff ID in case of update
        })
        ->exists();
    
        if ($exists) {
            return response()->json(false);
        } else {
            return response()->json(true);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = $this->user->withTrashed()->where('id', $id)->with('shipping_address')->get();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['user'] = $this->user->withTrashed()->find($id);
        $data['countries'] = Country::orderBy('name', 'asc')->get();
        return view('admin.user.edit_user', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = $this->user->find($id);

        $data = $this->user->InsertId($request, $user);

        if ($data != 1) {
            return response()->json(['success' => false, 'message' => 'Record Updated Failed']);
        }
        return response()->json(['success' => true, 'message' => 'Record Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = $this->user->find($id);

        if ($data) {
            $data->delete_by = 'admin';
            // Delete all Sanctum access tokens of the user
            $data->tokens()->delete();

            $data->save();

            // Delete associated device token(s)
            DB::table('device_tokens')->where('user_id', $data->id)->delete();

            $data->delete();
            
            return redirect()->route('user.index')->withSuccess('Record Delete Successfully');
        } else {
            return redirect()->route('user.index')->with('error', 'Record Delete Failed');
        }
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->trashed()) {
            $user->restore();
            $user->delete_by = null;
            $user->save();
            return redirect()->route('user.index')->withSuccess('User restored successfully.');
        }

        return redirect()->route('user.index')->with('info', 'User is not deleted.');
    }
    
    public function export_user()
    {
        $users = User::all(); // Fetch all users

        $headers = [
            'Full Name',
            'Email',
            'Country',
            'Phone Number',
            'Status',
            'Profile Status', // New column for profile completion status
            'Date',
        ];

        $response = new StreamedResponse(function () use ($users, $headers) {
            $handle = fopen('php://output', 'w');

            // Write CSV headers
            fputcsv($handle, $headers);

            foreach ($users as $index => $user) {
                $formattedDate = $user->created_at->format('l, d F Y'); // Ensure correct date format
                $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')); // Fix concatenation
                $phoneNumber = ($user->country_code ? $user->country_code . ' ' : '') . ($user->phone ?? ''); // Ensure null-safe concatenation

                // Determine profile completion status
                $profileStatus = $user->current_steps == 'step_1' ? 'Incomplete Profile' : 'Complete Profile'; 

                $row = [
                    $fullName,
                    $user->email ?? '',
                    $user->country ?? '',
                    $phoneNumber,
                    $user->status == 1 ? 'Active' : 'Inactive',
                    $profileStatus, // Add profile status here
                    $formattedDate,
                ];

                fputcsv($handle, $row);
            }

            fclose($handle);
            flush(); // Ensure output buffer is flushed
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="Users_' . now()->format('Y-m-d_H-i-s') . '.csv"');

        return $response;
    }

}
