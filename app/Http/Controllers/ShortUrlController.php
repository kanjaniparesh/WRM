<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\ShortUrl;
use Carbon\Carbon;
use Redirect;
use DB;

class ShortUrlController extends Controller
{
    //
    public function index()
    {
        $user_id = Auth::user()->id;
        $admin_id = config('myconfig.admin_id');
        if ($user_id == $admin_id) {
            $data['data'] = ShortUrl::orderBy('user_id', 'desc')->with('users')->paginate(10);

            return view('dashboard')->with($data);
        } else {
            $data['data'] = ShortUrl::where('user_id', $user_id)->orderBy('id', 'desc')->paginate(5);
            return view('home')->with($data);
        }
    }

    public function store(Request $request)
    {

        if (isset($request->tiny_id)) {
            $ShortUrl = ShortUrl::findOrFail($request->tiny_id);
            $code = $ShortUrl->code;
        } else {
            $ShortUrl = new ShortUrl;
            $code = $this->generateUniqueCode();
            $ShortUrl->code = $code;
        }
        $ShortUrl->link = urldecode($request->url);
        $ShortUrl->user_id = Auth::user()->id;
        if ($ShortUrl->save()) {
            return response()->json(['success' => '1', 'code' => $code]);
        }
    }

    public function show($code)
    {
        $data['data'] = ShortUrl::where("code", "=", $code)->first();
        $data['url'] = env('APP_URL');

        return view('layouts.success')->with($data);
    }
    public function generateUniqueCode()
    {
        do {
            $code = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        } while (ShortUrl::where("code", "=", $code)->first());

        return $code;
    }

    public function publiclyshow($code)
    {

        $date = Carbon::today()->subDays(7);
        $data = ShortUrl::where("code", "=", $code)->where("disable", "=", 'No')->where('created_at', '>=', $date)->get();
        if ($data->count() == 0) {
            return abort(404);
        } else {
            foreach ($data as $key => $value) {
                $url = $value->link;
            }
            $visit_count = ShortUrl::where("code", "=", $code)->increment('visit_count', 1);
            if ($ret = parse_url($url)) {
                if (!isset($ret["scheme"])) {
                    $url = "http://{$url}";
                }
            }
            return Redirect::away($url);
        }
    }
    public function destroy(Request $request)
    {

        $id = $request->id;
        $action = $request->action;
        $tiny = ShortUrl::find($id);

        if ($action == 'Delete') {
            if ($tiny->delete()) {
                return response()->json(['success' => '1']);
            }
        }
        if ($action == 'Disable') {
            $tiny->disable = 'Yes';
            $tiny->update();
            return response()->json(['success' => '1']);
        }
    }
    public function edit($id)
    {
        $data['data'] = ShortUrl::find($id);
        $data['url'] = env('APP_URL');

        return view('layouts.edit')->with($data);
    }
    public function piechart()
    {
        $tempData['name'] = 'Username';
        $tempData['colorByPoint'] = true;

        $data = ShortUrl::select('users.id', 'users.name', DB::raw('COUNT(short_urls.id) As y'))
            ->leftJoin('users', 'short_urls.user_id', '=', 'users.id')
            ->groupby('short_urls.user_id')
            ->orderby('y', 'desc')
            ->limit(5)
            ->get()->toArray();
        $count = 0;
        $temp_arr = array();
        foreach ($data as $key => $value) {
            array_push($temp_arr, $value['id']);
            $tempData['data'][$count]['name'] = $value['name'];
            $tempData['data'][$count]['y'] = $value['y'];
            $count++;
        }

        $data = ShortUrl::select(DB::raw('COUNT(short_urls.id) As y'))
            ->whereNotIn('user_id', $temp_arr)
            ->get()->toArray();
        foreach ($data as $key => $value) {
            $tempData['data'][$count]['name'] = "other";
            $tempData['data'][$count]['y'] = $value['y'];
        }
        $startDate = Carbon::today()->subDays(7);
        $data2 = ShortUrl::orderby('visit_count', 'desc')
            ->where('created_at', '>=', $startDate)
            ->limit(3)
            ->get()->toArray();

        return response()->json(['data' => $tempData, 'tableData' => $data2, 'APP_URL' => env('APP_URL')]);
    }
    public function sendEmailNotification()
    {
        $startDate = Carbon::today()->subDays(7);
        $endDate = Carbon::today()->subDays(6);
        $dt = Carbon::now();


        $data = ShortUrl::select('short_urls.*', 'users.name', 'users.email')
            ->leftJoin('users', 'short_urls.user_id', '=', 'users.id')
            ->where('expiry_email_sent', '=', 'No')
            ->whereBetween('short_urls.created_at', [$startDate, $endDate])->get();

        if ($data->count() > 0) {
            foreach ($data as $key => $value) {
                $details['name'] = "Dear " . $value->name . ",";
                $details['msg'] = "Your tiny url " . env('APP_URL') . "/s/" . $value->code . " will be expired on " . $dt->toFormattedDateString() . ".";
                ShortUrl::where('id', $value->id)->update(['expiry_email_sent' => 'Yes']);
            }
            dd("Email Sent.");
        } else {
            dd('No record found');
        }
    }
}
