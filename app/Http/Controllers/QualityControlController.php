<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Job;
use App\Helpers\Helper;
use App\Models\TimeLines;
use App\Response\Response;
use Illuminate\Http\Request;
use App\Models\JobAssignment;
use App\Models\QualityControl;
use App\Http\Resources\JobResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\JobAssignmentResource;
use App\Http\Resources\QualityControlResource;
use App\Http\Requests\StoreQualityControlRequest;
use App\Http\Requests\UpdateQualityControlRequest;

class QualityControlController extends Controller
{

    public function index()
    {
        if (Auth::user()->role == 1) {
            try {

                $qc = QualityControl::with('job_assignment.job', 'job_assignment.user')
                    ->orderBy('created_at', 'desc')->get();

                $groupedQC = $qc->groupBy('job_assignment_kode');

                $latestQC = $groupedQC->map(function ($group) {
                    return $group->first();
                });

                $filteredQC = $latestQC->filter(function ($item) {
                    return $item->petugas->role == 3 && $item->status == 1 && $item->job_assignment->job->status == 0;
                });

                $filteredArray = $filteredQC->values()->all();

                return ['data' => $filteredArray];
            } catch (Exception $e) {
                return Response(['error' => $e->getMessage()]);
            }
        } else {
            return Response(['message' => 'access denied']);
        }
    }

    public function store(Request $request, $kode)
    {
        $role = Auth::user()->role;

        if ($role == 1) {
            try {

                switch ($request->status) {
                    case 1:
                        $request->validate([
                            'komentar' => 'required',
                            'status' => 'required'
                        ]);
                        break;
                    default:
                        $request->validate([
                            'status' => 'required'
                        ]);
                }

                $request['kode'] = Helper::IDGenerator(new QualityControl, 'QCL');
                ($request->status == 0) ? $request['komentar'] = '' : '';
                $request['petugas_kode'] = Auth::user()->kode;
                $request['job_assignment_kode'] = $kode;

                $jobAssignment = JobAssignment::where('kode', $kode)->first();
                $job = Job::where('kode', $jobAssignment->job_kode);

                switch ($request->status) {
                    case 1:
                        $job->update(['status' => 5]);
                        break;
                    case 0:
                        $job->update(['status' => 6, 'tanggapan_customer' => 0]);
                        $job->update(['status' => 6, 'tanggapan_customer' => 0]);
                        break;
                    default;
                }

                QualityControl::create($request->all());

                $timeLines = TimeLines::create([
                    'event' => ($request->status == 0) ?
                        'Lolos Koordinator' : 'Tidak Lolos Koordinator',
                    'tanggal_event' => now()->format('Y/m/d H:i:s'),
                    'job_assignment_kode' => $kode,
                    'quality_control_kode' => $request->kode
                ]);

                return Response(['message' => 'insert data successfully']);
            } catch (Exception $e) {
                return Response(['message' => 'data insert error', 'error' => $e->getMessage()], 500);
            }
        } else if ($role == 3) {
            // try {
            switch ($request->status) {
                case 1:
                    $request->validate([
                        'komentar' => 'required',
                        'status' => 'required'
                    ]);
                    break;
                default:
                    $request->validate([
                        'status' => 'required'
                    ]);
            }

            $request['kode'] = Helper::IDGenerator(new QualityControl, 'QCL');
            ($request->status == 0) ? $request['komentar'] = '' : '';
            $request['petugas_kode'] = Auth::user()->kode;
            $request['job_assignment_kode'] = $kode;

            $qc = QualityControl::create($request->all());

            $jobAssignment = JobAssignment::where('kode', $kode)->firstOrFail();
            $job = Job::where('kode', $jobAssignment->job_kode)->firstOrFail();

            $job->update(['status' => 0]);

            $timeLines = TimeLines::create([
                'event' => ($request->status == 0) ?
                    'Lolos QC' : 'Tidak Lolos QC',
                'tanggal_event' => now()->format('Y/m/d H:i:s'),
                'job_assignment_kode' => $kode,
                'quality_control_kode' => $request->kode
            ]);

            // return Response(['message' => 'insert data successfully']);
            return Response($qc);
            // } catch (Exception $e) {
            //     return Response(['message' => 'data insert error', 'error' => $e->getMessage()], 500);
            // }
        } else {
            return Response(['message' => 'access denied']);
        }
    }

    public function job_checked()
    {
        $role_id = Auth::user()->role;
        if ($role_id == 1) {
            try {
                $qc = QualityControl::with('job_assignment.job.data_pendukung', 'job_assignment.user')
                    ->orderBy('created_at', 'desc')
                    ->get();

                $groupedQC = $qc->groupBy('job_assignment_kode');

                $latestQC = $groupedQC->map(function ($group) {
                    return $group->first();
                });

                $filteredQC = $latestQC->filter(function ($item) {
                    return $item->petugas->role == 3 && $item->status == 0 && $item->job_assignment->job->status == 0;
                });

                $filteredQC->each(function ($item) {
                    $item->job_assignment->tanggal_pengumpulan = date_format(date_create($item->job_assignment->tanggal_pengumpulan), 'Y/m/d');
                    $item->job_assignment->job->tanggal_kirim = date_format(date_create($item->job_assignment->job->tanggal_kirim), 'Y/m/d');
                });

                $filteredArray = $filteredQC->values()->all();


                return ['data' => $filteredArray];
            } catch (Exception $e) {
                return Response(['error' => $e->getMessage()]);
            }
        } else if ($role_id == 3) {
            try {
                $jobsAssignment = JobAssignment::whereHas('job', function ($jobAssignment) {
                    $jobAssignment->where('status', 4);
                })->with('job:kode,id,nama,perusahaan,tanggal_kirim,catatan,hasil_design', 'user:kode,nama')->get();

                return JobAssignmentResource::collection($jobsAssignment);
            } catch (Exception $e) {
                return Response(['error' => $e->getMessage()]);
            }
        } else {
            return response()->json(["message" => "access denied"]);
        }
    }

    public function getKomentar($kode)
    {
        $qc = QualityControl::where('job_assignment_kode', $kode)->firstOrFail();
        dd($qc);
        $qc->with('job_assignment')->whereHas('petugas', function ($query) {
            $query->where('role', 3);
        })->orderBy('created_at', 'desc')->get();

        return ['data' => collect($qc)->unique('job_assignment_kode')->values()->all()];
    }
}
