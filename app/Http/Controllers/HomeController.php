<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Youtube;

class HomeController extends Controller
{

    public function __construct() {}

    public function index(Request $request) {
		return view('welcome');
    }
	public function getVideos(Request $request)
	{
		if($request->has('q') && $request->get('q') != '') {
			$info = Youtube::getChannelByName($request->get('q'));
			$videos = null;
			if($info) {
				$param = ['part'=>'id,snippet','channelId'=>$info->id,'q'=>'','maxResults'=>10];
				if($request->has('n') && $request->get('n') != '') {
					$param['pageToken'] = $request->get('n');
				}
				if($request->has('p') && $request->get('p') != '') {
					$param['pageToken'] = $request->get('p');
				}
				$v = Youtube::searchAdvanced($param,true);
				return response()->json(['status' => 1,'videos' => $v['results'],'info'=>$v['info']]);
			}
		}
		
		return response()->json(['status' => 0]);
	}
}
