<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\TimeLines;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TimeLinesResource;
use App\Http\Requests\StoreTimeLinesRequest;
use App\Http\Requests\UpdateTimeLinesRequest;
use App\Models\JobAssignment;
use App\Models\QualityControl;
use Exception;

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

    public function calendar()
    {
        $timelines = TimeLines::with('job_assignment.job', 'job_assignment.user')->get();

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
                $timelineData = [
                    'id' => $timeline->id,
                    'title' => $timeline->event,
                    'subtitle' => $timeline->job_assignment->job->nama,
                    'startDate' => ($timeline->event != 'Waktu Pengerjaan') ?  date_format(date_create($timeline->tanggal_event), 'Y-m-d') : date_format(date_create($timeline->mulai_pengerjaan), 'Y-m-d'),
                    'endDate' => ($timeline->event != 'Waktu Pengerjaan') ?   date_format(date_create($timeline->tanggal_event), 'Y-m-d') :  date_format(date_create($timeline->selesai_pengerjaan), 'Y-m-d'),
                    'description' => $this->description($timeline->event),
                    'bgColor' => $this->color($timeline->event),
                    'occupancy' => 3600
                    // 'job_assignment_kode' => $timeline->job_assignment_kode,
                    // 'quality_control_kode' => $timeline->quality_control_kode,
                    // 'mulai_pengerjaan' => $timeline->mulai_pengerjaan,
                    // 'selesai_pengerjaan' => $timeline->selesai_pengerjaan,
                    // 'job_assignment' => [
                    //     'id' => $timeline->job_assignment->id,
                    //     'kode' => $timeline->job_assignment->kode,
                    //     'job_kode' => $timeline->job_assignment->job_kode,
                    //     'designer_kode' => $timeline->job_assignment->designer_kode,
                    //     'tanggal_pengumpulan' => $timeline->job_assignment->tanggal_pengumpulan,
                    //     'status' => $timeline->job_assignment->status,
                    // ],
                ];

                // $timelineData['job'] = [
                //     'id' => $timeline->job_assignment->job->id,
                //     'kode' => $timeline->job_assignment->job->kode,
                //     'nama' => $timeline->job_assignment->job->nama,
                //     'perusahaan' => $timeline->job_assignment->job->perusahaan,
                // ];

                $userData['data'][] = $timelineData;
            }

            $responseData[] = $userData;
        }
        return Response($responseData);
    }

    public function description($text)
    {

        if (preg_match("/^Pengumpulan\sRevisi\w+$/", $text)) {
            return 'Revisi pekerjaan telah dikirim';
        } else if (preg_match("/^Penjadwalan\sRevisi\w+$/", $text)) {
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
        if (preg_match("/^Pengumpulan\sRevisi\w+$/", $text)) {
            return '#5fde35';
        } else if (preg_match("/^Penjadwalan\sRevisi\w+$/", $text)) {
            return '#8f35de';
        } else {
            switch ($text) {
                case 'Waktu Pengerjaan':
                    return '#0ee34e';
                    break;

                case 'Mulai Pengerjaan':
                    return '#6835de';
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
                    return '#e3a00e';
                    break;

                case 'Pekerjaan diterima Customer':
                    return '#e30edf';
                    break;

                case 'Pekerjaan ditolak Customer':
                    return '#f23b2e';
                    break;

                case 'Pengumpulan':
                    return '#f22e73';
                    break;

                default;
                    return '#a6a2a4';
            }
        }
    }
}
