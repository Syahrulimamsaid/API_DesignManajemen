<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Job;
use App\Models\User;
use App\Models\TimeLines;
use Illuminate\Http\Request;
use App\Models\JobAssignment;
use App\Models\QualityControl;
use App\Http\Controllers\Controller;
use App\Http\Resources\TimeLinesResource;
use App\Http\Requests\StoreTimeLinesRequest;
use App\Http\Requests\UpdateTimeLinesRequest;

class TimeLinesController extends Controller
{
    public function show($job_assignment_kode)
    {
        $jobsAssignment = JobAssignment::where('kode', $job_assignment_kode)->firstOrFail();

        $timeLines = TimeLines::with('job_assignment', 'quality_control')->where('job_assignment_kode', $job_assignment_kode)->whereNot('event', 'Waktu Pengerjaan')->orderBy('tanggal_event', 'asc')->get();

        return TimeLinesResource::collection($timeLines);
    }

    public function store(Request $request)
    {
        $request->validate(['event' => 'required', 'tanggal_event' => 'required', 'job_assignment_kode' => 'required']);

        try {
            $jobsAssignment = JobAssignment::where('kode', $request->job_assignment_kode)->firstOrFail();

            ($request->quality_control_kode) ? QualityControl::where('kode', $request->quality_control_kode)->firstOrFail() : '';

            $timeLines = TimeLines::create($request->all());
            return Response(['message' => 'data insert successfully']);
        } catch (Exception $e) {
            return Response(['message' => 'data insert error', 'error' => $e->getMessage()]);
        }
    }

    public function calendar($kode)
    {
        // $timelines = TimeLines::where('job_assignment_kode', $kode)->with('job_assignment.job', 'job_assignment.user')->get();

        $designer = User::where('kode', $kode)->firstOrFail();
        
        $timelines = TimeLines::whereHas('job_assignment', function ($query) use ($designer) {
            $query->where('designer_kode', $designer->kode);
        })->with('job_assignment.job', 'job_assignment.user')->get();

        // dd($timelines);

        $grouped = $timelines->groupBy('job_assignment.user.kode');

        $responseData = [];

        foreach ($grouped as $userKode => $userTimelines) {
            $userData = [
                'id' => $userKode,
                'label' => [
                    'icon' => '',
                    'title' => $userTimelines->first()->job_assignment->user->nama,
                    'subtitle' => $userTimelines->first()->job_assignment->user->role,
                ],
                'data' => [],
            ];

            foreach ($userTimelines as $timeline) {
                if ($timeline->event != 'Mulai Pengerjaan' && $timeline->event != 'Pengumpulan') {
                    $timelineData = [
                        'id' => $timeline->id,
                        'title' => $timeline->event,
                        'subtitle' => $timeline->job_assignment->job->nama,
                        'startDate' => ($timeline->event != 'Waktu Pengerjaan') ?  date_format(date_create($timeline->tanggal_event), 'Y-m-d') : date_format(date_create($timeline->mulai_pengerjaan), 'Y-m-d'),
                        'endDate' => ($timeline->event != 'Waktu Pengerjaan') ?   date_format(date_create($timeline->tanggal_event), 'Y-m-d') :  date_format(date_create($timeline->selesai_pengerjaan), 'Y-m-d'),
                        'description' => $this->description($timeline->event),
                        'bgColor' => $this->color($timeline->event),
                        'occupancy' => 3600
                    ];

                    $userData['data'][] = $timelineData;
                }
            }
            $responseData[] = $userData;
        }
        return Response($responseData);
    }

    public function description($text)
    {

        if (preg_match("/^Pengumpulan\sRevisi\s\d+$/", $text)) {
            return 'Revisi pekerjaan telah dikirim';
        } else if (preg_match("/^Penjadwalan\sRevisi\s\d+$/", $text)) {
            return 'Penjadwalan pekerjan revisi telah dilakukan';
        } else {
            switch ($text) {
                case 'Waktu Pengerjaan':
                    return 'Proses pengerjaan pekerjaan';
                    break;

                case 'Mulai Pengerjaan':
                    return 'Pengerjaan pekerjaan dimulai';
                    break;

                case 'Lolos QC':
                    return 'Pekerjaan telah lolos dari Quality Control';
                    break;

                case 'Tidak Lolos QC':
                    return 'Pekerjaan tidak lolos dari Quality Control';
                    break;

                case 'Lolos Koordinator':
                    return 'Pekerjaan telah lolos dari Koordinator';
                    break;

                case 'Tidak Lolos Koordinator':
                    return 'Pekerjaan tidak lolos dari Koordinator';
                    break;

                case 'Pekerjaan diterima Customer':
                    return 'Pekerjaan diterima oleh Customer';
                    break;

                case 'Pekerjaan ditolak Customer':
                    return 'Pekerjaan tidak diterima oleh Customer';
                    break;

                case 'Pengumpulan':
                    return 'Pekerjaan telah dikirim';
                    break;

                default;
                    return 'Tidak terdefinisi';
            }
        }
    }

    public function color($text)
    {
        if (preg_match("/^Pengumpulan\sRevisi\s\d+$/", $text)) {
            return '#f26c2e';
        } else if (preg_match("/^Penjadwalan\sRevisi\s\d+$/", $text)) {
            return '#f26c2e';
        } else {
            switch ($text) {
                case 'Waktu Pengerjaan':
                    return '#0ee34e';
                    break;

                case 'Mulai Pengerjaan':
                    return '#0ee34e';
                    break;

                case 'Lolos QC':
                    return '#0ed1e3';
                    break;

                case 'Tidak Lolos QC':
                    return '#bfe30e';
                    break;

                case 'Lolos Koordinator':
                    return '#0e55e3';
                    break;

                case 'Tidak Lolos Koordinator':
                    return '#7d400a';
                    break;

                case 'Pekerjaan diterima Customer':
                    return '#e30edf';
                    break;

                case 'Pekerjaan ditolak Customer':
                    return '#f23b2e';
                    break;

                case 'Pengumpulan':
                    return '#0ee34e';
                    break;

                default;
                    return '#a6a2a4';
            }
        }
    }
}
