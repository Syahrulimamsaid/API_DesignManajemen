<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Job;
use App\Helpers\Helper;
use App\Models\TimeLines;
use Illuminate\Http\Request;
use App\Models\JobAssignment;
use App\Models\QualityControl;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TimeLinesResource;
use App\Http\Resources\JobGetDataResource;
use App\Http\Resources\JobScheduleResource;
use App\Http\Resources\JobAssignmentResource;
use App\Http\Requests\StoreJobAssignmentRequest;
use App\Http\Requests\UpdateJobAssignmentRequest;

class JobAssignmentController extends Controller
{
    public function getDataJobFinalling()
    {
        if (Auth::user()->role == 2) {
            $jobsAssignment = JobAssignment::whereHas('job', function ($query) {
                $query->where('status', 6);
            })->with('job', 'user:kode,nama')->get();

            return JobAssignmentResource::collection($jobsAssignment);
        } else {
            return Response(['message' => 'access denied']);
        }
    }

    public function getDataDash()
    {
        $jobAssignment = JobAssignment::all();
        $job = Job::all();
        // dd($job);
        $role_id = Auth::user()->role;
        if ($role_id == 1) {
            return response()->json(['data' =>
            [
                'job_assignment' =>  $jobAssignment->count(),
                'job_completed' => $job->where('tanggapan_customer', 1)->count(),
                'job_in_progress' => $jobAssignment->filter(function ($jobAssignment) {
                    return $jobAssignment->job->status != 5;
                })->count()
            ]]);
        } else if ($role_id == 2) {
            return response()->json(['data' =>
            [
                'job_handled' =>  $job->count(),
                'job_completed' => $job->where('tanggapan_customer', 1)->count(),
                'job_in_progress' => $jobAssignment->filter(function ($jobAssignment) {
                    return $jobAssignment->job->status != 5;
                })->count()

            ]]);
        } else if ($role_id == 3) {
            return response()->json(['data' =>
            [
                'job_accepted' =>  QualityControl::where('petugas_kode', '=', Auth::user()->kode)->count(),
                'job_completed' => $job->where('tanggapan_customer', 1)->count(),
                'job_in_progress' => $jobAssignment->filter(function ($jobAssignment) {
                    return $jobAssignment->job->status != 5;
                })->count()
            ]]);
        } else if ($role_id == 4) {
            return response()->json(['data' =>
            [
                'job_accepted' =>  $jobAssignment->where('designer_kode', Auth::user()->kode)
                    ->where('status', 1)->count(),
                'job_completed' => JobAssignment::where('designer_kode', Auth::user()->kode)->with('job')->get()->filter(function ($jobAssignment) {
                    return $jobAssignment->job->status == 5;
                })
                    ->count(),
                'job_in_progress' => JobAssignment::where('designer_kode', Auth::user()->kode)->with('job')->get()->filter(function ($jobAssignment) {
                    return $jobAssignment->job->status != 5;
                })
                    ->count(),
            ]]);
        } else {
            return response()->json(['message' => 'data nod found'], 404);
        }
    }

    public function index()
    {
        if (Auth::user()->role == 4) {
            $user_kode = Auth::user()->kode;
            $jobsAssignment = JobAssignment::where('designer_kode', $user_kode)->where('status', 0)->get();

            return JobAssignmentResource::collection($jobsAssignment->loadMissing('job'));
        } else {
            return Response(['message' => 'access denied']);
        }
    }

    public function update(Request $request, $kode)
    {
        $request->validate(['status' => 'required']);
        if (Auth::user()->role == 4) {

            $jobAssignment = JobAssignment::where('kode', $kode)->firstOrFail();
            $jobAssignment->update($request->all());

            $job = Job::where('kode', $jobAssignment->job_kode)->firstOrFail();
            $job->update(['status' => 3]);

            $timeLines = TimeLines::create([
                'event' => 'Mulai Pengerjaan',
                'tanggal_event' => now()->format('Y/m/d H:i:s'),
                'job_assignment_kode' => $kode
            ]);

            $timeLines = TimeLines::create([
                'event' => 'Waktu Pengerjaan',
                'tanggal_event' => now()->format('Y/m/d H:i:s'),
                'job_assignment_kode' => $kode,
                'mulai_pengerjaan' => now()->format('Y/m/d H:i:s'),
            ]);

            return Response($jobAssignment);
        } else {
            return Response(['message' => 'access denied']);
        }
    }

    public function getAllJob()
    {
        if (Auth::user()->role == 4) {

            $user_kode = Auth::user()->kode;
            $jobsAssignment = JobAssignment::where('designer_kode', $user_kode)->where('status',1)->get();
            $qcArray = [];

            foreach ($jobsAssignment as $item) {
                switch ($item->job->status) {
                    case 5:
                        $qc = QualityControl::where('job_assignment_kode', $item->kode)->orderBy('created_at', 'desc')->first();

                        $qcArray[$item->kode] = $qc;
                        $item->qc = $qc;
                        break;
                    default:
                        break;
                }
            }

            return JobAssignmentResource::collection($jobsAssignment->loadMissing('job'));
        } else {
            return Response(['message' => 'access denied']);
        }
    }

    public function jobRevisionScheduled(Request $request, $kode)
    {
        $request->validate(['tanggal_pengumpulan' => 'required']);
        if (Auth::user()->role == 1) {
            try {
                $jobAssignment = JobAssignment::where('kode', $kode)->firstOrFail();
                $jobAssignment->update($request->all());

                $job = Job::where('kode', $jobAssignment->job_kode)->firstOrFail();
                $job->update(['status' => 5]);

                $timeLines = TimeLines::where('job_assignment_kode', $jobAssignment->kode)->where('event', 'Tidak Lolos QC')->count();

                switch ($timeLines) {
                    case 0:
                        $timeLines = TimeLines::create([
                            'event' => 'Penjadwalan Revisi 1',
                            'tanggal_event' => now()->format('Y/m/d H:i:s'),
                            'job_assignment_kode' => $jobAssignment->kode
                        ]);
                        break;
                    default:
                        $countRevision = TimeLines::where('job_assignment_kode', $jobAssignment->kode)->whereIn('event', ['Tidak Lolos QC', 'Tidak Lolos Koordinator','Pekerjaan ditolak Customer'])->count();

                        $timeLines = TimeLines::create([
                            'event' => "Penjadwalan Revisi $countRevision", 
                            'tanggal_event' => now()->format('Y/m/d H:i:s'),
                            'job_assignment_kode' => $jobAssignment->kode
                        ]);
                }

                $timeLines = TimeLines::create([
                    'event' => 'Waktu Pengerjaan',
                    'tanggal_event' => now()->format('Y/m/d H:i:s'),
                    'job_assignment_kode' => $kode,
                    'mulai_pengerjaan' => now()->format('Y/m/d H:i:s'),
                ]);

                return Response(['message' => 'data updated successfully']);
            } catch (Exception $e) {
                return Response(['message' => 'data updated error', 'error' => $e->getMessage()]);
            }
        } else {
            return Response(['message' => 'access denied']);
        }
    }

    public function jobSchedulling(Request $request)
    {
        $request->validate([
            'job_kode' => 'required',
            'designer_kode' => 'required',
            'tanggal_pengumpulan' => 'required'
        ]);
        if (Auth::user()->role == 1) {
            $request['kode'] = Helper::IDGenerator(new JobAssignment, 'JSM');
            $job = Job::where('kode', $request->job_kode)->firstOrFail();

            $jobAssignment = JobAssignment::create($request->all());

            $job->update(['status' => 2]);
            return Response($jobAssignment);
        } else {
            return Response(['message' => 'access denied']);
        }
    }


    public function getJob()
    {
        $jobAssignment = JobAssignment::all();
        return JobAssignmentResource::collection($jobAssignment->loadMissing('job', 'user'));
    }

    public function jobRejected()
    {
        if (Auth::user()->role == 1) {
            $jobsAssignment = JobAssignment::whereHas('job', function ($query) {
                $query->where('status', 6)->where('tanggapan_customer', 1);
            })->with('job')->get();

            return JobAssignmentResource::collection($jobsAssignment->loadMissing('user'));
        } else {
            return Response(['message' => 'access denied']);
        }
    }
}
