<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffController extends Controller
{
    public $staff;

    public function __construct(Staff $staff)
    {
        $this->staff = $staff;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $staff_list = $this->staff->latest('id');

            if ($request->has('keyword') && $request->keyword != "") {
                $keyword = $request->keyword;
                $staff_list = $staff_list->where(function ($query) use ($keyword) {
                    $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $keyword . '%')
                          ->orWhere('email', 'like', '%' . $keyword . '%')
                          ->orWhere('phone', 'like', '%' . $keyword . '%');
                });
            }

            $staff_list = $staff_list->paginate(10);
            return response()->json([
                'data' => $staff_list->items(),
                'current_page' => $staff_list->currentPage(),
                'per_page' => $staff_list->perPage(),
                'links' => (string) $staff_list->links()->render()  // Render pagination links as HTML
            ]);
        }

        $staff_list = $this->staff->latest('id')->paginate(10); // Adjust the pagination limit as needed
        return view('admin.staff.index', compact('staff_list'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['countries'] = Country::orderBy('name', 'asc')->get();
        return view('admin.staff.add_staff', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->staff->InsertId($request);

        if ($data != 1) {
            return response()->json(['success' => false, 'message' => 'Record Addition Failed']);
        }
        return response()->json(['success' => true, 'message' => 'Record Added Successfully']);
    }

    public function uniquePhoneNumber(Request $request)
    {
        $staffId = $request->input('staff_id');

        $exists = Staff::where('phone', $request->phone)
        ->where('country_code', $request->country_code)
        ->when($staffId, function ($query, $staffId) {
            return $query->where('id', '!=', $staffId); // Ignore the current staff ID in case of update
        })
        ->exists();
    
        if ($exists) {
            return response()->json(false);
        } else {
            return response()->json(true);
        }
    }

    public function uniqueEmail(Request $request){
        $staffId = $request->input('staff_id');

        $exists = Staff::where('email', $request->email)->when($staffId, function ($query, $staffId) {
            return $query->where('id', '!=', $staffId); // Ignore the current staff ID in case of update
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
        $staff = $this->staff->find($id);
        if (!$staff) {
            return response()->json(['message' => 'Member not found'], 404);
        }
        return response()->json($staff);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['staff'] = $this->staff->find($id);
        $data['countries'] = Country::orderBy('name', 'asc')->get();
        return view('admin.staff.edit_staff', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $staff = $this->staff->find($id);
        $data = $this->staff->InsertId($request, $staff);

        if ($data != 1) {
            return response()->json(['success' => false, 'message' => 'Record Updated Failed.']);
        }
        return response()->json(['success' => true, 'message' => 'Record Updated Successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = $this->staff->find($id);
        if ($data) {
            if ($data->image != "") {
                File::delete(public_path('/admin-assets/assets/img/profile_img/staff/' . $data->image));
            }
            $data->delete();
            return redirect()->route('staff.index')->withSuccess('Record Delete Successfully');
        } else {
            return redirect()->route('staff.index')->with('error', 'Record Delete Failed');
        }
    }

    /************ Export staff members *************************/
    public function export_staff(){
        $staff_members = Staff::all(); // Fetch all users

        $headers = [
            'Full Name',
            'Email',
            'Country',
            'Phone Number',
            'Status',
            'Date',
        ];

        $response = new StreamedResponse(function () use ($staff_members, $headers) {
            $handle = fopen('php://output', 'w');

            // Write CSV headers
            fputcsv($handle, $headers);

            foreach ($staff_members as $member) {
                $formattedDate = $member->created_at->format('l, d F Y'); // Ensure correct date format
                $fullName = trim(($member->first_name ?? '') . ' ' . ($member->last_name ?? '')); // Fix concatenation
                $phoneNumber = ($member->country_code ? $member->country_code . ' ' : '') . ($member->phone ?? ''); // Ensure null-safe concatenation

                $row = [
                    $fullName,
                    $member->email ?? '',
                    $member->country ?? '',
                    $phoneNumber,
                    $member->status == 1 ? 'Active' : 'Inactive',
                    $formattedDate,
                ];

                fputcsv($handle, $row);
            }

            fclose($handle);
            flush(); // Ensure output buffer is flushed
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="Staff_members' . now()->format('Y-m-d_H-i-s') . '.csv"');

        return $response;
    }
}
