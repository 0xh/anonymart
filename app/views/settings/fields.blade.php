<tr>
	<td>Site Name</td>
	<td>{{Form::text('site_name',null,['class'=>'form-control'])}}</td>
</tr>
<tr>
	<td>Currency</td>
	<td>{{Form::select('currency',get_currency_options(),null,['class'=>'form-control'])}}</td>
</tr>
<tr>
	<td>Order TTL (Minutes)
		<p class="explainer">How long should an order stay alive before it expires?</p>
	</td>
	<td>{{Form::text('order_ttl_minutes',null,['class'=>'form-control'])}}</td>
</tr>
<tr>
	<td>
		Site Info
		<p class="explainer">Information about your site and your product. This field will be interepreted as markdown.</p>
	</td>
	<td>{{Form::textarea('site_info',null,['class'=>'form-control'])}}</td>
</tr>
<tr>
	<td>PGP Public Key
	</td>
	<td>
		{{Form::textarea('pgp_public',null,['class'=>'form-control'])}}
	</td>
</tr>
<tr>
	<td>Master Public Key (MPK)
		<ul class="explainer">
			<li>Generate a fresh MPK to be used only for this {{{PROJECT_NAME}}}. Do not reuse MPKs.</li>
			<li>This MPK should <b>not</b> be used for any personal transactions</li>
			<li>Your MPK is a long string of characters that starts with <code>xpub</code></li>
			<li>If you are using Electrum 2.0+, create a new wallet then go to <code>Wallet > Master Public Keys</code></li>
		</ul>
	</td>
	<td>
		{{Form::text('mpk',null,['class'=>'form-control'])}}
	</td>
</tr>
<tr>
	<td>Password</td>
	<td>{{Form::password('password',['class'=>'form-control'])}}</td>
</tr>
<tr>
	<td>Password Confirmation</td>
	<td>{{Form::password('password_confirmation',['class'=>'form-control'])}}</td>
</tr>
<tr>
	<td>
		Enable Automatic Updates?
		<p class="explainer">Leave this checked and {{{PROJECT_NAME}}} will apply security updates and new features daily.</p>
	</td>
	<td>
		{{Form::checkbox('do_auto_update',"1",Settings::make()->isSet?null:true)}}
	</td>
</tr>
<tr>
	<td>Test Mode?
		<p class="explainer">If you are using this in production, leave this unchecked</p>
	</td>
	<td>
		{{Form::checkbox('is_testing')}}
	</td>
</tr>