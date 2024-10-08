<?php

namespace App\Http\Controllers;

use App\Mail\sendEmail;
use App\Mail\reschedule;
use App\Mail\sendStandardToLpm;
use App\Mail\sendStandardUpdateToLpm;
use App\Mail\deletedAuditPlan;
use App\Models\AuditPlan;
use App\Models\AuditPlanAuditor;
use App\Models\AuditPlanCategory;
use App\Models\AuditPlanCriteria;
use App\Models\AuditStatus;
use App\Models\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use App\Models\Location;
use App\Models\Observation;
use App\Models\StandardCategory;
use App\Models\StandardCriteria;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class AuditPlanController extends Controller
{
    //Tampilan Audit Plan
    public function index(Request $request)
    {
        $data = AuditPlan::all();
        $auditee = User::with(['roles' => function ($query) {
                $query->select('id', 'name');
            }])
            ->whereHas('roles', function ($q) use ($request) {
                $q->where('name', 'auditee');
            })
            ->orderBy('name')->get();
        return view('audit_plan.index', compact('data', 'auditee'));
    }

    //Tambah Audit Plan
    public function add(Request $request)
    {
        if ($request->isMethod('POST')) {
            $this->validate($request, [
                'type_audit'      => ['required'],
                'periode'         => ['required'],
                'date_start'      => ['required'],
                'date_end'        => ['required'],
                'department'      => ['required'],
                'auditee'         => ['required'],
                'head_major'      => ['required'],
                'upm_major'       => ['required'],
                'auditor_1'       => ['required'],
                'auditor_2'       => ['required'],
            ]);

            $data = AuditPlan::create([
                'type_audit'      => $request->type_audit,
                'periode'         => $request->periode,
                'date_start'      => $request->date_start,
                'date_end'        => $request->date_end,
                'department_id'   => $request->department,
                'auditee_id'      => $request->auditee,
                'head_major'      => $request->head_major,
                'upm_major'       => $request->upm_major,
                'audit_status_id' => '1',
                'location_id'     => null,
            ]);

            AuditPlanAuditor::create([
                'audit_plan_id' => $data->id,
                'auditor_id'    => $request->auditor_1,
            ]);

            AuditPlanAuditor::create([
                'audit_plan_id' => $data->id,
                'auditor_id'    => $request->auditor_2,
            ]);

            $auditee = User::find($request->auditee);

          
        // Send Email
        // if ($auditee) {
        //     $department = Department::find($request->department_id);
        //     $location = Location::find($request->location_id);
        //     // Assuming $request->auditor_id contains IDs of the auditors
        //     $auditorNames = [];
        //     foreach ($request->auditor_id as $auditorId) {
        //         $auditor = User::find($auditorId);
        //         if ($auditor) {
        //             $auditorNames[] = $auditor->name;
        //         }
        //     }
        //     // Data untuk email
        //     $emailData = [
        //         'auditee_id'    => $auditee->name,
        //         'auditor_id'    => implode(', ', $auditorNames), // Combine auditor names into a string
        //         'date_start'    => $request->date_start,
        //         'date_end'      => $request->date_end,
        //         'department_id' => $department ? $department->name : null,
        //         'location_id'   => $location ? $location->title : null,
        //         'link'          => $request->link,
        //         'type_audit'    => $request->type_audit,
        //         'periode'       => $request->periode,
        //         'subject'       => 'Notification Audit Mutu Internal']; // Add the subject here
        //     // Kirim email ke pengguna yang ditemukan
        //     Mail::to($auditee->email)->send(new sendEmail($emailData));
        //     // yang ke kirim cuma 
        //     $auditPlanId = $data->id;
        //     $auditors = AuditPlanAuditor::where('audit_plan_id', $auditPlanId)
        //         ->with('auditor')
        //         ->get();

        //     foreach ($auditors as $auditPlanAuditor) {
        //         $auditor = $auditPlanAuditor->auditor;

        //         if ($auditor && $auditor->email) {
        //             Mail::to($auditor->email)
        //                 ->send(new sendEmail($emailData));
        //         }
        //     }
        //     // Redirect dengan pesan sukses
            return redirect()->route('audit_plan.standard.create', ['id' => $data->id])
                    ->with('msg', 'Data ' . $auditee->name . ' on date ' . $request->date_start . ' until date ' . $request->date_end . ' successfully added and email sent!!');
        //     }
        }
        $audit_plan = AuditPlan::with('auditstatus')->get();
        $locations = Location::orderBy('title')->get();
        $departments = Department::orderBy('name')->get();
        $auditStatus = AuditStatus::orderBy('title')->get();
        $category = StandardCategory::where('status', true)->get();
        $criterias = StandardCriteria::where('status', true)->get();
        $auditee = User::whereHas('roles', function ($q) use ($request) {
                $q->where('name', 'auditee');
            })->orderBy('name')->get();
        $auditor = User::whereHas('roles', function ($q) use ($request) {
                $q->where('name', 'auditor');
            })->orderBy('name')->get();
        $data = AuditPlan::all();
        $prd = Carbon::now()->subYears(5)->year;
        return view("audit_plan.add", compact("data", "category", "criterias", "auditee", "auditor", "locations", "auditStatus", "departments", "audit_plan", 'prd'));
    }


    //Edit Audit Plan
    public function edit(Request $request, $id)
    {
        // Mendapatkan data audit plan berdasarkan id
        $data = AuditPlan::findOrFail($id);

        // Mendapatkan semua lokasi
        $locations = Location::orderBy('title')->get();

        // Mendapatkan auditor yang memiliki peran 'auditor'
        $auditors = User::with(['roles' => function ($query) {
            $query->select('id', 'name');
        }])
        ->whereHas('roles', function ($q) {
            $q->where('name', 'auditor');
        })
        ->orderBy('name')->get();

        // Mendapatkan semua auditor_id dari tabel audit_plan_auditor yang terkait dengan audit_plan_id
        $selectedAuditors = AuditPlanAuditor::where('audit_plan_id', $id)->pluck('auditor_id')->toArray();

        return view('audit_plan.edit_audit', compact('data', 'locations', 'auditors', 'selectedAuditors'));
    }

    //Proses Edit Audit Plan
    public function update(Request $request, $id)
    {
    $request->validate([
        'date_start' => 'required',
        'date_end' => 'required',
        'location_id' => 'required',
        'auditor_id' => 'array',
    ]);

    $data = AuditPlan::findOrFail($id);
    $updateData = [
        'date_start' => $request->date_start,
        'date_end' => $request->date_end,
        'location_id' => $request->location_id,
        'audit_status_id' => '2',
    ];

    $data->update($updateData);

    // Ambil semua auditor saat ini dari database untuk audit plan ini
    $currentAuditors = $data->auditor->pluck('auditor_id')->toArray();

    // Ambil auditor baru dari form
    $newAuditors = $request->auditor_id;

    // Hapus auditor yang tidak ada di form
    $auditorsToDelete = array_diff($currentAuditors, $newAuditors);
    AuditPlanAuditor::where('audit_plan_id', $id)
                    ->whereIn('auditor_id', $auditorsToDelete)
                    ->delete();

    // Tambahkan atau perbarui auditor baru
    foreach ($newAuditors as $auditor) {
        AuditPlanAuditor::updateOrCreate(
            ['audit_plan_id' => $id, 'auditor_id' => $auditor]
        );
    }

    // Kirim notifikasi email
    // $auditee = User::find($data->auditee_id);
    // if ($auditee) {
    //     $department = Department::find($data->department_id);
    //     $location = Location::find($data->location_id);

    //     // Assuming $request->auditor_id contains IDs of the auditors
    //     $auditorNames = [];
    //     foreach ($newAuditors as $auditorId) {
    //         $auditor = User::find($auditorId);
    //         if ($auditor) {
    //             $auditorNames[] = $auditor->name;
    //         }
    //     }
    //     // Data untuk email
    //     $emailData = [
    //         'auditee_id'    => $auditee->name,
    //         'auditor_id'    => implode(', ', $auditorNames), // Combine auditor names into a string
    //         'date_start'    => $request->date_start,
    //         'date_end'      => $request->date_end,
    //         'department_id' => $department ? $department->name : null,
    //         'location_id'   => $location ? $location->title : null,
    //         'subject'       => 'Reschedule Audit Plane' // Add the subject here
    //     ];

    //     // Kirim email ke auditee
    //     Mail::to($auditee->email)->send(new reschedule($emailData));
    //     // Kirim email ke auditor
    //     foreach ($newAuditors as $auditorId) {
    //         $auditor = User::find($auditorId);
    //         if ($auditor) {
    //             Mail::to($auditor->email)->send(new reschedule($emailData));
    //         }
    //     }
    // }
    return redirect()->route('audit_plan.index')->with('msg', 'Audit Plan updated successfully.');
}


    // Delete Audit Plan
    public function delete(Request $request)
{
    $data = AuditPlan::find($request->id);

    if ($data) {
        // Hapus entri terkait di AuditPlanAuditor
        $auditPlanAuditors = AuditPlanAuditor::where('audit_plan_id', $data->id)->get();

        foreach ($auditPlanAuditors as $auditPlanAuditor) {
            // Hapus Observasi yang terkait dengan AuditPlanAuditor
            Observation::where('audit_plan_auditor_id', $auditPlanAuditor->id)->delete();
        }

        // Hapus AuditPlanAuditor
        AuditPlanAuditor::where('audit_plan_id', $data->id)->delete();

        // Hapus Observasi yang terkait dengan AuditPlan
        Observation::where('audit_plan_id', $data->id)->delete();


        // Hapus AuditPlan itu sendiri
        $data->delete();

    // Email Pembatalan Auditing

        return response()->json([
            'success' => true,
            'message' => 'Berhasil dihapus!'
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Gagal dihapus! Data tidak ditemukan.'
        ]);
    }
}


    //Data Audit Plan
    public function data(Request $request)
    {
        $data = AuditPlan::with([
            'auditee' => function ($query) {
                $query->select('id', 'name', 'no_phone');
            },
            'auditstatus' => function ($query) {
                $query->select('id', 'title', 'color');
            },
            'auditorId' => function ($query) {
                $query->select('id', 'name', 'no_phone');
            },
            'category' => function ($query) {
                $query->select('id', 'description', 'status');
            },
            'criteria' => function ($query) {
                $query->select('id', 'title', 'status');
            },
            'departments' => function ($query) {
                $query->select('id', 'name');
            },
        ])
            ->leftJoin('locations', 'locations.id', '=', 'location_id')
            ->select(
                'audit_plans.id',
                'audit_plans.auditee_id',
                'audit_plans.date_start',
                'audit_plans.date_end',
                'audit_plans.audit_status_id',
                'audit_plans.location_id',
                'locations.title as location'
            )
            ->orderBy("audit_plans.id", "desc");
        return DataTables::of($data)
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('select_auditee'))) {
                    $instance->whereHas('auditee', function ($q) use ($request) {
                        $q->where('auditee_id', $request->get('select_auditee'));
                    });
                }
                if (!empty($request->get('search'))) {
                    $instance->where(function ($w) use ($request) {
                        $search = $request->get('search');
                        $w->orWhere('date_start', 'LIKE', "%$search%")
                        ->orWhere('date_end', 'LIKE', "%$search%")
                        ->orWhere('locations.title', 'LIKE', "%$search%")
                        ->orWhereHas('auditee', function ($query) use ($search) {
                            $query->where('name', 'LIKE', "%$search%");
                        });
                    });
                }
            })->make(true);
    }




    // ------------- CHOOSE STANDARD AUDITOR ---------------
    public function standard(Request $request, $id)
    {
        $data = AuditPlan::findOrFail($id);
        $auditor = AuditPlanAuditor::where('audit_plan_id', $id)->get();
        return view('audit_plan.standard.index', compact('data', 'auditor'));
    }

    public function create(Request $request, $id)
    {
        $data = AuditPlan::findOrFail($id);
        $auditors = AuditPlanAuditor::where('audit_plan_id', $id)->get(); // Ambil semua auditor
        $category = StandardCategory::where('status', true)->get();
        $criteria = StandardCriteria::where('status', true)->get();

        $selectedCategories = [];
        $selectedCriteria = [];

        // Ambil data kategori dan kriteria yang sudah dipilih untuk masing-masing auditor
        foreach ($auditors as $auditor) {
            $selectedCategories[$auditor->id] = AuditPlanCategory::where('audit_plan_auditor_id', $auditor->id)->pluck('standard_category_id')->toArray();
            $selectedCriteria[$auditor->id] = AuditPlanCriteria::where('audit_plan_auditor_id', $auditor->id)->pluck('standard_criteria_id')->toArray();
        }

        return view("audit_plan.standard.create", compact("data", "auditors", "category", "criteria", "selectedCategories", "selectedCriteria"));
    }


    public function create_auditor_std(Request $request, $id)
{
    $this->validate($request, [
        'auditor_id' => 'required|array',
        'standard_category_id' => 'required|array',
        'standard_criteria_id' => 'required|array',
    ]);

    // Clear existing categories and criteria for each auditor
    foreach ($request->auditor_id as $auditorId) {
        // Store categories
        if (isset($request->standard_category_id[$auditorId])) {
            foreach ($request->standard_category_id[$auditorId] as $categoryId) {
                AuditPlanCategory::updateOrCreate(
                    ['audit_plan_auditor_id' => $auditorId, 'standard_category_id' => $categoryId],
                    ['audit_plan_auditor_id' => $auditorId, 'standard_category_id' => $categoryId]
                );
            }
        }

        // Store criteria
        if (isset($request->standard_criteria_id[$auditorId])) {
            foreach ($request->standard_criteria_id[$auditorId] as $criteriaId) {
                AuditPlanCriteria::updateOrCreate(
                    ['audit_plan_auditor_id' => $auditorId, 'standard_criteria_id' => $criteriaId],
                    ['audit_plan_auditor_id' => $auditorId, 'standard_criteria_id' => $criteriaId]
                );
            }
        }

        // Send email notifications to LPM users
        // $lpm = User::whereHas('roles', function ($q) {
        //     $q->where('name', 'lpm');
        // })
        // ->orderBy('name')
        // ->get(['id', 'email', 'name']); // Mengambil hanya field yang diperlukan

        // foreach ($lpm as $user) {
        //     Mail::to($user->email)->send(new sendStandardToLpm($id));
        // }
    }
    // Redirect with success message
    return redirect()->route('audit_plan.index')
        ->with('msg', 'Auditor data to determine each Standard was added successfully!');
}

    public function edit_auditor_std(Request $request, $id)
    {
        $data = AuditPlan::findOrFail($id);
        $auditors = AuditPlanAuditor::where('audit_plan_id', $id)->get(); // Ambil semua auditor
        $category = StandardCategory::where('status', true)->get();
        $criteria = StandardCriteria::where('status', true)->get();

        $selectedCategories = [];
        $selectedCriteria = [];

        // Ambil data kategori dan kriteria yang sudah dipilih untuk masing-masing auditor
        foreach ($auditors as $auditor) {
            $selectedCategories[$auditor->id] = AuditPlanCategory::where('audit_plan_auditor_id', $auditor->id)->pluck('standard_category_id')->toArray();
            $selectedCriteria[$auditor->id] = AuditPlanCriteria::where('audit_plan_auditor_id', $auditor->id)->pluck('standard_criteria_id')->toArray();
        }
        return view("audit_plan.standard.edit", compact("data", "auditors", "category", "criteria", "selectedCategories", "selectedCriteria"));
    }

    public function update_auditor_std(Request $request, $id)
{
    // Validasi request
    $this->validate($request, [
        'auditor_id' => 'required|array',
        'auditor_id.*' => 'required|exists:users,id',
        'standard_category_id' => 'required|array',
        'standard_category_id.*' => 'required|array',
        'standard_criteria_id' => 'required|array',
        'standard_criteria_id.*' => 'required|array',
    ]);

    // Looping untuk tiap auditor
    foreach ($request->auditor_id as $auditorId) {
        // Ambil semua AuditPlanAuditor berdasarkan audit_plan_id dan auditor_id
        $auditPlanAuditors = AuditPlanAuditor::where('audit_plan_id', $id)
                                             ->where('auditor_id', $auditorId)
                                             ->get();

        // Loop melalui koleksi AuditPlanAuditor
        foreach ($auditPlanAuditors as $auditPlanAuditor) {
            // Update data auditor
            $auditPlanAuditor->update([
                'auditor_id' => $auditorId,
            ]);

            // Ambil kategori dan kriteria untuk auditor ini
            $categories = $request->standard_category_id[$auditorId] ?? [];
            $criteria = $request->standard_criteria_id[$auditorId] ?? [];

            // Update atau buat kategori standar
            foreach ($categories as $categoryId) {
                AuditPlanCategory::updateOrCreate(
                    [
                        'audit_plan_auditor_id' => $auditPlanAuditor->id,
                        'standard_category_id' => $categoryId
                    ]
                );
            }

            // Hapus kategori yang tidak dipilih lagi
            AuditPlanCategory::where('audit_plan_auditor_id', $auditPlanAuditor->id)
                ->whereNotIn('standard_category_id', $categories)
                ->delete();

            // Update atau buat kriteria standar
            foreach ($criteria as $criteriaId) {
                AuditPlanCriteria::updateOrCreate(
                    [
                        'audit_plan_auditor_id' => $auditPlanAuditor->id,
                        'standard_criteria_id' => $criteriaId
                    ]
                );
            }

            // Hapus kriteria yang tidak dipilih lagi
            AuditPlanCriteria::where('audit_plan_auditor_id', $auditPlanAuditor->id)
                ->whereNotIn('standard_criteria_id', $criteria)
                ->delete();
        }
    }

    // Update status audit plan
    $data = AuditPlan::findOrFail($id);
    $data->update([
        'audit_status_id' => '13',
    ]);

    // Kirim email notifikasi ke pengguna LPM
    $lpmUsers = User::whereHas('roles', function ($query) {
        $query->where('name', 'lpm');
    })->get();

    foreach ($lpmUsers as $user) {
        Mail::to($user->email)->send(new sendStandardUpdateToLpm($id));
    }

    return redirect()->route('audit_plan.index')->with('msg', 'Auditor data to determine each Standard was updated successfully!');
}

    public function getStandardCriteriaByCategoryIds(Request $request)
    {
        $categoryIds = $request->input('ids'); // Get all selected category IDs
        $criteria = StandardCriteria::whereIn('standard_category_id', $categoryIds)->get();

        return response()->json($criteria);
    }

    public function data_auditor(Request $request, $id)
    {
        $data = AuditPlanAuditor::where('audit_plan_id', $id)
        ->with([
            'auditor' => function ($query) {
                $query->select('id', 'name', 'no_phone');
            }
        ])->orderBy("id");

        // Gunakan DataTables untuk memfilter dan membuat respons JSON
        return DataTables::of($data)
            ->filter(function ($query) use ($request) {
                // Filter berdasarkan auditee jika dipilih
                if (!empty($request->get('select_auditee'))) {
                    $query->where('auditee_id', $request->get('select_auditee'));
                }

                // Pencarian umum
                if (!empty($request->get('search'))) {
                    $query->where(function ($w) use ($request) {
                        $search = $request->get('search');
                        $w->orWhere('date_start', 'LIKE', "%$search%")
                            ->orWhere('date_end', 'LIKE', "%$search%");
                    });
                }
            })
            ->make(true);
    }


    public function datatables()
    {
        $data = AuditPlan::select('*');
        return DataTables::of($data)->make(true);
    }
}
