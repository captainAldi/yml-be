<?php

namespace App\Http\Controllers;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Http\Request;



class HeartbeatController extends Controller
{

    public function getMonitors() {
        try {

            // baca file existing
            $data = Yaml::parseFile(env('HEARTBEAT_YML_PATH'));

            return response()->json([
                'message' => 'Data Berhasil di Ambil !',
                'data'     => $data['heartbeat.monitors']
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Data Berhasil di Ambil !',
                'data'     => $e->getMessage()
            ], 500);
        }
            
    }
    
    public function addMonitor(Request $request) {

        // Pesan Jika Error
        $messages = [
            'type.required'       => 'Masukkan Type !',
            'id.required'         => 'Masukkan ID !',
            'name.required'       => 'Masukkan Nama !',
            'schedule.required'   => 'Masukkan Schedule !',
            'urls.required'       => 'Masukkan URL',
            'ipv4.required'       => 'Masukkan IP v4',
            'ipv6.required'       => 'Masukkan IP v6',
            'mode.required'       => 'Masukkan Mode'
        ];

        //Validasi Data
        $validasiData = $this->validate($request, [
            'type'       => 'required',
            'id'         => 'required',
            'name'       => 'required',
            'schedule'   => 'required',
            'urls'       => 'required',
            'ipv4_monitor'       => 'required',
            'ipv6_monitor'       => 'required',
            'mode'       => 'required',
        ], $messages);

        // Get Value
        $type_monitor = $request->input('type');
        $id_monitor = $request->input('id');
        $name_monitor = $request->input('name');
        $schedule_monitor = $request->input('schedule');
        $urls_monitor = $request->input('urls');
        $ipv4_monitor = (boolean)$request->input('ipv4_monitor');
        $ipv6_monitor = (boolean)$request->input('ipv6_monitor');
        $mode_monitor = $request->input('mode');

        try {

            // baca file existing
            $oldArray = Yaml::parseFile(env('HEARTBEAT_YML_PATH'));

            // Cek ID Input is Unique
            $cekID = array_search($id_monitor, array_column($oldArray['heartbeat.monitors'], 'id'));

            if($cekID !== false) {
                return response()->json([
                    'message' => 'ID Sudah Ada !',
                ], 406);
            }

            // Cek ID not come with spaces
            if (preg_match('/\s/', $id_monitor)) {
                return response()->json([
                    'message' => 'ID Tidak Boleh Ada Spasi !',
                ], 406);
            }

            // create new array

            // $newPushArray = [
            //     'type'      => 'http',
            //     'id'        => 'id-baru',
            //     'name'      => 'Coba tambah',
            //     'schedule'  => '@every 5s',
            //     'urls'      => [
            //         'http://coba.domain'
            //     ],
            //     'ipv4'      => true,
            //     'ipv6'      => true,
            //     'mode'      => 'any'
            // ];

            $newPushArray = [
                'type'      => $type_monitor,
                'id'        => $id_monitor,
                'name'      => $name_monitor,
                'schedule'  => $schedule_monitor,
                'urls'      => $urls_monitor,
                'ipv4'      => $ipv4_monitor,
                'ipv6'      => $ipv6_monitor,
                'mode'      => $mode_monitor
            ];

            // masukkan array baru ke daftar
            array_push($oldArray['heartbeat.monitors'], $newPushArray);

            // Tes tampilkan
            // echo '<pre>'; print_r($oldArray); echo '</pre>';

            // buat jadi yaml
            $yaml = Yaml::dump($oldArray, 2, 1);

            // masukkan ke file
            file_put_contents(env('HEARTBEAT_YML_PATH'), $yaml);


            // echo "berhasil tambah data";

            return response()->json([
                'message' => 'Data Berhasil di Tambahkan !',
                'data'     => $newPushArray
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 406);
        }

    }

    public function deleteMonitor($id)
    {

        // Get ID
        $id_monitor = $id;

        // Proses Delete
        try {
            
            // baca file existing
            $oldArray = Yaml::parseFile(env('HEARTBEAT_YML_PATH'));

            // Cek ID Input is Unique
            $cekID = array_search($id_monitor, array_column($oldArray['heartbeat.monitors'], 'id'));

            if($cekID === false) {
                return response()->json([
                    'messages' => 'ID Tidak Ada !',
                ], 404);
            }

            
            // Hapus
            array_splice($oldArray['heartbeat.monitors'], $cekID, 1);


            // buat jadi yaml
            $yaml = Yaml::dump($oldArray, 2, 1);

            // masukkan ke file
            file_put_contents(env('HEARTBEAT_YML_PATH'), $yaml);

            return response()->json([
                'message' => 'Data Berhasil di Hapus !',
            ], 200);
            


        } catch (\Exception $e) {
            return response()->json([
                'messages' => $e->getMessage(),
            ], 406);
        }

    }


}
