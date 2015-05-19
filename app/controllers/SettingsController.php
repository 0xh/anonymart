<?php

class SettingsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('settings.create',['settings'=>new Settings]);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		
		$inputs = Input::all();
		$rules = array_merge(Settings::make()->rules,[
			'password'=>'required|confirmed'
		]);

		$validator = Validator::make($inputs,$rules);

		if($validator->fails())
			return get_form_redirect('errors',$validator->messages()->all());

		Settings::set($inputs);

		$user = User::where('username','admin')->first();
		if($user) $user->delete();

		$user = new User;
		$user->is_admin = true;
		$user->username = 'admin';
		$user->password = $inputs['password'];
		$user->save();

		return Redirect::to('/');

	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit()
	{
		return View::make('settings.edit');
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update()
	{
		$inputs = Input::all();
		$rules = array_merge(Settings::make()->rules,[
			'password'=>'confirmed'
		]);

		$validator = Validator::make($inputs,$rules);

		if($validator->fails())
			return get_form_redirect('errors',$validator->messages()->all());

		Settings::set($inputs);

		return get_form_redirect('successes',['Settings updated']);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function getElectrum()
	{

		try{
			$mnemonic = get_electrum()->getMnemonic();
		}catch(Exception $e){
			return View::make('settings.electrum',[
				'mnemonic'=>''
			])->with('errors',['Electrum failed with the following message',$e->getMessage()]);
		}

		return View::make('settings.electrum',[
			'mnemonic'=>$mnemonic
		]);
	}

	public function postElectrum()
	{

		if(!Input::has('mnemonic'))
			return get_form_redirect('errors',['Mnemonic missing']);

		try{
			$electrum = get_electrum();
			$oldMnemonic = $electrum->getMnemonic();
			
			$electrum->removeWallet();
			$electrum->restore(Input::get('mnemonic'));
		}catch(Exception $e){
			@$electrum->restore($oldMnemonic);
			return get_form_redirect('errors',['Something went wrong',$e->getMessage()]);
		}
		return get_form_redirect('successes',['Wallet updated']);

	}


}
