<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Job;
use App\Models\Post;
use App\Helpers\Helper;
use App\Models\TimeLines;
use Illuminate\Http\Request;
use App\Models\JobAssignment;
use Illuminate\Http\Response;
use App\Models\QualityControl;
use App\Http\Resources\JobResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\JobCheckResource;
use App\Http\Resources\TimeLinesResource;
use App\Http\Resources\JobGetDataResource;
use App\Http\Resources\JobAssignmentResource;
use App\Http\Resources\QualityControlResource;

class JobController extends Controller
{
    public function store(Request $request)
    {
        if (Auth::user()->role == 2) {
            $request->validate([
                'nama' => 'required',
                'perusahaan' => 'required',
            ]);

            try {
                $job = Job::where('nama', $request->nama)->first();
                if ($job) {
                    return response(['message' => 'Preparate sudah ada !!!'], 400);
                } else {
                    $request['kode'] = Helper::IDGenerator(new Job, 'JOB');
                    $request['status'] = 1;

                    $job = Job::create($request->all());

                    return response($job, 201);
                }
            } catch (Exception $e) {
                return response(['message' => 'Terjadi kesalahan saat menyimpan data.', 'error' => $e->getMessage()], 500);
            }
        } else {
            return response(['message' => 'access denied'], 403);
        }
    }

    public function index()
    {
        if (Auth::user()->role == 1) {
            $jobs = Job::all();
            $jobAssignment_kode = JobAssignment::pluck('job_kode')->toArray();

            $jobsAssignment = JobAssignment::where('status', 0)->get();

            $jobResult = [];
            foreach ($jobs as $job) {
                if (!in_array($job->kode, $jobAssignment_kode)) {
                    $jobResult[] = [
                        'id' => $job->id,
                        'kode' => $job->kode,
                        'nama' => $job->nama,
                        'perusahaan' => $job->perusahaan,
                        'tanggal_kirim' => date_format(date_create($job->tanggal_kirim), 'Y/m/d'),
                        'status_data' => $job->status_data,
                        'status' => $job->status
                    ];
                }
            }

            foreach ($jobsAssignment as $jobAssignment) {
                $jobResult[] = [
                    'id' => $jobAssignment->job->id,
                    'kode' => $jobAssignment->job->kode,
                    'nama' => $jobAssignment->job->nama,
                    'perusahaan' => $jobAssignment->job->perusahaan,
                    'tanggal_kirim' => date_format(date_create($jobAssignment->job->tanggal_kirim), 'Y/m/d'),
                    'status_data' => $jobAssignment->job->status_data,
                    'status' => $jobAssignment->job->status,
                    'tanggapan_customer' => $jobAssignment->job->tanggapan_customer,
                    'tanggal_pengumpulan' => date_format(date_create($jobAssignment->tanggal_pengumpulan), 'Y/m/d'),
                    'designer' => $jobAssignment->user->nama,
                ];
            }

            $jobCollection = collect($jobResult);

            $sortedJobs = $jobCollection->sortByDesc('kode')->values()->all();

            return response()->json(['data' => $sortedJobs]);
        } else {
            return response()->json(['message' => 'access denied']);
        }
    }

    public function design_result(Request $request, $kode)
    {
        if (Auth::user()->role == 4) {
            $job = Job::where('kode', $kode)->firstOrFail();
            // dd($request->file('hasil_design')->getClientMimeType());
            
            $request->validate(['hasil_design' => 'required|mimes:png,jpg,jpeg,ai,pdf,psd,cdr'], [
                'hasil_design.mimes' => 'Format file tidak sesuai.'
            ]);
            
            $cek = $request->file('hasil_design')->getClientMimeType(); 
            $design = null;
            if ($request->hasil_design) {
                $fileName = "design_{$job->nama}" . today()->format('ymd');
                $extension = $request->hasil_design->extension();
                if($cek == 'application/postscript'){
                    $extension= 'ai';
                }
                else if($cek == 'application/pdf')
                {
                    $extension = 'pdf';
                }
                $design = $fileName . '.' . $extension;

                Storage::putFileAs('design', $request->hasil_design, $design);

                $request['hasil_design'] = $design;
            }
            $job->fill([
                'hasil_design' => $design,
                'status' => 4,
                'tanggapan_customer' => 0
            ]);
            $job->save();

            $jobAssignment = JobAssignment::where('job_kode', $kode)->first();

            $timeLines = TimeLines::where('job_assignment_kode', $jobAssignment->kode)->where('event', 'Tidak Lolos QC')->count();

            switch ($timeLines) {
                case 0:
                    $timeLines = TimeLines::create([
                        'event' => 'Pengumpulan',
                        'tanggal_event' => now()->format('Y/m/d H:i:s'),
                        'job_assignment_kode' => $jobAssignment->kode
                    ]);
                    break;
                default:
                    $countRevision = TimeLines::where('job_assignment_kode', $jobAssignment->kode)->whereIn('event', ['Tidak Lolos QC', 'Tidak Lolos Koordinator', 'Pekerjaan ditolak Customer'])->count();

                    $timeLines = TimeLines::create([
                        'event' => "Pengumpulan Revisi $countRevision",
                        'tanggal_event' => now()->format('Y/m/d H:i:s'),
                        'job_assignment_kode' => $jobAssignment->kode
                    ]);
            }

            $getTimeLinesWaktu = TimeLines::where('job_assignment_kode', $jobAssignment->kode)->where('event', 'Waktu Pengerjaan')->whereNotNull('mulai_pengerjaan')->where('selesai_pengerjaan', null)->orderBy('tanggal_event', 'desc')->first();
            $getTimeLinesWaktu->update([
                'selesai_pengerjaan' =>
                now()->format('Y/m/d h:i:s')
            ]);


            return Response($job);
            // } catch (Exception $e) {
            //     return Response(['message' => 'data updated error', 'error' => $e->getMessage()]);
            // }
        } else {
            return Response(['message' => 'access denied']);
        }
    }

    public function update(Request $request, $kode)
    {
        if (Auth::user()->role == 2) {
            $request->validate([
                'nama' => 'required',
                'perusahaan' => 'required',
            ]);

            // try {
            $job = Job::where('kode', $kode)->firstOrFail();
            $job->update($request->all());

            return Response($job);
            // return response()->json(['message' => 'data updated successfully']);
            // } catch (Exception $e) {
            //     return response()->json(['message' => 'data updated error', 'error' => $e->getMessage()]);
            // }
        } else {
            return response()->json(['message' => 'access denied']);
        }
    }

    public function fileHasilDesign($nama)
    {


        if (Storage::disk('local')->exists("design/$nama")) {
            $data =  Storage::disk('local')->get("design/$nama");

            $mime = Storage::mimeType($data);
            $response = new Response($data, 200);
            $response->header('Content-Type', $mime);

            return $response;
        } else {
            return Response(['message' => 'data not found']);
        }
    }

    public function getJobPost()
    {
        if (Auth::user()->role == 2) {
            $job = Job::whereIn('status', [1, 2])->orderBy('kode', 'desc')->get();
            // $jobSort = $job->sortBy('kode');
            // $jobSort = $job->orderBy('kode','desc');
            return JobResource::collection($job->loadMissing('data_pendukung'));
        } else {
            return Response(['message' => 'access denied']);
        }
    }

    public function destroy($kode)
    {
        try {
            $job = Job::where('kode', $kode)->firstOrFail();
            $job->delete();
            return Response(['message' => 'data deleted successfully']);
        } catch (Exception $e) {
            return Response(['message' => 'data updated error', 'error' => $e->getMessage()]);
        }
    }

    public function jobResponse(Request $request, $kode)
    {
        if (Auth::user()->role == 2) {
            $request->validate(['tanggapan_customer' => 'required',]);

            $jobAssignment = JobAssignment::where('kode', $kode)->firstOrFail();

            $job = Job::where('kode', $jobAssignment->job_kode)->firstOrFail();
            // dd($job->toArray());

            if ($request->tanggapan_customer == 2) {
                $request['tanggal_diterima'] =  now()->format('Y/m/d H:i:s');

                $job->update($request->all());

                TimeLines::create([
                    'event' => 'Pekerjaan diterima Customer',
                    'tanggal_event' => now()->format('Y/m/d H:i:s'),
                    'job_assignment_kode' => $kode,
                ]);
            } else if ($request->tanggapan_customer == 1) {
                $request->validate(['tanggapan_customer' => 'required', 'komentar' => 'required']);

                $job->update(['tanggapan_customer' => $request->tanggapan_customer]);

                $qcKode = Helper::IDGenerator(new QualityControl, 'QCL');

                QualityControl::create([
                    'kode' => $qcKode, 'status' => 1,
                    'petugas_kode' => Auth::user()->kode,
                    'komentar' => $request->komentar,
                    'job_assignment_kode' => $kode
                ]);

                TimeLines::create([
                    'event' => 'Pekerjaan ditolak Customer',
                    'tanggal_event' => now()->format('Y/m/d H:i:s'),
                    'job_assignment_kode' => $kode,
                    'quality_control_kode' => $qcKode,
                ]);
            }
            return Response($job);
        } else {
            return Response(['message' => 'access denied']);
        }
    }

    public function commentCustomer()
    {
        $jobsAssignment = JobAssignment::whereHas('job', function ($job) {
            $job->where('status', 6)->whereNotNull('tanggapan_customer')->where('tanggapan_customer', 1);
        })->with('job')->get();

        $qcArray = [];

        foreach ($jobsAssignment as $item) {
            switch ($item->job->tanggapan_customer) {
                case 1:
                    $qc = QualityControl::where('job_assignment_kode', $item->kode)->orderBy('created_at', 'desc')->first();

                    $qcArray[$item->kode] = $qc;
                    $item->qc = $qc;
                    break;
                default:
                    break;
            }
        }
        $sortedJobsAssignment = $jobsAssignment->sortBy('qc.created_at');

        return Response(['data' => $sortedJobsAssignment]);
    }
}
