<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\User;
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
            })->with('job', 'user:kode,nama')->orderBy('job_kode', 'desc')->get();
            // $jobsAssignmentSort = $jobsAssignment->sortBy('job.kode');
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
            $jobAssignment_kode = JobAssignment::pluck('job_kode')->toArray();

            $jobResult = 0;
            foreach ($job as $a) {
                if (in_array($a->kode, $jobAssignment_kode)) {
                    $jobResult += 1;
                }
            }

            return Response(['data' =>
            [
                'total_job' => $job->count(),
                'sudah_plotting' => $jobResult,
                'sudah_diambil' => $jobAssignment->where('status', 1)->count(),
                'belum_diambil' => $jobAssignment->where('status', 0)->count(),
            ]]);
        } else if ($role_id == 2) {
            return Response(['data' =>
            [
                'total_job' => $job->count(),
                'belum_dikerjakan' => $jobAssignment->where('status', 0)->count(),
                'sedang_dikerjakan' => $jobAssignment->where('status', 1)->count(),
                'rejec_customer' => $job->where('status', 6)->where('tanggapan_customer', 1)->count(),
                'acc_customer' => $job->where('status', 6)->where('tanggapan_customer', 2)->count(),
            ]]);
        } else if ($role_id == 3) {
            $qc = QualityControl::with('job_assignment.job.data_pendukung', 'job_assignment.user')
                ->orderBy('created_at', 'desc')
                ->get();

            $groupedQC = $qc->groupBy('job_assignment_kode');

            $latestQC = $groupedQC->map(function ($group) {
                return $group->first();
            });

            $filteredQC = $latestQC->filter(function ($item) {
                return $item->petugas->role == 3 && $item->status == 0 && $item->job_assignment->job->status == 0;
            })->count();

            $qc1 = QualityControl::with('job_assignment.job', 'job_assignment.user')
                ->orderBy('created_at', 'desc')->get();

            $groupedQC1 = $qc1->groupBy('job_assignment_kode');

            $latestQC1 = $groupedQC1->map(function ($group1) {
                return $group1->first();
            });
            $filteredQC1 = $latestQC1->filter(function ($item) {
                return $item->petugas->role == 3 && $item->status == 1 &&
                    (
                        (
                            $item->job_assignment->job->tanggapan_customer == null ||
                            $item->job_assignment->job->tanggapan_customer == 0
                        ) &&
                        (
                            $item->job_assignment->job->status == 0 ||
                            $item->job_assignment->job->status == 5
                        )
                    );
            })->count();

            // dd($filteredQC1->toArray());
            return Response(['data' =>
            [
                'belum_dicek' => JobAssignment::whereHas('job', function ($jobAssignment) {
                    $jobAssignment->where('status', 4);
                })->count(),
                'acc' => $filteredQC,
                'rejec' => $filteredQC1,

            ]]);
        } else if ($role_id == 4) {
            return Response(['data' =>
            [
                'total_job' =>   $jobAssignment->where('designer_kode', Auth::user()->kode)->count(),
                'belum_diambil' => $jobAssignment->where('designer_kode', Auth::user()->kode)
                    ->where('status', 0)->count(),
                'on_progress' => JobAssignment::where('designer_kode', Auth::user()->kode)->where('status', 1)->with('job')->get()->filter(function ($jobAssignment) {
                    return $jobAssignment->job->status == 3 || $jobAssignment->job->status == 5;
                })
                    ->count(),
                'selesai' => JobAssignment::where('designer_kode', Auth::user()->kode)->with('job')->get()->filter(function ($jobAssignment) {
                    return $jobAssignment->job->status == 6 || $jobAssignment->job->status == 4 || $jobAssignment->job->status == 0;
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

            if ($job->status  == 5) {
            } else {
                $job->update(['status' => 3]);

                $timeLines = TimeLines::create([
                    'event' => 'Mulai Pengerjaan',
                    'tanggal_event' => now()->format('Y/m/d H:i:s'),
                    'job_assignment_kode' => $kode
                ]);
            }

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
            $jobsAssignment = JobAssignment::where('designer_kode', $user_kode)->orderBy('job_kode', 'desc')->get();
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
            // dd($jobsAssignment->toArray());

            // $sortedJobsAssignment = $jobsAssignment->sortBy('job.kode');

            return JobAssignmentResource::collection($jobsAssignment);
        } else {
            return Response(['message' => 'access denied']);
        }
    }

    public function jobRevisionScheduled(Request $request, $kode)
    {
        $request->validate(['tanggal_pengumpulan' => 'required']);
        if (Auth::user()->role == 1) {
            try {
                $request['status'] = 0;
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
                        $countRevision = TimeLines::where('job_assignment_kode', $jobAssignment->kode)->whereIn('event', ['Tidak Lolos QC', 'Tidak Lolos Koordinator', 'Pekerjaan ditolak Customer'])->count();

                        $timeLines = TimeLines::create([
                            'event' => "Penjadwalan Revisi $countRevision",
                            'tanggal_event' => now()->format('Y/m/d H:i:s'),
                            'job_assignment_kode' => $jobAssignment->kode
                        ]);
                }

                // $timeLines = TimeLines::create([
                //     'event' => 'Waktu Pengerjaan',
                //     'tanggal_event' => now()->format('Y/m/d H:i:s'),
                //     'job_assignment_kode' => $kode,
                //     'mulai_pengerjaan' => now()->format('Y/m/d H:i:s'),
                // ]);

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
        $jobAssignment = JobAssignment::orderBy('job_kode', 'desc')->get();
        return JobAssignmentResource::collection($jobAssignment->loadMissing('job', 'user'));
    }

    public function jobRejected()
    {
        if (Auth::user()->role == 1) {
            $jobsAssignment = JobAssignment::whereHas('job', function ($query) {
                $query->where('status', 6)->where('tanggapan_customer', 1);
            })->with('job')->get();

            $jobSort = $jobsAssignment->sortByDesc('job.kode');
            return JobAssignmentResource::collection($jobSort->loadMissing('user'));
        } else {
            return Response(['message' => 'access denied']);
        }
    }

    public function jobOnProgres($kode)
    {
        $jobsAssignment = JobAssignment::where('designer_kode', $kode)->where('status', 1)->get();

        return JobAssignmentResource::collection($jobsAssignment->loadMissing('job', 'user'));
        // return Response(['data'=>$jobsAssignment]);
    }

    public function topDesigner()
    {
        $dateNow = date('n');
        // dd($dateNow);


        $jobsAssignment = JobAssignment::with('user')->whereMonth('created_at', $dateNow)->get()->groupBy('designer_kode');
        // dd($jobsAssigment);
        $result = [];
        foreach ($jobsAssignment as $item) {
            $result[] = [
                'designer_kode' => $item[0]['user']['kode'],
                'designer_nama' => $item[0]['user']['nama'],
                'total_job' => $item->count()

            ];
        }
        return Response($result);
        // return JobAssignmentResource::collection($jobsAssignment);

    }

    public function detailAssignment()
    {
        $resultByMonth = [
            'penjadwalan_revisi' => [],
            'pengecekan' => [],
            'penolakan' => []
        ];

        for ($i = 1; $i <= 12; $i++) {
            $penjadwalanRevisi = TimeLines::where('event', 'LIKE', '%Penjadwalan Revisi%')->whereMonth('tanggal_event', $i)->count();

            $pengecekan = TimeLines::whereIn('event', ['Lolos Koordinator', "Tidak Lolos Koordinator"])->whereMonth('tanggal_event', $i)->count();
            $penolakan = TimeLines::where('event', 'Tidak Lolos Koordinator')->whereMonth('tanggal_event', $i)->count();

            $resultByMonth['penjadwalan_revisi'][] = $penjadwalanRevisi;
            $resultByMonth['pengecekan'][] = $pengecekan;
            $resultByMonth['penolakan'][] = $penolakan;
        }
        return Response(['data' => $resultByMonth]);
    }
    public function detailJob()
    {
        $resultByMonth = [
            'selesai' => [],
            'rejec_customer' => [],
            'acc_customer' => []
        ];
        for ($i = 1; $i <= 12; $i++) {
            $finish = Job::where('status', 6)->whereIn('tanggapan_customer', [0, null])->whereMonth('created_at', $i)->count();
            $rejec_cus = Job::where('status', 6)->whereNotNull('tanggapan_customer')->where('tanggapan_customer', 1)->whereMonth('created_at', $i)->count();
            $acc_cus = Job::where('status', 6)->whereNotNull('tanggapan_customer')->where('tanggapan_customer', 2)->whereMonth('created_at', $i)->count();

            $resultByMonth['selesai'][] = $finish;
            $resultByMonth['rejec_customer'][] = $rejec_cus;
            $resultByMonth['acc_customer'][] = $acc_cus;
        }
        return Response(['data' => $resultByMonth]);
    }

    public function detailQC()
    {
        $resultByMonth = [
            'reject' => [],
            'acc' => [],
        ];
        for ($i = 1; $i <= 12; $i++) {
            $reject = QualityControl::where('petugas_kode', Auth::user()->kode)->where('status', 1)->whereMonth('created_at', $i)->count();
            $acc = QualityControl::where('petugas_kode', Auth::user()->kode)->where('status', 0)->whereMonth('created_at', $i)->count();

            $resultByMonth['reject'][] = $reject;
            $resultByMonth['acc'][] = $acc;
        }
        return Response(['data' => $resultByMonth]);
    }

    public function urgentDeadline()
    {
        $currentDate = Carbon::now();

        $dueDate = $currentDate->copy()->addDays(2)->toDateString();
        $dateNow = $currentDate->toDateString();
        // dd($dateNow);

        $jobsAssignment = JobAssignment::whereHas('job', function ($job) {
            $job->whereIn('status', [3, 5]);
        })->with('job')->where('designer_kode', Auth::user()->kode)
            ->where('tanggal_pengumpulan', '>', $dateNow)
            ->where('tanggal_pengumpulan', '<', $dueDate)->orderBy('job_kode')
            ->get();

        return Response(['data' => $jobsAssignment]);
    }


    public function detailDesigner($kode)
    {
        $designer = User::where('kode', $kode)->firstOrFail();
        $resultByMonth = [
            'rata_pengerjaan' => [],
            'revisi' => []
        ];

        for ($i = 1; $i <= 12; $i++) {
            $resultHari = TimeLines::whereHas('job_assignment', function ($jobAssignment) use ($designer, $i) {
                $jobAssignment->where('designer_kode', $designer->kode);
            })->where('event', 'Waktu Pengerjaan')->whereNotNull('mulai_pengerjaan')->whereNotNull('selesai_pengerjaan')
                ->whereMonth('tanggal_event', $i)->get();

            $jumlahData = $resultHari->count();
            $tambah = $resultHari->sum(function ($event) {
                $mulaiPengerjaan = Carbon::parse($event->mulai_pengerjaan);
                $selesaiPengerjaan = Carbon::parse($event->selesai_pengerjaan);
                return $selesaiPengerjaan->diffInDays($mulaiPengerjaan);
            });
            $rataPengerjaan = $jumlahData > 0 ? round($tambah / $jumlahData) : 0;

            $revisi = Timelines::whereHas('job_assignment', function ($jobAssignment) use ($kode) {
                $jobAssignment->where('designer_kode', $kode);
            })->whereIn('event', ['Tidak Lolos QC', 'Tidak Lolos Koordinator', 'Pekerjaan ditolak Customer'])->whereMonth('tanggal_event', $i)->count();

            $resultByMonth['rata_pengerjaan'][] = $rataPengerjaan;
            $resultByMonth['revisi'][] = $revisi;
        }

        return Response(['data' => $resultByMonth]);
    }
}
