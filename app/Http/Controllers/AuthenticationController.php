<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use Carbon\Carbon;
use \App\Models\User;
use App\Helpers\Helper;
use PhpParser\Node\Expr;
use App\Models\TimeLines;
use Illuminate\Http\Request;
use App\Models\JobAssignment;
use App\Models\QualityControl;
use Illuminate\Routing\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // throw ValidationException::withMessages(['username' => ['The provided crudentials are incorrect']]);
            return Response(['message' => 'Username atau Password Salah !!!'], 401);
        } else {
            return [
                'id' => $user->id,
                'kode' => $user->kode,
                'nama' => $user->nama,
                'jenis_kelamin' => $user->jenis_kelamin,
                'username' => $user->username,
                'token' => $user->createToken($request->username)->plainTextToken,
                'role' => $user->role,
            ];
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return Response(['message' => 'user logout successfully']);
        } catch (Exception $e) {
            return Response(['message' => 'user logout error', 'error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $user = new User;
        $request->validate([
            'nama' => 'required',
            'jenis_kelamin' => 'required',
            'username' => 'required',
            'password' => 'required',
            'role' => 'required'
        ]);
        try {
            $user['kode'] = Helper::IDGenerator($user, 'USR');
            $user['nama'] = $request->nama;
            $user['jenis_kelamin'] = (int)$request->jenis_kelamin;
            $user['username'] = $request->username;
            $user['password'] = bcrypt($request->password);
            $user['role'] = (int)$request->role;
            $user->save();

            return response()->json(['message' => 'data insert successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => 'data insert error', 'error' => $e->getMessage()], 500);
        }
    }

    public function report()
    {
        $designers = User::where('role', 4)->get();
        $resultByMonth = [];
        $resultByMonthDetail = [];

        foreach ($designers as $user) {
            for ($i = 1; $i <= 12; $i++) {
                $revisi = QualityControl::whereHas('job_assignment', function ($jobAssignment) use ($user, $i) {
                    $jobAssignment->where('designer_kode', $user->kode);
                })->where('status', 1)->whereMonth('created_at', $i)->count();

                $selesai = JobAssignment::where('designer_kode', $user->kode)->whereHas('job', function ($job) use ($i) {
                    $job->where('status', 4);
                })->whereMonth('created_at', $i)->count();

                $belumSelesai = JobAssignment::where('designer_kode', $user->kode)->whereHas('job', function ($job) use ($i) {
                    $job->where('status', '!=', 6);
                })->where('status', 1)->whereMonth('created_at', $i)->count();

                $tidakDiambil = JobAssignment::where('designer_kode', $user->kode)->where('status', 0)->whereMonth('created_at', $i)->count();

                $acc = JobAssignment::where('designer_kode', $user->kode)->whereHas('job', function ($job) use ($i) {
                    $job->where('status', 6)->where('tanggapan_customer', 0)->orWhere('tanggapan_customer', null);
                })->where('status', 1)->whereMonth('created_at', $i)->count();

                $revisi = JobAssignment::where('designer_kode', $user->kode)->whereHas('job', function ($job) use ($i) {
                    $job->where('status', 5);
                })->where('status', 1)->whereMonth('created_at', $i)->count();


                $rejec = JobAssignment::where('designer_kode', $user->kode)
                    ->whereHas('job', function ($job) use ($i) {
                        $job->where('status', 5)
                            ->where(function ($query) {
                                $query->whereNull('tanggapan_customer')
                                    ->orWhere('tanggapan_customer', 0);
                            });
                    })
                    ->where('status', 1)
                    ->whereMonth('created_at', $i)
                    ->get();

                $countRejecQC = 0;
                $countRejecKoor = 0;

                foreach ($rejec as $item) {
                    $qc = QualityControl::where('job_assignment_kode', $item->kode)->orderBy('created_at', 'desc')->first();

                    if ($qc) {
                        switch ($qc->petugas->role) {
                            case 1:
                                $countRejecKoor += 1;
                                break;
                            case 3:
                                $countRejecQC += 1;
                                break;
                            default:
                                break;
                        }
                    }
                }

                $rejec_customer = JobAssignment::where('designer_kode', $user->kode)
                    ->whereHas('job', function ($job) use ($i) {
                        $job->where('status', 5)
                            ->whereNotNull('tanggapan_customer')
                            ->where('tanggapan_customer', 1);
                    })
                    ->where('status', 1)
                    ->whereMonth('created_at', $i)
                    ->count();

                $resultByMonthDetail[$i] = [
                    'reject_koor' => $countRejecKoor,
                    'reject_qc' => $countRejecQC,
                    'reject_customer' => $rejec_customer,
                ];

                $resultByMonth[$i][] = [
                    'designer' => $user->kode,
                    'designer_nama' => $user->nama,
                    'belum_diambil' => $tidakDiambil,
                    'selesai' => $selesai,
                    'on_progress' => $belumSelesai,
                    'acc' => $acc,
                    'revisi' => $revisi,
                    'reject_koor' => $countRejecKoor,
                    'reject_qc' => $countRejecQC,
                    'reject_customer' => $rejec_customer,
                ];
            }
        }

        return Response(['data' => $resultByMonth]);
    }

    public function detail($kode)
    {
        $resultByMonth = [];
       
        for ($i = 1; $i <= 12; $i++) {

            $rejec = JobAssignment::where('designer_kode', $kode)
                ->whereHas('job', function ($job) use ($i) {
                    $job->where('status', 5)
                        ->where(function ($query) {
                            $query->whereNull('tanggapan_customer')
                                ->orWhere('tanggapan_customer', 0);
                        });
                })
                ->where('status', 1)
                ->whereMonth('created_at', $i)
                ->get();

            $countRejecQC = 0;
            $countRejecKoor = 0;

            foreach ($rejec as $item) {
                $qc = QualityControl::where('job_assignment_kode', $item->kode)->orderBy('created_at', 'desc')->first();

                if ($qc) {
                    switch ($qc->petugas->role) {
                        case 1:
                            $countRejecKoor += 1;
                            break;
                        case 3:
                            $countRejecQC += 1;
                            break;
                        default:
                            break;
                    }
                }
            }

            $rejec_customer = JobAssignment::where('designer_kode', $kode)
                ->whereHas('job', function ($job) use ($i) {
                    $job->where('status', 5)
                        ->whereNotNull('tanggapan_customer')
                        ->where('tanggapan_customer', 1);
                })
                ->where('status', 1)
                ->whereMonth('created_at', $i)
                ->count();

            $resultByMonth[$i] = [
                'reject_koor' => $countRejecKoor,
                'reject_qc' => $countRejecQC,
                'reject_customer' => $rejec_customer,
            ];
        }

        return response()->json(['data' => $resultByMonth]);
    }

    public function reportDesigner($kode)
    {
        $designer = User::where('kode', $kode)->firstOrFail();
        $resultByMonth = [];


        for ($i = 1; $i <= 12; $i++) {
            $revisi = QualityControl::whereHas('job_assignment', function ($jobAssignment) use ($designer, $i) {
                $jobAssignment->where('designer_kode', $designer->kode);
            })->where('status', 1)->whereMonth('created_at', $i)->count();

            $selesai = JobAssignment::where('designer_kode', $designer->kode)->whereHas('job', function ($job) use ($i) {
                $job->where('status', 4);
            })->whereMonth('created_at', $i)->count();

            $belumSelesai = JobAssignment::where('designer_kode', $designer->kode)->whereHas('job', function ($job) use ($i) {
                $job->where('status', '!=', 6);
            })->whereMonth('created_at', $i)->count();

            $tidakDiambil = JobAssignment::where('designer_kode', $designer->kode)->where('status', 0)->whereMonth('created_at', $i)->count();

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

            $resultByMonth[$i][] = [
                'designer' => $designer->kode,
                'designer_nama' => $designer->nama,
                'revisi' => $revisi,
                'selesai' => $selesai,
                'belum_selesai' => $belumSelesai,
                'tidak_diambil' => $tidakDiambil,
                'rata_pengerjaan' => $rataPengerjaan
            ];
        }


        return Response(['data' => $resultByMonth]);
    }

    public function designer()
    {
        $designer = User::where('role', 4)->get();
        $designerSort = $designer->sortBy('kode');
        return Response($designerSort);
    }

    public function index()
    {

        $user = User::all();
        $userSort = $user->sortBy('kode');
        return Response(['data' => $user]);
    }

    public function destroy($kode)
    {
        $user = User::where('kode', $kode)->firstOrFail();
        $user->delete();
        return Response($user);
    }
}
