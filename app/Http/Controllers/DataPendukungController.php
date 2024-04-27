<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Job;
use Illuminate\Http\Request;
use App\Models\DataPendukung;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreDataPendukungRequest;
use App\Http\Requests\UpdateDataPendukungRequest;

class DataPendukungController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate(['job_kode' => 'required', 'nama' => 'required|file']);
        // try {
        $job = Job::where('kode', $request->job_kode)->firstOrFail();

        $fileName = $request->nama->getClientOriginalName();
        Storage::putFileAs('data', $request->nama, $fileName);

        $data_pendukung = new DataPendukung;
        $data_pendukung['job_kode'] = $request->job_kode;
        $data_pendukung['nama'] = $fileName;
        $data_pendukung->save();

        // return response()->json(['message' => 'data insert successfully']);
        return Response($data_pendukung);
        // } catch (Exception $e) {
        //     return response()->json(['message' => 'data insert error', 'error' => $e->getMessage()], 500);
        // }
    }

    public function show($nama)
    {
        // $request->validate(['nama' => 'required']);

        if (Storage::disk('local')->exists("data/$nama")) {
            $data =  Storage::disk('local')->get("data/$nama");

            $mime = Storage::mimeType($data);
            $response = new Response($data, 200);
            $response->header('Content-Type', $mime);

            return $response;
        } else {
            return Response(['message' => 'data not found']);
        }
    }

    public function destroy($id)
    {
        $data = DataPendukung::FindOrFail($id);
        $data->delete();
        return Response($data);
    }
}
