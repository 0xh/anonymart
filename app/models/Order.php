<?php

class Order extends \Eloquent {
	protected $fillable = ['pgp_public','quantity'];

	public function product(){
		return $this->belongsTo('Product')->withTrashed();;
	}

	public function messages(){
		return $this->hasMany('Message');
	}

	public function check(){
		$blockchain = get_blockchain();
		$this->balance_btc=$blockchain->Explorer->getAddress($this->address)->final_balance;

		if(bccomp($this->balance_btc, $this->total_amount_btc,BC_SCALE)>=0)
			$this->markAsPaid();
		$this->save();
	}

	public function setBalanceBtcAttribute($value){
		$this->attributes['balance_btc']=bcmul('1', $value,BC_SCALE);
	}

	public function getStatusPrettyAttribute(){
		return ucwords($this->status);
	}

	public function getIsCancellableAttribute(){
		return $this->status==='unpaid'|| $this->status==='paid';
	}

	public function getIsExpiredAttribute(){
		if($this->status==='expired')
			return true;

		if($this->status==='unpaid')
			return $this->ttl_minutes===0;
	
		return false;
	}

	public function getTotalAmountBtcAttribute(){
		return $this->product_amount_btc;
	}

	public function getTtlMinutesAttribute(){
		$ttl_minutes = Settings::get('order_ttl_minutes') - $this->created_at->diffInMinutes();

		if($ttl_minutes>0)
			return $ttl_minutes;
		else
			return 0;
	}

	public function mark($status){
		switch($status){
			case 'shipped':
				$this->status('shipped');
				$this->save();
				break;
		}
	}

	public function markAsShipped(){
		$this->status = 'shipped';
		$this->save();

		$message = new Message;
		$message->sender = 'app';
		$message->template = 'shipped';
		$this->messages()->save($message);
	}

	public function markAsPaid(){
		$this->status = 'paid';
		$this->save();

		$message = new Message;
		$message->sender = 'app';
		$message->template = 'paid';
		$this->messages()->save($message);
	}

	public function markAsCancelled(){
		$this->status = 'cancelled';
		$this->save();

		$message = new Message;
		$message->sender = 'app';
		if(Auth::check())
			$message->template = 'cancelled_vendor';
		else
			$message->template = 'cancelled_buyer';
		$this->messages()->save($message);
	}

	public function getMessageUrlAttribute(){
		return URL::to("orders/{$this->id}/messages/create");
	}

	public static function getRules(){
		$rules = [
			'quantity'=>'integer|min:1'
			,'pgp_public'=>'required|pgp_public'
			,'text'=>'required|pgp_message'
			,'captcha'=>'required|captchaish'
		];
	
		return $rules;
	}

	public function getUrlAttribute(){
		if(Auth::guest())
			return $this->url_with_code;
		else
			return URL::to("/orders/{$this->id}");
	}

	public function getUrlWithCodeAttribute(){
		return URL::to("orders/{$this->id}?code={$this->code}");
	}

	public function getMarkCancelledUrlAttribute(){
		return URL::to("orders/{$this->id}/markCancelled");
	}

	public function getMarkShippedUrlAttribute(){
		return URL::to("orders/{$this->id}/markShipped");
	}

	public static function getCuttoffTimestamp(){
		$order_ttl_minutes = Settings::get('order_ttl_minutes');
		return (new DateTime("-{$order_ttl_minutes} minutes"))->format('Y-m-d H:i:s');
	}

	public static function checkUnpaidOrders(){

		$orders = Order::where('status','unpaid')
			->where('created_at','>',Order::getCuttoffTimestamp())
			->get();

		foreach($orders as $order)
			$order->check();
	}

	public static function expireUnpaidOrders(){

		$orders = Order::where('status','unpaid')
			->where('created_at','<=',Order::getCuttoffTimestamp())
			->update(["status" => "expired"]);
	}

}

Order::creating(function($order){
	$order->code = get_random_string(64);
});

Order::created(function($order){
	$order->address = get_address($order->id);
	$order->save();
});